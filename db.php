<?php
// db.php (Reutilizado do projeto anterior)
$host = 'localhost';
$dbname = 'fichasonline'; // Reutilizando a DB ou crie uma nova
$user = 'root'; 
$password = ''; 

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8", $user, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Erro na conexão: " . $e->getMessage());
}
?>