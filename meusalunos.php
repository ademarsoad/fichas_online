<?php
require_once 'db.php';

// --- Funções de Carregamento ---
session_start();
if (!isset($_SESSION['professor'])) {
    header('location: index.php');

    exit;
}

$idDoProfessor = $_GET['idProfessor'];

if (isset($idDoProfessor)) {
    $stmt = $pdo->prepare('SELECT * FROM aluno WHERE id_professor = :idProfessor ORDER BY nome_aluno');
    $stmt->bindValue("idProfessor", $idDoProfessor);
    $stmt->execute();

    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/7.0.1/css/all.min.css   " rel="stylesheet">

    <title>Meus Alunos</title>

    <style>
        table {
            width: 80%;
            margin: 0 auto;
        }

        td {
            text-align: center;
            margin: 12px;

        }

        tr:nth-child(even) {
            background-color: #c1c1c1;
        }
        td:nth-child(3) {
            display: flex;
            justify-content: space-around;
            
        }

        .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .card-header a {
            text-decoration: none;
            color: black;
            border: 1px solid blue;
            padding: 10px;
            border-radius: 15px;
            background-color: #c1c1c1;
        }
        .fa-magnifying-glass, .fa-trash {
            font-size: 20px;
        }
    </style>

</head>

<body>
    <?php include('navbar.php'); ?>
    
    <div class="card-header">
        <h4>Lista de Alunos</h4>
        <a href="cadastro_aluno.php">Adicionar Aluno</a>

    </div>
    <table class="table table-border table-striped">
        <thead>
            <tr>
                <th>Numero</th>
                <th>Nome</th>
                <th>Ação</th>
            </tr>
        </thead>
        <tbody>
            <tr>
                <?php foreach ($result as $key=>$alunos) { ?>
                    <td><?php echo $key+1 ?></td>
                    <td><?php echo $alunos['nome_aluno'] ?></td>
                    <td><a href="informacoesaluno.php?idAluno=<?php echo $alunos['idaluno']; ?> and idProfessor=<?php echo $idDoProfessor; ?>"><i class="fa-solid fa-magnifying-glass"></i></a>
                    <i class="fa-solid fa-trash"></i></td>

            </tr>
        <?php } ?>
        </tbody>

    </table>

</body>

</html>