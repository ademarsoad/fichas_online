<?php
require_once 'db.php';

session_start();
if (!isset($_SESSION['id_aluno'])) {
    header('location: index.php');

    exit;
}

// Verifica se o ID do aluno foi passado na URL
if (!isset($_GET['id_login']) || empty($_GET['id_login'])) {
    die("Erro: ID do aluno não fornecido.");
}

$alunoId = (int)$_GET['id_login'];
$alunoNome = 'Aluno Desconhecido';
$ficha = null;
$dataFicha = 'N/A';
$treino = [];

try {
    // 1. Busca o nome do aluno
    $stmt = $pdo->prepare("SELECT nome_aluno FROM aluno WHERE idaluno = ?");
    $stmt->execute([$alunoId]);
    $aluno = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($aluno) {
        $alunoNome = htmlspecialchars($aluno['nome_aluno']);
    } else {
        die("Erro: Aluno não encontrado.");
    }

    // 2. Busca a ficha de treino mais recente para este aluno
    $stmt = $pdo->prepare("SELECT treino_json, data_ficha FROM treino WHERE aluno_id = ? ORDER BY data_ficha DESC LIMIT 1");
    $stmt->execute([$alunoId]);
    $ficha = $stmt->fetch(PDO::FETCH_ASSOC);

    if ($ficha) {
        // Converte a string JSON para um array PHP
        $treino = json_decode($ficha['treino_json'], true);
        $dataFicha = date('d/m/Y H:i', strtotime($ficha['data_ficha']));
    }
} catch (PDOException $e) {
    die("Erro ao carregar dados: " . $e->getMessage());
}
// Verifica se o aluno fez login e cadastra o dia e hora, para cadastrar a frequencia.
$datahoje = date('Y-m-d');
if (!empty($alunoId)) {
    $stmt = $pdo->prepare("SELECT * FROM frequencia WHERE id_aluno = :iddoaluno order by id desc");
    $stmt->bindValue(":iddoaluno", $alunoId);
    $stmt->execute();

    $result = $stmt->fetch(PDO::FETCH_ASSOC);
    $dataPresenca = date('Y-m-d', strtotime($result['data_presenca']));
    echo $dataPresenca . "<br>";
    echo $datahoje;

    if ($dataPresenca == $datahoje) {
        echo "Data já existe";
    } else {
        $stmt = $pdo->prepare("INSERT INTO frequencia (id_aluno) VALUES (:iddoaluno)");
        $stmt->bindValue(":iddoaluno", $alunoId);
        $stmt->execute();
    }


    // if(){
    //     $stmt = $pdo->prepare("INSERT INTO frequencia (id_aluno) VALUES (:iddoaluno)");
    //     $stmt->bindValue(":iddoaluno", $alunoId);
    //     $stmt->execute();
    // }
}

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ficha de Treino de <?php echo $alunoNome; ?></title>
    <link rel="stylesheet" href="css/style.css">

    <style>
       
    </style>
</head>

<body>
    <header>
        <?php include("views/header.php"); ?>
    </header>
    <div class="ficha-view">
        <div class="header-ficha">
            <h1>Medidas do Aluno</h1>
        </div>
        <div class="medidas">
            <!-- altura, peso, quadril, torax, cintura, abdomem, braco_direiro, braco_esquerdo, 
            perna_direita, perna_esquerda, panturilha, aluno_idaluno -->
            Altura: <?php echo "1.80 cm" ?>
        </div>
    </div>
    <div class="ficha-view">
        <div class="cronometro">
            <div id="display">00:00:00</div>
            <button id="toggle">Iniciar</button>
        </div>
    </div>
    <div class="ficha-view">
        <div class="header-ficha">
            <h1>Ficha de Treino: <?php echo $alunoNome; ?></h1>
            <p>Criada em: <strong><?php echo $dataFicha; ?></strong></p>
        </div>
        <div class="mytabs">
            <input type="radio" id="ficha1" name="mytabs" value="tab1" checked>
            <label for="ficha1">Ficha A</label>
            <div class="tab">

                <?php if (!empty($treino)): ?>
                    <ul class="treino-lista">
                        <?php $contador = 1; ?>
                        <?php foreach ($treino as $exercicio): if ($exercicio['treino'] == 'a') { ?>
                                <li>
                                    <div class="exercicio-detalhe">
                                        <?php echo $contador++ . ". " . htmlspecialchars($exercicio['nome']); ?>
                                    </div>
                                    <div class="parametros">
                                        <span>Séries: <strong><?php echo htmlspecialchars($exercicio['series']); ?></strong></span>
                                        <span>Reps: <strong><?php echo htmlspecialchars($exercicio['repeticoes']); ?></strong></span>
                                        <span>Carga: <strong><?php echo htmlspecialchars($exercicio['carga']); ?> kg</strong></span>

                                    </div>
                                    <div class="exercicio-obs">
                                        <textarea name="obs" id="obs" readonly><?php echo htmlspecialchars($exercicio['obs']); ?></textarea>
                                    </div>
                                    <div>
                                        <input type="checkbox" class="check_a">
                                    </div>
                                </li>
                        <?php }
                        endforeach; ?>
                    </ul>
                <?php else: ?>

                    <div class="ficha-vazia">
                        <p>Nenhuma ficha de treino encontrada para este aluno.</p>
                    </div>
                <?php endif; ?>

            </div>


            <input type="radio" id="ficha2" name="mytabs" value="tab2">
            <label for="ficha2">Ficha B</label>
            <div class="tab">

                <?php if (!empty($treino)): ?>
                    <ul class="treino-lista">
                        <?php $contador = 1; ?>
                        <?php foreach ($treino as $exercicio): if ($exercicio['treino'] == 'b') { ?>
                                <li>
                                    <div class="exercicio-detalhe">
                                        <?php echo $contador++ . ". " . htmlspecialchars($exercicio['nome']); ?>
                                    </div>
                                    <div class="parametros">
                                        <span>Séries: <strong><?php echo htmlspecialchars($exercicio['series']); ?></strong></span>
                                        <span>Reps: <strong><?php echo htmlspecialchars($exercicio['repeticoes']); ?></strong></span>
                                        <span>Carga: <strong><?php echo htmlspecialchars($exercicio['carga']); ?> kg</strong></span>

                                    </div>
                                    <div class="exercicio-obs">
                                        <textarea name="obs" id="obs" readonly><?php echo htmlspecialchars($exercicio['obs']); ?></textarea>
                                    </div>
                                    <div>
                                        <input type="checkbox" class="check_b">
                                    </div>
                                </li>
                        <?php }
                        endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="ficha-vazia">
                        <p>Nenhuma ficha de treino encontrada para este aluno.</p>
                    </div>
                <?php endif; ?>


            </div>

            <input type="radio" id="ficha3" name="mytabs" value="tab3">
            <label for="ficha3">Ficha C</label>
            <div class="tab">

                <?php if (!empty($treino)): ?>
                    <ul class="treino-lista">
                        <?php $contador = 1; ?>
                        <?php foreach ($treino as $exercicio): if ($exercicio['treino'] == 'c') { ?>
                                <li>
                                    <div class="exercicio-detalhe">
                                        <?php echo $contador++ . ". " . htmlspecialchars($exercicio['nome']); ?>
                                    </div>
                                    <div class="parametros">
                                        <span>Séries: <strong><?php echo htmlspecialchars($exercicio['series']); ?></strong></span>
                                        <span>Reps: <strong><?php echo htmlspecialchars($exercicio['repeticoes']); ?></strong></span>
                                        <span>Carga: <strong><?php echo htmlspecialchars($exercicio['carga']); ?> kg</strong></span>
                                    </div>
                                    <div class="exercicio-obs">
                                        <textarea name="obs" id="obs" readonly><?php echo htmlspecialchars($exercicio['obs']); ?></textarea>
                                    </div>
                                    <div>
                                        <input type="checkbox" class="check_c">
                                    </div>
                                </li>
                        <?php }
                        endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="ficha-vazia">
                        <p>Nenhuma ficha de treino encontrada para este aluno.</p>
                    </div>
                <?php endif; ?>



            </div>

            <input type="radio" id="ficha4" name="mytabs" value="tab4">
            <label for="ficha4">Ficha D</label>
            <div class="tab">

                <?php if (!empty($treino)): ?>
                    <ul class="treino-lista">
                        <?php $contador = 1; ?>
                        <?php foreach ($treino as $exercicio): if ($exercicio['treino'] == 'd') { ?>
                                <li>
                                    <div class="exercicio-detalhe">
                                        <?php echo $contador++ . ". " . htmlspecialchars($exercicio['nome']); ?>
                                    </div>
                                    <div class="parametros">
                                        <span>Séries: <strong><?php echo htmlspecialchars($exercicio['series']); ?></strong></span>
                                        <span>Reps: <strong><?php echo htmlspecialchars($exercicio['repeticoes']); ?></strong></span>
                                        <span>Carga: <strong><?php echo htmlspecialchars($exercicio['carga']); ?> kg</strong></span>

                                    </div>
                                    <div class="exercicio-obs">
                                        <textarea name="obs" id="obs" readonly><?php echo htmlspecialchars($exercicio['obs']); ?></textarea>
                                    </div>
                                    <div>
                                        <input type="checkbox" class="check_d">
                                    </div>
                                </li>
                        <?php }
                        endforeach; ?>
                    </ul>
                <?php else: ?>
                    <div class="ficha-vazia">
                        <p>Nenhuma ficha de treino encontrada para este aluno.</p>
                    </div>
                <?php endif; ?>

            </div>
        </div>
    </div>
    <div class="cont-prog">
        <div class="progresso">
            <p>Progresso:</p>
            <div class="barra"><span id="barra"></span></div>
        </div>

        <div class="conquista" id="conquista" style="display:none;">
            🎉🎉 Parabéns! Você completou 100% do treino! 🎉🎉
        </div>
    </div>
    <div style="text-align: center; margin-top: 30px;">
        <a href="ficha.php" style="color: #007bff; text-decoration: none;">&larr; Voltar para Montar Fichas</a>
    </div>


    <script>
        const checks_a = document.querySelectorAll(".check_a");
        const checks_b = document.querySelectorAll(".check_b");
        const checks_c = document.querySelectorAll(".check_c");
        const checks_d = document.querySelectorAll(".check_d");
        const barra = document.getElementById("barra");
        const conquista = document.getElementById("conquista");

        // Seleciona todos os radios com o mesmo "name"
        const radios = document.querySelectorAll('input[name="mytabs"]');
        const resultado = document.getElementById("resultado");

        // Adiciona evento de clique em cada radio
        radios.forEach(radio => {
            radio.addEventListener("click", () => {

                if (radio.value == "tab2") {
                    checks_b.forEach(c => {
                        c.addEventListener("change", atualizarProgresso);
                    });

                    function atualizarProgresso() {

                        let total = checks_b.length;
                        let feitos = [...checks_b].filter(c => c.checked).length;
                        let porcentagem = (feitos / total) * 100;
                        barra.style.width = porcentagem + "%";

                        if (porcentagem === 100) {
                            conquista.style.display = "block";
                        } else {
                            conquista.style.display = "none";
                        }
                    }
                } else if (radio.value == "tab3") {
                    checks_c.forEach(c => {
                        c.addEventListener("change", atualizarProgresso);
                    });

                    function atualizarProgresso() {

                        let total = checks_c.length;
                        let feitos = [...checks_c].filter(c => c.checked).length;
                        let porcentagem = (feitos / total) * 100;
                        barra.style.width = porcentagem + "%";

                        if (porcentagem === 100) {
                            conquista.style.display = "block";
                        } else {
                            conquista.style.display = "none";
                        }
                    }
                } else if (radio.value == "tab4") {
                    checks_d.forEach(c => {
                        c.addEventListener("change", atualizarProgresso);
                    });

                    function atualizarProgresso() {

                        let total = checks_d.length;
                        let feitos = [...checks_d].filter(c => c.checked).length;
                        let porcentagem = (feitos / total) * 100;
                        barra.style.width = porcentagem + "%";

                        if (porcentagem === 100) {
                            conquista.style.display = "block";
                        } else {
                            conquista.style.display = "none";
                        }
                    }
                }
            });
        });


        checks_a.forEach(c => {
            c.addEventListener("change", atualizarProgresso);
        });

        function atualizarProgresso() {

            let total = checks_a.length;
            let feitos = [...checks_a].filter(c => c.checked).length;
            let porcentagem = (feitos / total) * 100;
            barra.style.width = porcentagem + "%";

            if (porcentagem === 100) {
                conquista.style.display = "block";

            } else {
                conquista.style.display = "none";
            }
        }


        let timer = null;
        let seconds = 0;
        let running = false;

        function formatTime(sec) {
            let h = String(Math.floor(sec / 3600)).padStart(2, '0');
            let m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
            let s = String(sec % 60).padStart(2, '0');
            return `${h}:${m}:${s}`;
        }

        document.getElementById("toggle").addEventListener("click", function() {
            if (!running) {
                running = true;
                this.textContent = "Parar";
                timer = setInterval(() => {
                    seconds++;
                    document.getElementById("display").textContent = formatTime(seconds);
                }, 1000);
            } else {
                running = false;
                this.textContent = "Iniciar";
                clearInterval(timer);

                // Enviar tempo para o servidor
                fetch("salvar.php", {
                        method: "POST",
                        headers: {
                            "Content-Type": "application/x-www-form-urlencoded"
                        },
                        body: "tempo=" + seconds
                    })
                    .then(res => res.text())
                    .then(data => alert(data));
            }
        });
    </script>

</body>

</html>