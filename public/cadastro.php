<?php
include '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = $_POST['nome'];
    $email = $_POST['email'];
    $senha = md5($_POST['senha']);

    $sql = "INSERT INTO usuarios(nome,email,senha)
            VALUES('$nome','$email','$senha')";

    mysqli_query($conn, $sql);

    header('Location: login.php');
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Cadastro</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

<div class="card">

<h2>Cadastro</h2>

<form method="POST">

<input type="text" name="nome" placeholder="Nome" required>

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="senha" placeholder="Senha" required>

<button type="submit">Cadastrar</button>

</form>

</div>

</div>

</body>
</html>
