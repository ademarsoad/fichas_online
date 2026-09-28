<?php
// 1. INÍCIO DE SESSÃO (Essencial para manter o usuário logado)
// Este deve ser o PRIMEIRO comando em qualquer página que use sessões
session_start();

// 2. VERIFICAÇÃO DE LOGIN (Simulada, pois o index.php anterior não criava a sessão)
// Em um sistema real, você verificaria se $_SESSION['logado'] é TRUE.
// Para fins de demonstração, vamos simular que o login foi bem-sucedido no index.php e 
// que as informações do usuário estão aqui.

// Se você tivesse um login bem-sucedido no index.php, faria:
// $_SESSION['logado'] = true;
// $_SESSION['usuario'] = $usuario_digitado;
// header('Location: dashboard.php'); // Redireciona para cá

// --- SIMULAÇÃO DE DADOS DO USUÁRIO ---
// Se você acessasse esta página diretamente sem passar pelo index.php,
// você veria a mensagem de erro, pois a sessão não estaria iniciada.
if (!isset($_SESSION['logado']) || $_SESSION['logado'] !== true) {
    // Se não estiver logado, redireciona de volta para o login
    $usuario_logado = 'Visitante'; // Fallback
    
    // Em um ambiente real, você faria:
    // header('Location: index.php?erro=sessao'); 
    // exit();
    
    // Para a demonstração no navegador:
    $mensagem_alerta = "<div class='alerta erro'>Você não está logado. Por favor, acesse a página de login (index.php).</div>";

} else {
    $usuario_logado = $_SESSION['usuario'];
    $mensagem_alerta = "<div class='alerta sucesso'>Bem-vindo(a) de volta, <strong>" . htmlspecialchars($usuario_logado) . "</strong>! Sua ficha de treino está pronta.</div>";
}

// Função de Logout (Simulada)
function fazerLogout() {
    // Em um sistema real, você faria:
    // session_destroy();
    // header('Location: index.php');
    // exit();
    return "<div class='alerta aviso'>Você foi desconectado. (Ação simulada)</div>";
}

if (isset($_GET['logout']) && $_GET['logout'] == 'true') {
    $mensagem_alerta = fazerLogout();
}

?>
<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard - Minha Ficha de Treino</title>
    <style>
        /* Estilos BÁSICOS para o Dashboard (Reutilizando a estética do index.php) */
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #e9ecef;
            color: #333;
        }
        header {
            background-color: #007bff;
            color: white;
            padding: 15px 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        header a {
            color: white;
            text-decoration: none;
            border: 1px solid white;
            padding: 5px 10px;
            border-radius: 4px;
            transition: background-color 0.3s;
        }
        header a:hover {
            background-color: rgba(255, 255, 255, 0.2);
        }

        .container-dash {
            width: 90%;
            max-width: 1200px;
            margin: 25px auto;
            display: flex;
            gap: 25px;
        }

        .coluna-principal {
            flex: 3;
        }

        .sidebar {
            flex: 1;
            min-width: 250px;
        }
        
        /* Cartões de Conteúdo */
        .card {
            background-color: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        
        .card h3 {
            color: #007bff;
            border-bottom: 1px solid #eee;
            padding-bottom: 10px;
        }

        /* Alertas de Mensagem (Copiados do index.php para consistência) */
        .alerta {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 4px;
            font-weight: bold;
        }
        .sucesso {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }
        .erro {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        .aviso {
            background-color: #fff3cd;
            color: #856404;
            border: 1px solid #ffeeba;
        }
        
        /* Estilo da Ficha de Treino (Simulação) */
        .ficha-item {
            border: 1px solid #ddd;
            padding: 10px;
            margin-bottom: 10px;
            border-radius: 4px;
        }
        .ficha-item strong {
            display: block;
            color: #333;
        }
    </style>
</head>
<body>

    <header>
        <h2>Bem-vindo(a), <?php echo htmlspecialchars($usuario_logado); ?></h2>
        <div>
            <a href="dashboard.php?logout=true">Sair</a>
        </div>
    </header>

    <div class="container-dash">
        
        <main class="coluna-principal">
            <?php echo $mensagem_alerta; // Exibe o status de login/logout ?>

            <div class="card">
                <h3>Sua Ficha de Treino Atual (Dia 1: Peito e Tríceps)</h3>
                
                <div class="ficha-item">
                    <strong>Supino Reto com Barra:</strong> 4 Séries de 8-10 Repetições (Carga: 60kg)
                </div>
                <div class="ficha-item">
                    <strong>Crucifixo Inclinado com Halteres:</strong> 3 Séries de 12 Repetições (Carga: 20kg)
                </div>
                <div class="ficha-item">
                    <strong>Tríceps Testa:</strong> 3 Séries de 10-12 Repetições
                </div>
                
                <button style="background-color: #17a2b8; margin-top: 15px;">Marcar Treino Como Concluído</button>
            </div>

            <div class="card">
                <h3>Progresso Recente</h3>
                <p>Você aumentou sua carga total em 5% nas últimas 4 semanas!</p>
                </div>
        </main>

        <aside class="sidebar">
            <div class="card">
                <h3>Menu Rápido</h3>
                <ul>
                    <li><a href="#">Ver Histórico</a></li>
                    <li><a href="#">Ver Próximo Treino</a></li>
                    <li><a href="#">Ajustar Dados Pessoais</a></li>
                </ul>
            </div>
            <div class="card">
                <h3>Dica do Dia</h3>
                <p>Lembre-se de fazer um bom aquecimento antes de levantar cargas pesadas!</p>
            </div>
        </aside>

    </div>
</body>
</html>