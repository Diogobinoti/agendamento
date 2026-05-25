<?php
session_start();
include '../config/conexao.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $email = $_POST['email'];
    $senha = md5($_POST['senha']);

    $sql = "SELECT * FROM usuarios WHERE email='$email' AND senha='$senha'";
    $resultado = mysqli_query($conn, $sql);

    if (mysqli_num_rows($resultado) > 0) {

        $usuario = mysqli_fetch_assoc($resultado);

        $_SESSION['id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['tipo'] = $usuario['tipo'];

        header('Location: dashboard.php');

    } else {
        echo "Login inválido";
    }
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Login</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">

<div class="card">

<h2>Login</h2>

<form method="POST">

<input type="email" name="email" placeholder="Email" required>

<input type="password" name="senha" placeholder="Senha" required>

<button type="submit">Entrar</button>

</form>

<br>

<a href="cadastro.php">Criar conta</a>

</div>

</div>

</body>
</html>
