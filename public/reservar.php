<?php
include '../includes/auth.php';
include '../config/conexao.php';

$quadras = mysqli_query($conn, "SELECT * FROM quadras");

if ($_SERVER['REQUEST_METHOD'] == 'POST') {

    $usuario = $_SESSION['id'];
    $quadra = $_POST['quadra'];
    $data = $_POST['data'];
    $inicio = $_POST['inicio'];
    $fim = $_POST['fim'];

    $verifica = mysqli_query(
        $conn,
        "SELECT * FROM reservas
         WHERE quadra_id='$quadra'
         AND data_reserva='$data'
         AND (
            hora_inicio < '$fim'
            AND hora_fim > '$inicio'
         )"
    );

    if (mysqli_num_rows($verifica) > 0) {
        die('Horário indisponível');
    }

    $sql = "INSERT INTO reservas
    (usuario_id,quadra_id,data_reserva,hora_inicio,hora_fim)
    VALUES
    ('$usuario','$quadra','$data','$inicio','$fim')";

    mysqli_query($conn, $sql);

    echo "Reserva criada com sucesso!";
}
?>

<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<title>Reservar</title>
<link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<?php include '../includes/header.php'; ?>

<div class="container">

<div class="card">

<h2>Reservar Quadra</h2>

<form method="POST">

<select name="quadra">

<?php while($quadra = mysqli_fetch_assoc($quadras)) { ?>

<option value="<?php echo $quadra['id']; ?>">
<?php echo $quadra['nome']; ?>
</option>

<?php } ?>

</select>

<input type="date" name="data" required>

<input type="time" name="inicio" required>

<input type="time" name="fim" required>

<button type="submit">Reservar</button>

</form>

</div>

</div>

</body>
</html>
