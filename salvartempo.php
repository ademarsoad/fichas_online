<?php
require('db.php');


if (isset($_POST['tempo'])) {
    $tempo = $_POST['tempo'] ?? 0;
    $aluno = $_POST['aluno'] ?? 0;

    $stmt = $pdo->prepare('update frequencia set tempo_treino = :temp where id_aluno = :idaluno 
    order by id desc LIMIT 1');

    $stmt->bindValue(":temp", $tempo);
    $stmt->bindValue(":idaluno", $aluno);

    $stmt->execute();

    $horas = str_pad(floor($tempo / 3600), 2, '0', STR_PAD_LEFT);
    $minutos = str_pad(floor(($tempo % 3600) / 60), 2, '0', STR_PAD_LEFT);
    $segundos = str_pad($tempo % 60, 2, '0', STR_PAD_LEFT);

    $tempoFormatado = "$horas:$minutos:$segundos";

    

    echo "Aluno: $aluno - Tempo: $tempoFormatado";


 } else {

    echo "Nenhum tempo recebido";
}
