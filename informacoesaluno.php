<?php

include_once('db.php');

session_start();
if (!isset($_SESSION['professor'])) {
    header('location: index.php');

    exit;
}



if (!isset($_GET['idAluno']));

$id_aluno_ficha = $_GET['idAluno'];

$stmt = $pdo->prepare("SELECT treino_json, data_ficha FROM treino WHERE aluno_id = ? ORDER BY data_ficha DESC LIMIT 1");
$stmt->execute([$id_aluno_ficha]);

$ficha = $stmt->fetch(PDO::FETCH_ASSOC);

if ($ficha) {
    $treino = json_decode($ficha['treino_json'], true);
}



?>

<!DOCTYPE html>
<html lang="pt_br">
<style>
    table {
        border-collapse: collapse;
        margin: 0 auto;
        /* Merge borders into single lines */
    }

    th,
    td {
        border: 1px solid #333;
        /* Border for cells */
        padding: 10px;
        /* Space inside cells */
        text-align: left;
    }

    th {
        background-color: #f2f2f2;
        /* Header background */
    }
</style>

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Aluno </title>

</head>

<body>
<?php include('navbar.php'); ?>

    <h1>Visualizar / Alterar Ficha</h1>
    <table id="tabelaTreino">
        <tbody>
            <tr>
                <th>Nome</th>
                <th>Serie</th>
                <th>Repetição</th>
                <th>Carga</th>
                <th>Obs</th>
                <th>Treino</th>
                <th></th>
            </tr>
            <tr>
                <form action="" method="POST">
                    <?php foreach ($treino as $f) { ?>

                        <td><input type="text" name="nome[]" value="<?php echo $f['nome']; ?>"></td>
                        <td><input type="text" name="series[]" value="<?php echo $f['series']; ?>"></td>
                        <td><input type="text" name="repeticoes[]" value="<?php echo $f['repeticoes']; ?>"></td>
                        <td><input type="text" name="carga[]" value="<?php echo $f['carga']; ?>"></td>
                        <td><input type="text" name="obs[]" value="<?php echo $f['obs']; ?>"></td>
                        <td><input type="text" name="treino[]" value="<?php echo $f['treino']; ?>"></td>
                        <td><button type="button" onclick="removerLinha(this)">Remover</button></td>
            </tr>


            </tr>
        <?php
                    }
                    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                        $novoTreino = [];
                        foreach ($_POST['nome'] as $i => $nome) {
                            $novoTreino[] = [
                                "nome"       => $nome,
                                "series"     => $_POST['series'][$i],
                                "repeticoes" => $_POST['repeticoes'][$i],
                                "carga"      => $_POST['carga'][$i],
                                "obs"        => $_POST['obs'][$i],
                                "treino"     => $_POST['treino'][$i]
                            ];
                        }

                        $jsonTreino = json_encode($novoTreino);
                        // var_dump($novoTreino);
                        $stmt = $pdo->prepare("UPDATE treino SET treino_json = ? WHERE aluno_id = ?");
                        $stmt->execute([$jsonTreino, $id_aluno_ficha]);
                    }
        ?>
        </tbody>
    </table>
    <button type="button" onclick="adicionarLinha()">+ Adicionar exercício</button>
    <button type="submit">Alterar</button>
    </form>

</body>
<script>
    function adicionarLinha() {
        let tabela = document.getElementById("tabelaTreino").getElementsByTagName("tbody")[0];
        let novaLinha = tabela.insertRow();

        novaLinha.innerHTML = `
        <td><input type="text" name="nome[]" placeholder="Nome do exercício"></td>
        <td><input type="text" name="series[]" placeholder="Séries"></td>
        <td><input type="text" name="repeticoes[]" placeholder="Repetições"></td>
        <td><input type="text" name="carga[]" placeholder="Carga"></td>
        <td><input type="text" name="obs[]" placeholder="Observações"></td>
        <td><input type="text" name="treino[]" placeholder="Treino"></td>
        <td><button type="button" onclick="removerLinha(this)">Remover</button></td>

    `;
    }
    function removerLinha(botao) {
    // Remove a linha onde o botão foi clicado
    let linha = botao.closest("tr");
    linha.remove();
}

</script>

</html>