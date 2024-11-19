<?php
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');
header("Access-Control-Allow-Headers: Content-Type");
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');




$host = "sql312.infinityfree.com"; // ou o IP do seu servidor
$dbname = "if0_37737698_bot_btw"; // Nome do seu banco de dados
$username = "if0_37737698"; // Seu usuário do MySQL
$password = "yjg4R8a8Gc"; // Sua senha do MySQL

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    // Definindo o modo de erro do PDO
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    echo "Erro de conexão: " . $e->getMessage();
    exit();
}
?>
