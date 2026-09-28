<?php
include_once('db.php');

if (!isset($_GET['idAluno']));



$id_aluno_ficha = $_GET['idAluno'];

$stmt = $pdo->prepare("SELECT treino_json, data_ficha FROM treino WHERE aluno_id = ? ORDER BY 
data_ficha DESC LIMIT 1");

$stmt->execute([$id_aluno_ficha]);

$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ficha) {

    $treino = json_decode($ficha['treino_json'], true);
}
// var_dump($treino);

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Alterar Ficha</title>
</head>

<body>


    <table>
        <tbody>
            <tr>
                <th>Nome</th>
                <th>Serie</th>
                <th>Repetições</th>
                <th>Carga</th>
                <th>Obs</th>
                <th>Treino</th>
</tr>
            <tr>
                <form action="" method="post">
                    <?php foreach ($treino as $f) { ?>

                        <td><input type="text" name="" id="" value="<?php echo $f['nome']; ?>"></th>
                        <td><input type="text" name="" id="" value="<?php echo $f['series']; ?>"></th>
                        <td><input type="text" name="" id="" value="<?php echo $f['repeticoes']; ?>"></th>
                        <td><input type="text" name="" id="" value="<?php echo $f['carga']; ?>"></th>
                        <td><input type="text" name="" id="" value="<?php echo $f['obs']; ?>"></th>
                        <td><input type="text" name="" id="" value="<?php echo $f['treino']; ?>"></th>

            </tr>
        <?php } ?>
        </tbody>
    </table>

    <input id="alterButton" type="button" value="Alterar">
    </form>
    <?php

    ?>

    <script src="script.js"></script>
</body>

</html>