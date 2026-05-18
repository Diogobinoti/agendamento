<?php
$host = "localhost";
$db = "sistema_agendamento";
$user = "root";
$pass = "vertrigo";

$conn = mysqli_connect($host, $user, $pass, $db);

if (!$conn) {
    die("Erro na conexão: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8");
?>
