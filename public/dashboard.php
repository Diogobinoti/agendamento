<?php
// public/dashboard.php
require '../includes/auth.php';
exigirLogin();
require '../config/conexao.php';

// Busca apenas espaços ativos
$espacos = $conn->query('SELECT * FROM espacos WHERE ativo = 1 ORDER BY nome');
?>
<?php include '../includes/header.php'; ?>

<main class="container">
    <div class="page-top">
        <h1>Olá, <?= htmlspecialchars($_SESSION['nome']) ?>! 👋</h1>
        <p class="subtitulo">Escolha um espaço abaixo e faça sua reserva.</p>
    </div>

    <?php if ($espacos->num_rows === 0): ?>
        <div class="alert alert-info">Nenhum espaço disponível no momento.</div>
    <?php else: ?>
        <div class="grid-cards">
            <?php while ($e = $espacos->fetch_assoc()): ?>
                <div class="card card-espaco">
                    <div class="card-header-tipo"><?= htmlspecialchars($e['tipo']) ?></div>
                    <h2><?= htmlspecialchars($e['nome']) ?></h2>
                    <p class="descricao"><?= nl2br(htmlspecialchars($e['descricao'])) ?></p>

                    <div class="card-meta">
                        <?php if ($e['capacidade']): ?>
                            <span>👥 Até <?= (int)$e['capacidade'] ?> pessoas</span>
                        <?php endif; ?>
                        <span>💰 R$ <?= number_format($e['preco_hora'], 2, ',', '.') ?>/hora</span>
                    </div>

                    <a href="reservar.php?espaco=<?= $e['id'] ?>"
                       class="btn btn-primary btn-block mt-1">Reservar</a>
                </div>
            <?php endwhile; ?>
        </div>
    <?php endif; ?>

    <div class="acoes-rapidas">
        <a href="minhas_reservas.php" class="btn btn-outline">📋 Ver minhas reservas</a>
        <?php if (isAdmin()): ?>
            <a href="admin_espacos.php"  class="btn btn-outline">🏟️ Gerenciar espaços</a>
            <a href="admin_reservas.php" class="btn btn-outline">📊 Todas as reservas</a>
        <?php endif; ?>
    </div>
</main>

<?php include '../includes/footer.php'; ?>