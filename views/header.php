<style>
    nav {
        background-color: #007bff;
        padding: 20px;
        display: flex;
        justify-content: end;
        align-items: center;
    }
    nav div a {
        padding: 0 10px;
        text-decoration: none;
        border: 1px solid red;
        border-radius: 15px;
        background-color: #b4b4b4;
        padding: 2px;

    }
</style>

<nav>
    <div>
        <a href="visualizar_ficha.php?id_login=<?php echo $alunoId ?>">Ficha</a>
            <a href="frequencia_aluno.php?id_login=<?php echo $alunoId ?>">Frequencia</a>
            <a href="sair.php">Sair</a>
        </div>
</nav>