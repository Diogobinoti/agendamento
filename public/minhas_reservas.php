<?php
// public/minhas_reservas.php
require '../includes/auth.php';
exigirLogin();
require '../config/conexao.php';

$usuarioId = (int)$_SESSION['id'];
$msg = '';

// --- Cancelar reserva ---
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cancelar_id'])) {
    $cancelarId = (int)$_POST['cancelar_id'];

    // Garante que a reserva pertence ao usuário e ainda está ativa
    $stmtCancel = $conn->prepare(
        'UPDATE reservas SET status = "cancelada"
         WHERE id = ? AND usuario_id = ? AND status = "ativa"'
    );
    $stmtCancel->bind_param('ii', $cancelarId, $usuarioId);
    $stmtCancel->execute();

    if ($stmtCancel->affected_rows > 0) {
        $msg = ['tipo' => 'ok', 'texto' => 'Reserva cancelada com sucesso.'];
    } else {
        $msg = ['tipo' => 'erro', 'texto' => 'Não foi possível cancelar esta reserva.'];
    }
    $stmtCancel->close();
}

// --- Busca reservas do usuário ---
$stmt = $conn->prepare(
    'SELECT r.id, e.nome AS espaco, e.tipo, r.data_reserva,
            r.hora_inicio, r.hora_fim, r.status, r.criado_em
     FROM reservas r
     JOIN espacos e ON e.id = r.espaco_id
     WHERE r.usuario_id = ?
     ORDER BY r.data_reserva DESC, r.hora_inicio DESC'
);
$stmt->bind_param('i', $usuarioId);
$stmt->execute();
$reservas = $stmt->get_result();
$stmt->close();

$hoje = date('Y-m-d');
?>
<?php include '../includes/header.php'; ?>

<main class="container">
    <div class="page-top">
        <h1>Minhas Reservas</h1>
        <a href="reservar.php" class="btn btn-primary">+ Nova Reserva</a>
    </div>

    <?php if ($msg): ?>
        <div class="alert alert-<?= $msg['tipo'] ?>"><?= htmlspecialchars($msg['texto']) ?></div>
    <?php endif; ?>

    <?php if ($reservas->num_rows === 0): ?>
        <div class="alert alert-info">Você ainda não tem reservas. <a href="reservar.php">Fazer uma reserva</a></div>
    <?php else: ?>
        <div class="tabela-wrap">
            <table class="tabela">
                <thead>
                    <tr>
                        <th>#</th>
                        <th>Espaço</th>
                        <th>Data</th>
                        <th>Horário</th>
                        <th>Status</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                <?php while ($r = $reservas->fetch_assoc()): ?>
                    <?php
                    $passada  = $r['data_reserva'] < $hoje;
                    $ativa    = $r['status'] === 'ativa';
                    $podeCancelar = $ativa && !$passada;
                    ?>
                    <tr class="<?= !$ativa ? 'linha-cancelada' : ($passada ? 'linha-passada' : '') ?>">
                        <td><?= $r['id'] ?></td>
                        <td>
                            <strong><?= htmlspecialchars($r['espaco']) ?></strong><br>
                            <small><?= htmlspecialchars($r['tipo']) ?></small>
                        </td>
                        <td><?= date('d/m/Y', strtotime($r['data_reserva'])) ?></td>
                        <td><?= substr($r['hora_inicio'],0,5) ?> – <?= substr($r['hora_fim'],0,5) ?></td>
                        <td>
                            <span class="badge badge-<?= $r['status'] ?>">
                                <?= $r['status'] === 'ativa' ? '✅ Ativa' : '❌ Cancelada' ?>
                            </span>
                        </td>
                        <td>
                            <?php if ($podeCancelar): ?>
                                <form method="POST"
                                      onsubmit="return confirm('Deseja cancelar esta reserva?')">
                                    <input type="hidden" name="cancelar_id" value="<?= $r['id'] ?>">
                                    <button type="submit" class="btn btn-danger btn-sm">Cancelar</button>
                                </form>
                            <?php else: ?>
                                <span class="texto-muted">—</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</main>

<?php include '../includes/footer.php'; ?>