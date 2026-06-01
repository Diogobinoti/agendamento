<?php
// includes/header.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

$logado  = !empty($_SESSION['id']);
$isAdmin = ($SESSION['tipo'] ?? '') === 'admin';
// Corrige a verificação usando $_SESSION diretamente
$isAdmin = $logado && ($_SESSION['tipo'] === 'admin');

// Detecta a página atual para marcar o link ativo
$paginaAtual = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sistema de Agendamento</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="main-header">
    <div class="header-left">
        <span class="site-title">📅 Sistema de Agendamento</span>
    </div>
    <nav class="main-nav">
        <?php if ($logado): ?>
            <a href="dashboard.php"       class="<?= $paginaAtual === 'dashboard.php'        ? 'ativo' : '' ?>">Início</a>
            <a href="reservar.php"        class="<?= $paginaAtual === 'reservar.php'         ? 'ativo' : '' ?>">Reservar</a>
            <a href="minhas_reservas.php" class="<?= $paginaAtual === 'minhas_reservas.php'  ? 'ativo' : '' ?>">Minhas Reservas</a>
            <?php if ($isAdmin): ?>
                <a href="admin_espacos.php"  class="<?= $paginaAtual === 'admin_espacos.php'  ? 'ativo' : '' ?>">Espaços</a>
                <a href="admin_reservas.php" class="<?= $paginaAtual === 'admin_reservas.php' ? 'ativo' : '' ?>">Todas Reservas</a>
            <?php endif; ?>
            <a href="logout.php" class="nav-logout">Sair</a>
        <?php else: ?>
            <a href="login.php"   class="<?= $paginaAtual === 'login.php'   ? 'ativo' : '' ?>">Login</a>
            <a href="cadastro.php" class="<?= $paginaAtual === 'cadastro.php' ? 'ativo' : '' ?>">Cadastro</a>
        <?php endif; ?>
    </nav>
</header>