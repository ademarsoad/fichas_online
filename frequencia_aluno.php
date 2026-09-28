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

// Cria array com os dias de presença
$diasPresenca = [];
foreach ($result as $resultado) {
    $diasPresenca[] = date('d', strtotime($resultado['data_presenca']));
}

// Informações do mês atual
setlocale(LC_TIME, 'pt_BR', 'pt_BR.utf-8', 'pt_BR.utf-8', 'portuguese');
date_default_timezone_set('America/Sao_Paulo');
$nomeMes = strftime('%B', mktime(0, 0, 0, $mesAtual, 1, $anoAtual));
$diasNoMes = cal_days_in_month(CAL_GREGORIAN, $mesAtual, $anoAtual);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined" rel="stylesheet" />

    <title>Frequência - <?php echo $alunoNome; ?></title>
    <style>
        .semana {
            border: 1px solid #ccc;
            margin: 10px 0;
            padding: 10px;
        }

        .semana h3 {
            margin: 0 0 10px 0;
        }

        .dia {
            display: inline-block;
            margin: 5px;
        }
        .confirm {
            color: green;
        }
        .falsed {
            color: red;
        }
    </style>
</head>

<body>
    <header>
        <?php include_once('views/header.php'); ?>
    </header>
    <h2>Frequência do aluno: <?php echo $alunoNome; ?></h2>
    <h3>Mês atual: <?php echo ucfirst($nomeMes) . " / " . $anoAtual; ?></h3>

    <?php
    // Divide os dias do mês em semanas reais
    $semana = 1;
    for ($dia = 1; $dia <= $diasNoMes; $dia++) {
        // Descobre o dia da semana (1 = segunda, 7 = domingo)
        $timestamp = mktime(0, 0, 0, $mesAtual, $dia, $anoAtual);
        $diaSemana = date('N', $timestamp);

        // Se for início da semana, abre bloco
        if ($diaSemana == 1) {
            echo "<div class='semana'><h3>Semana $semana</h3>";
        }

        echo "<div class='dia'>";
        echo "<label>Dia $dia</label> ";

        if (in_array($dia, $diasPresenca)) {
            // Mostra imagem de correto verde
            // echo "<img src='check-verde.png' alt='Presente' width='20'>";
    ?><span class="material-symbols-outlined confirm">
check_circle
</span> <?php
    } else {
       // Mostra vazio ou outro ícone (opcional)
        // echo "<img src='x-cinza.png' alt='Ausente' width='20'>";
        ?> <span class="material-symbols-outlined falsed">close</span> <?php
     }
       echo "</div>";
    // Se for domingo ou último dia do mês, fecha bloco
      if ($diaSemana == 7 || $dia == $diasNoMes) {
       echo "</div>";
      $semana++;
    }
    }
    ?>
</body>

</html>