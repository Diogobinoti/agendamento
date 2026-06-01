<?php
// public/api/disponibilidade.php
// Endpoint chamado via JS para verificar horários ocupados

require '../../includes/auth.php';
exigirLogin();
require '../../config/conexao.php';

header('Content-Type: application/json');

$espacoId = (int)($_GET['espaco'] ?? 0);
$data     = $_GET['data'] ?? '';

if (!$espacoId || !$data || !preg_match('/^\d{4}-\d{2}-\d{2}$/', $data)) {
    echo json_encode(['reservas' => []]);
    exit;
}

$stmt = $conn->prepare(
    'SELECT hora_inicio, hora_fim
     FROM reservas
     WHERE espaco_id    = ?
       AND data_reserva = ?
       AND status       = "ativa"
     ORDER BY hora_inicio'
);
$stmt->bind_param('is', $espacoId, $data);
$stmt->execute();
$resultado = $stmt->get_result();

$reservas = [];
while ($r = $resultado->fetch_assoc()) {
    $reservas[] = $r;
}
$stmt->close();

echo json_encode(['reservas' => $reservas]);