<?php
include_once('db.php');




?>


<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Tela de Cadastro</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background: linear-gradient(135deg, #4facfe, #00f2fe);
      display: flex;
      justify-content: center;
      align-items: center;
      height: 100vh;
    }
    .container {
      background: #fff;
      padding: 20px 30px;
      border-radius: 8px;
      box-shadow: 0 0 10px rgba(0,0,0,0.1);
      width: 320px;
    }
    h2 {
      text-align: center;
      margin-bottom: 20px;
    }
    label {
      display: block;
      margin-top: 10px;
      font-weight: bold;
    }
    input, select {
      width: 100%;
      padding: 8px;
      margin-top: 5px;
      border: 1px solid #ccc;
      border-radius: 4px;
    }
    button {
      margin-top: 20px;
      width: 100%;
      padding: 10px;
      background: #4facfe;
      color: white;
      border: none;
      border-radius: 4px;
      cursor: pointer;
      font-size: 16px;
    }
    button:hover {
      background: #00c6fb;
    }
    .hidden {
      display: none;
    }
  </style>
</head>
<body>
  <div class="container">
    <h2>Cadastro</h2>
    <form id="cadastroForm">
      <label for="email">Email:</label>
      <input type="email" id="email" required>

      <label for="senha">Senha:</label>
      <input type="password" id="senha" required>

      <label for="tipo">Você é:</label>
      <select id="tipo" required>
        <option value="">Selecione...</option>
        <option value="professor">Professor</option>
        <option value="aluno">Aluno</option>
      </select>

      <!-- Campo extra para professor -->
      <div id="professorFields" class="hidden">
        <label for="cref">Número do CREF:</label>
        <input type="text" id="cref">
      </div>

      <button type="submit">Cadastrar</button>
    </form>
  </div>

  <script>
    const tipoSelect = document.getElementById("tipo");
    const professorFields = document.getElementById("professorFields");

    tipoSelect.addEventListener("change", function() {
      if (this.value === "professor") {
        professorFields.classList.remove("hidden");
        document.getElementById("cref").setAttribute("required", "true");
      } else {
        professorFields.classList.add("hidden");
        document.getElementById("cref").removeAttribute("required");
      }
    });

    document.getElementById("cadastroForm").addEventListener("submit", function(event) {
      event.preventDefault();

      const email = document.getElementById("email").value;
      const senha = document.getElementById("senha").value;
      const tipo = document.getElementById("tipo").value;
      const cref = document.getElementById("cref").value;

      if (tipo === "professor") {
        alert(`Cadastro realizado!\nEmail: ${email}\nTipo: ${tipo}\nCREF: ${cref}`);
      } else {
        alert(`Cadastro realizado!\nEmail: ${email}\nTipo: ${tipo}`);
      }
    });
  </script>
</body>
</html>