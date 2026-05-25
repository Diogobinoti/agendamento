<?php
include '../includes/auth.php';
include '../config/conexao.php';

if ($_SESSION['tipo'] != 'admin') {
    die('Acesso negado');
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $nome = $_POST['nome'];
    $descricao = $_POST['descricao'];
    $preco = $_POST['preco'];

    $sql = "INSERT INTO quadras(nome,descricao,preco_hora)
            VALUES('$nome','$descricao','$preco')";

    mysqli_query($conn, $sql);
}

$quadras = mysqli_query($conn, "SELECT * FROM quadras");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Quadras</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">

<div class="card">

<h2>Cadastrar Quadra</h2>

<form method="POST">

<input type="text" name="nome" placeholder="Nome" required>

<textarea name="descricao" placeholder="Descrição"></textarea>

<input type="number" step="0.01" name="preco" placeholder="Preço" required>

<button type="submit">Salvar</button>

</form>

</div>

<?php while($quadra = mysqli_fetch_assoc($quadras)) { ?>

<div class="card">

<h3><?php echo $quadra['nome']; ?></h3>

<p><?php echo $quadra['descricao']; ?></p>

</div>

<?php } ?>

</div>

</body>
</html>
