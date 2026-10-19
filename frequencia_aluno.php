<?php
require_once 'db.php';

session_start();
if (!isset($_SESSION['id_aluno'])) {
    header('location: index.php');
    exit;
}

if (!isset($_GET['id_login']) || empty($_GET['id_login'])) {
    die("Erro: ID do aluno não fornecido.");
}

$alunoId = (int)$_GET['id_login'];

$alunoNome = 'Aluno Desconhecido';
$result = [];

try {
    // Busca o nome do aluno
    $stmt = $pdo->prepare("SELECT nome_aluno FROM aluno WHERE idaluno = ?");
    $stmt->execute([$alunoId]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aluno) {
        $alunoNome = htmlspecialchars($aluno['nome_aluno']);
    } else {
        die("Erro: Aluno não encontrado.");
    }

    // Busca os dias de presença do mês atual
    $mesAtual = date('m');
    $anoAtual = date('Y');
    $stmt = $pdo->prepare("SELECT data_presenca 
                           FROM frequencia 
                           WHERE id_aluno = ? 
                           AND MONTH(data_presenca) = ? 
                           AND YEAR(data_presenca) = ?");
    $stmt->execute([$alunoId, $mesAtual, $anoAtual]);
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}

// Cria array com os dias de presença (apenas para o mês atual)
$diasPresenca = [];
$presencaDates = [];
foreach ($result as $resultado) {
    $diasPresenca[] = date('d', strtotime($resultado['data_presenca']));
    $presencaDates[] = date('Y-m-d', strtotime($resultado['data_presenca']));
}

// Informações do mês atual
setlocale(LC_TIME, 'pt_BR.utf-8', 'pt_BR', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$nomeMes = ucfirst(strftime('%B', mktime(0, 0, 0, $mesAtual, 1, $anoAtual)));
$diasNoMes = cal_days_in_month(CAL_GREGORIAN, $mesAtual, $anoAtual);

// Estatísticas até hoje
$hojeDia = (int)date('j');
$presencasCount = count($presencaDates);
$faltasCount = max(0, $hojeDia - $presencasCount);

// Dados para os últimos 7 dias (para os círculos)
$ultimos7 = [];
for ($i = 6; $i >= 0; $i--) {
    $d = date('Y-m-d', strtotime("-{$i} days"));
    $dow = date('w', strtotime($d)); // 0 (domingo) - 6 (sábado)
    // Letras em PT: Domingo D, Segunda S, Terça T, Quarta Q, Quinta Q, Sexta S, Sábado S
    $letters = ['D', 'S', 'T', 'Q', 'Q', 'S', 'S'];
    $letter = $letters[$dow];
    $present = in_array($d, $presencaDates, true);
    $ultimos7[] = [
        'date' => $d,
        'label' => $letter,
        'present' => $present
    ];
}

// Mensagem de compartilhamento (URL-encoded para WhatsApp)
$shareText = "Eu, {$alunoNome}, tenho {$presencasCount} presenças em {$nomeMes}. Confira meu progresso no app!";
$shareTextEncoded = rawurlencode($shareText);
$whatsappLink = "https://wa.me/?text={$shareTextEncoded}";
$facebookShareUrl = "https://www.facebook.com/sharer/sharer.php?u=" . rawurlencode("https://seusite.com/frequencia?id_login={$alunoId}");
?>
<!doctype html>
<html lang="pt-BR">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width,initial-scale=1">
    <title>Frequência - <?php echo $alunoNome; ?></title>

    <!-- Estilos -->
    <style>
        :root {
            --primary: #007bff;
            --primary-dark: #0056b3;
            --bg: #f4f6f9;
            --card: #ffffff;
            --success: #28a745;
            --danger: #dc3545;
        }

        * {
            box-sizing: border-box
        }

        body {
            font-family: Inter, Arial, sans-serif;
            background: var(--bg);
            margin: 0;
            color: #222;
        }

        /* Navbar */
     
        /* Container */
        .container {
            max-width: 920px;
            margin: 22px auto;
            padding: 16px;
        }

        .card {
            background: var(--card);
            border-radius: 14px;
            padding: 18px;
            box-shadow: 0 6px 18px rgba(16, 24, 40, 0.06);
            margin-bottom: 18px;
        }

        .summary {
            display: grid;
            grid-template-columns: 1fr 220px;
            gap: 18px;
            align-items: center;
        }

        .summary-left h3 {
            margin: 0 0 6px 0;
            color: var(--primary);
            font-size: 20px;
        }

        .summary-left p {
            margin: 4px 0;
            color: #444;
        }

        .summary-right {
            text-align: center;
            background: linear-gradient(180deg, rgba(0, 123, 255, 0.06), rgba(0, 86, 179, 0.04));
            padding: 12px;
            border-radius: 10px;
        }

        .summary-right .big {
            font-size: 20px;
            font-weight: 700;
            color: var(--primary);
        }

        /* Dias circulares */
        .dias {
            display: flex;
            gap: 20px;
            justify-content: flex-start;
            margin-top: 14px;
            flex-wrap: wrap;
        }

        .dia {
            width: 46px;
            height: 46px;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: 700;
            color: #fff;
            box-shadow: 0 4px 10px rgba(2, 6, 23, 0.06);
            position: relative;
            font-size: 16px;
        }

        .dia.v {
            background: var(--success);
        }

        .dia.x {
            background: var(--danger);
        }

        .dia .small-date {
            position: absolute;
            bottom: -18px;
            left: 50%;
            transform: translateX(-50%);
            font-size: 11px;
            color: #666;
        }

        /* Gráfico e estatísticas */
        .grid-2 {
            display: grid;
            grid-template-columns: 1fr 320px;
            gap: 18px;
            align-items: center;
        }

        .chart-card {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 8px;
        }

        .stats {
            padding: 12px;
        }

        .stat-item {
            display: flex;
            justify-content: space-between;
            margin-bottom: 10px;
            font-weight: 600;
        }

        .share-buttons {
            display: flex;
            gap: 10px;
            margin-top: 12px;
            justify-content: flex-start;
        }

        .share-btn {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 10px 14px;
            border-radius: 999px;
            color: #fff;
            text-decoration: none;
            font-weight: 700;
            font-size: 14px;
            transition: transform .18s ease, opacity .18s ease;
        }

        .share-btn.whatsapp {
            background: #25D366;
        }

        .share-btn.facebook {
            background: #1877f2;
        }

        .share-btn.copy {
            background: #6c757d;
        }

        .share-btn:hover {
            transform: translateY(-3px);
            opacity: 0.95;
        }

        /* Responsividade */
        @media (max-width: 880px) {
            .summary {
                grid-template-columns: 1fr;
            }

            .grid-2 {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {

    .menu-toggle {
        display: flex;
    }

    #menu-links {
        position: absolute;
        top: 60px;
        right: 0;
        width: 100%;
        background: #007bff;
        flex-direction: column;

        max-height: 0;
        opacity: 0;
        overflow: hidden;

        transition: all .4s ease;
    }

    #menu-links.show {
        max-height: 500px;
        opacity: 1;
    }
}
    </style>
</head>

<body>
  <header>
        <?php include("views/header_aluno.php"); ?>
    </header>

    <div class="container">

        <!-- Card resumo treino -->
        <div class="card">
            <div class="summary">
                <div class="summary-left">
                    <h3>🎉 Parabéns, <?php echo $alunoNome; ?>!</h3>
                    <p>Você está acompanhando sua frequência em <strong><?php echo htmlspecialchars($nomeMes); ?></strong>.</p>
                    <p><strong>Presenças até hoje:</strong> <?php echo $presencasCount; ?> &nbsp; • &nbsp; <strong>Faltas:</strong> <?php echo $faltasCount; ?></p>

                    <!-- Dias da semana (últimos 7 dias) -->
                    <div class="dias" aria-hidden="true">
                        <?php foreach ($ultimos7 as $d):
                            $cls = $d['present'] ? 'v' : 'x';
                            $label = htmlspecialchars($d['label']);
                            $shortDate = date('d/m', strtotime($d['date']));
                        ?>
                            <div class="dia <?php echo $cls; ?>">
                                <?php echo $label; ?>
                                <div class="small-date"><?php echo $shortDate; ?></div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>

                <div class="summary-right">
                    <div style="font-size:12px;color:#666;margin-bottom:6px;">Resumo mensal</div>
                    <div class="big"><?php echo $presencasCount; ?> / <?php echo $hojeDia; ?></div>
                    <div style="font-size:12px;color:#666;margin-top:6px;">Presenças até hoje / Dias do mês</div>
                </div>
            </div>
        </div>

        <!-- Card gráfico e estatísticas -->
        <div class="card">
            <div class="grid-2">
                <div class="chart-card">
                    <canvas id="frequenciaChart" width="100%" height="300"></canvas>
                </div>

                <div class="stats">
                    <div class="stat-item"><span>Presenças</span><span style="color:var(--success)"><?php echo $presencasCount; ?></span></div>
                    <div class="stat-item"><span>Faltas</span><span style="color:var(--danger)"><?php echo $faltasCount; ?></span></div>
                    <div class="stat-item"><span>Percentual</span>
                        <span>
                            <?php
                            $totalConsiderado = max(1, $hojeDia);
                            $percent = round(($presencasCount / $totalConsiderado) * 100);
                            echo $percent . '%';
                            ?>
                        </span>
                    </div>

                    <div class="share-buttons">
                        <a class="share-btn whatsapp" href="<?php echo $whatsappLink; ?>" target="_blank" rel="noopener noreferrer">🟢 WhatsApp</a>
                        <a class="share-btn facebook" href="<?php echo $facebookShareUrl; ?>" target="_blank" rel="noopener noreferrer">🔵 Facebook</a>
                        <button class="share-btn copy" id="copyShare">📋 Copiar</button>
                    </div>
                </div>
            </div>
        </div>

    </div>

    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <script>
        // // Menu toggle (mobile)
        // function toggleMenu(toggle) {
        //     toggle.classList.toggle("open");
        //     var links = document.getElementById('menu-links');
        //     if (links.style.display === 'flex' || links.style.display === '') {
        //         links.style.display = 'none';
        //     } else {
        //         links.style.display = 'flex';
        //     }
        // }

        // Dados do PHP para o gráfico
        const presencas = <?php echo json_encode($presencasCount); ?>;
        const faltas = <?php echo json_encode($faltasCount); ?>;

        const ctx = document.getElementById('frequenciaChart').getContext('2d');
        const frequenciaChart = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: ['Presenças', 'Faltas'],
                datasets: [{
                    data: [presencas, faltas],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderColor: ['#ffffff', '#ffffff'],
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '60%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            boxWidth: 12,
                            padding: 12
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const label = context.label || '';
                                const value = context.parsed || 0;
                                const total = presencas + faltas;
                                const pct = total ? Math.round((value / total) * 100) : 0;
                                return label + ': ' + value + ' (' + pct + '%)';
                            }
                        }
                    }
                }
            }
        });

        // Copiar mensagem de compartilhamento
        document.getElementById('copyShare').addEventListener('click', function() {
            const text = "<?php echo addslashes($shareText); ?>";
            navigator.clipboard?.writeText(text).then(function() {
                alert('Mensagem copiada para a área de transferência!');
            }, function() {
                alert('Não foi possível copiar automaticamente. Selecione e copie manualmente: ' + text);
            });
        });
    </script>
</body>

</html>