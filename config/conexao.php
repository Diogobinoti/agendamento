<?php
// config/conexao.php
// Ajuste as credenciais conforme seu ambiente

define('DB_HOST', 'localhost');
define('DB_NAME', 'sistema_agendamento');
define('DB_USER', 'root');
define('DB_PASS', 'vertrigo');          // altere para sua senha

$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);

if ($conn->connect_error) {
    // Em produção, nunca exiba detalhes do erro ao usuário
    error_log('Erro de conexão: ' . $conn->connect_error);
    die(json_encode(['erro' => 'Falha na conexão com o banco de dados.']));
}

$conn->set_charset('utf8mb4');