<?php
require_once 'db.php';

// --- Funções de Carregamento ---
session_start();
if (!isset($_SESSION['professor'])) {
    header('location: index.php');

    exit;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="css/style.css">
    <title>Cadastro de Aluno</title>

    <style>
        .medidas {
            display: flex;
            
            flex-wrap: wrap;
            align-items: center;
            justify-content: center;
        }
        textarea {
            width: 100%;
            resize: none;
        }
    </style>
</head>

<body>
    <h1>Cadastro Aluno</h1>

    <form method="post">
        <div class="cont_cadastro">
            <div class="cadastro">
                <label for="nome_aluno">Nome de Aluno</label>
                <input type="text" name="nome_aluno" id="">
                <label for="nome_aluno">Email</label>
                <input type="text" name="email_aluno" id="">
                <label for="password_aluno">Senha</label>
                <input type="password" name="password_aluno" id="">
                <input type="button" value="Cadastrar">
            </div>

        </div>
        <h1>Medidas</h1>
        <div class="cont_cadastro">
            <div class="medidas">

                <div>
                    <input type="number" placeholder="Altura" min="1.00" max="2.00" step="0.01">
                    <input type="number" placeholder="Peso">
                </div>
                <div>
                    <input type="number" placeholder="Quadril">
                    <input type="number" placeholder="Torax">
                </div>
                <div>
                    <input type="number" placeholder="Cintura">
                    <input type="number" placeholder="Abdomem">
                </div>
                <div>
                    <input type="number" placeholder="Braço Direito">
                    <input type="number" placeholder="Braço Esquerdo">
                </div>
                <div>
                    <input type="number" placeholder="Perna Direita">
                    <input type="number" placeholder="Perna Esquerda">
                </div>
                <div>
                    <input type="number" placeholder="Panturilha">
                </div>
            </div>
        </div>
        <h1>Objetivos</h1>
        <div>
            <textarea name="obje" id="obje"></textarea>
        </div>
        <h1>Observação Sobre o Aluno</h1>
        <div>
            <textarea name="obs" id="obs"></textarea>
        </div>
    </form>

</body>

</html>