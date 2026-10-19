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
        <a href="cadastro_aluno.php">Cadastro Aluno</a>
            <a href="meusalunos.php?idProfessor=<?php echo $_SESSION['professor']; ?>">Meus Alunos</a>
            <a href="sair.php">Sair</a>
        </div>
</nav>