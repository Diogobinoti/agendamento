<?php
if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$isAdmin = isset($_SESSION['tipo']) && $_SESSION['tipo'] === 'admin';
?>

<header class="main-header">
    <div class="header-left">
        <span class="site-title">Sistema de Agendamento</span>
    </div>
    <nav class="main-nav">
        <?php if (isset($_SESSION['id'])): ?>
            <a href="dashboard.php">Dashboard</a>
            <a href="reservar.php">Reservar</a>
            <?php if ($isAdmin): ?>
                <a href="admin_quadras.php">Admin Quadras</a>
            <?php endif; ?>
            <a href="logout.php">Sair</a>
        <?php else: ?>
            <a href="login.php">Login</a>
            <a href="cadastro.php">Cadastro</a>
        <?php endif; ?>
    </nav>
</header>
