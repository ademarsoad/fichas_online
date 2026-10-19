<style>
    nav {
        background: linear-gradient(135deg, #007bff, #0056b3);
        padding: 10px 20px;
        display: flex;
        justify-content: space-between;
        align-items: center;
        box-shadow: 0 4px 8px rgba(0,0,0,0.2);
        position: relative;
    }

    nav h2 {
        color: white;
        font-family: Arial, sans-serif;
        margin: 0;
        font-size: 20px;
    }

    /* Ícone hamburguer */
    .menu-toggle {
        display: none; /* só aparece em telas pequenas */
        flex-direction: column;
        justify-content: center;
        cursor: pointer;
    }

    .menu-toggle span {
        height: 3px;
        width: 25px;
        background: #fff;
        margin: 4px 0;
        border-radius: 5px;
        transition: 0.4s;
    }

    /* Menu escondido */
    #menu-links {
        display: flex;
        gap: 10px;
    }

    #menu-links a {
        text-decoration: none;
        color: #fff;
        padding: 10px 18px;
        border-radius: 25px;
        background-color: #ffffff;
        color: #007bff;
        font-weight: 600;
        font-family: Arial, sans-serif;
        transition: all 0.3s ease;
    }

    #menu-links a:hover {
        background-color: #007bff;
        color: #fff;
    }

    /* Responsividade */
    @media (max-width: 768px) {
        .menu-toggle {
            display: flex;
        }

        #menu-links {
            position: absolute;
            top: 60px;
            right: 0;
            flex-direction: column;
            background: #007bff;
            width: 100%;
            max-height: 0;
            opacity: 0;
            overflow: hidden;
            transition: max-height 0.4s ease, opacity 0.4s ease;
        }

        #menu-links a {
            color: #fff;
            background: transparent;
            border-bottom: 1px solid rgba(255,255,255,0.2);
            border-radius: 0;
            text-align: center;
            padding: 15px;
        }

        #menu-links.show {
            max-height: 300px;
            opacity: 1;
            z-index: 1;
        }
    }

    /* Animação hamburguer → X */
    .menu-toggle.open span:nth-child(1) {
        transform: rotate(45deg) translate(8px, 8px);
    }
    .menu-toggle.open span:nth-child(2) {
        opacity: 0;
    }
    .menu-toggle.open span:nth-child(3) {
        transform: rotate(-45deg) translate(8px, -8px);
    }
</style>

<nav>
    <h2>Menu</h2>
    <div class="menu-toggle" onclick="toggleMenu(this)">
        <span></span>
        <span></span>
        <span></span>
    </div>
    <div id="menu-links">
        <a href="visualizar_ficha.php?id_login=<?php echo $alunoId ?>">📄 Ficha</a>
        <a href="frequencia_aluno.php?id_login=<?php echo $alunoId ?>">📊 Frequência</a>
        <a href="sair.php">🚪 Sair</a>
    </div>
</nav>

<script>
    function toggleMenu(toggle) {
        toggle.classList.toggle("open");
        document.getElementById("menu-links").classList.toggle("show");
    }
</script>
