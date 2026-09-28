<?php
include_once 'db.php';
// --- LÓGICA DE LOGIN BÁSICA (NÃO SEGURO PARA PRODUÇÃO!) ---
$mensagem = '';

// Verificação de envio do formulário
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $usuario_digitado = $_POST['usuario'] ?? '';
    $senha_digitada = password_hash($_POST['senha'], PASSWORD_DEFAULT)  ?? '';

    // var_dump($senha_digitada);

    $stmt = $pdo->prepare("Select * from login WHERE usuario = ?");
    $stmt->execute([$usuario_digitado]);
    $login = $stmt->fetch(pdo::FETCH_ASSOC);

    if ($login) {
       $usuario_correto = $login['usuario'];
       $id_login = $login['id_login'];
       $id_professor = $login['professor_login'];
       session_start();
        $_SESSION['id_aluno'] = $id_login;
        $_SESSION['usuario'] = $usuario_correto;
        $_SESSION['professor'] = $id_professor;

        if (password_verify($login['senha'], $senha_digitada)) {
            // SIMULAÇÃO DE SUCESSO: Em um sistema real, você iniciaria uma sessão (session_start())
            // e redirecionaria para a página principal (ex: dashboard.php).
            $mensagem = "<div class='alerta sucesso'>Login realizado com sucesso! Bem-vindo(a), $usuario_correto!</div>";
            if($login['aluno_login'] != ""){
                 
                header('Location: visualizar_ficha.php?id_login=' . $id_login);
            } else if($login['professor_login'] != ""){
                header('Location: ficha.php');
            }
        } else {
            $mensagem = "<div class='alerta erro'>Usuário ou senha inválidos. Tente novamente.</div>";
        }
    } else {
        $mensagem = "<div class='alerta erro'>Usuário não existi. Tente novamente.</div>";
    }

    // ATENÇÃO: Em um sistema real, você DEVE usar senhas criptografadas (ex: password_hash e password_verify)
    // e verificar contra um banco de dados (MySQL).

 
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Ficha Online - Login</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      margin: 0;
      padding: 0;
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }

    .container {
      background: #fff;
      padding: 40px;
      border-radius: 10px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.2);
      width: 400px;
      text-align: center;
    }

    .container img {
      width: 100%;
      max-height: 290px;
      object-fit: cover;
      border-radius: 8px;
      margin-bottom: 20px;
    }

    .container h1 {
      margin-bottom: 10px;
      color: #333;
    }

    .container p.apresentacao {
      margin-bottom: 25px;
      color: #555;
      font-size: 15px;
      line-height: 1.4;
    }

    .form-group {
      margin-bottom: 15px;
      text-align: left;
    }

    .form-group label {
      display: block;
      margin-bottom: 5px;
      font-weight: bold;
      color: #444;
    }

    .form-group input {
      width: 100%;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 5px;
      outline: none;
    }

    .form-group input:focus {
      border-color: #4facfe;
    }

    .btn {
      width: 100%;
      padding: 12px;
      background: #4facfe;
      border: none;
      border-radius: 5px;
      color: #fff;
      font-size: 16px;
      cursor: pointer;
      transition: background 0.3s ease;
    }

    .btn:hover {
      background: #00c6fb;
    }

    .footer {
      margin-top: 20px;
      font-size: 14px;
      color: #777;
    }

    .footer a {
      color: #4facfe;
      text-decoration: none;
    }

    .footer a:hover {
      text-decoration: underline;
    }
    .imagemLogo {
      width:100%;  
    
      border-radius:10px; 
      margin-bottom:20px;
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Imagem de destaque -->
    <img src="img/Logo moderno para si.png" alt="Banner Ficha Online" class="imagemLogo">

    <!-- Título e apresentação 
    <h1>Ficha Online</h1>
    <p class="apresentacao">
      Organize suas fichas de forma prática e segura.  
      Acesse suas informações de qualquer lugar e mantenha tudo centralizado em um só sistema.
    </p> -->

    <!-- Formulário de login -->
    <form method="post" action="index.php">
      <div class="form-group">
        <label for="usuario">Usuário</label>
        <input type="text" id="usuario" name="usuario" placeholder="Digite seu usuário">
      </div>
      <div class="form-group">
        <label for="senha">Senha</label>
        <input type="password" id="senha" name="senha" placeholder="Digite sua senha">
      </div>
      <button type="submit" class="btn">Entrar</button>
    </form>

    <div class="footer">
      <p>Não tem conta? <a href="cadastro.php">Cadastre-se</a></p>
    </div>
  </div>
</body>
</html>