<?php


?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <title>Cronômetro Academia</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      text-align: center;
      margin-top: 50px;
    }
    #display {
      font-size: 3em;
      margin-bottom: 20px;
    }
    button {
      padding: 10px 20px;
      font-size: 1.2em;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <div id="display">00:00:00</div>
  <button id="toggle">Iniciar</button>

  <script>
    let timer = null;
    let seconds = 0;
    let running = false;

    function formatTime(sec) {
      let h = String(Math.floor(sec / 3600)).padStart(2, '0');
      let m = String(Math.floor((sec % 3600) / 60)).padStart(2, '0');
      let s = String(sec % 60).padStart(2, '0');
      return `${h}:${m}:${s}`;
    }

    document.getElementById("toggle").addEventListener("click", function() {
      if (!running) {
        running = true;
        this.textContent = "Parar";
        timer = setInterval(() => {
          seconds++;
          document.getElementById("display").textContent = formatTime(seconds);
        }, 1000);
      } else {
        running = false;
        this.textContent = "Iniciar";
        clearInterval(timer);
      }
    });
  </script>
</body>
</html>
