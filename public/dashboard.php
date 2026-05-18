<?php
include '../includes/auth.php';
include '../config/conexao.php';

$quadras = mysqli_query($conn, "SELECT * FROM quadras");
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Dashboard</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<div class="container">

<h1>Bem-vindo, <?php echo $_SESSION['nome']; ?></h1>

<a href="reservar.php"><button>Reservar Quadra</button></a>

<?php
if ($_SESSION['tipo'] == 'admin') {
    echo '<a href="admin_quadras.php"><button>Gerenciar Quadras</button></a>';
}
?>

<a href="logout.php"><button>Sair</button></a>

<?php while($quadra = mysqli_fetch_assoc($quadras)) { ?>

<div class="card">

<h2><?php echo $quadra['nome']; ?></h2>

<p><?php echo $quadra['descricao']; ?></p>

<p>R$ <?php echo $quadra['preco_hora']; ?>/hora</p>

</div>

<?php } ?>

</div>

</body>
</html>
