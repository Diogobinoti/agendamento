<?php
// includes/auth.php

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

/**
 * Garante que o usuário esteja logado.
 * Se não estiver, redireciona para login.php
 */
function exigirLogin(): void
{
    if (empty($_SESSION['id'])) {
        header('Location: login.php');
        exit;
    }
}

/**
 * Garante que o usuário seja admin.
 * Se não for, encerra com 403.
 */
function exigirAdmin(): void
{
    exigirLogin();
    if (($_SESSION['tipo'] ?? '') !== 'admin') {
        http_response_code(403);
        include '../includes/header.php';
        echo '<div class="container"><div class="alert alert-erro">Acesso negado. Você não tem permissão para acessar esta página.</div></div>';
        exit;
    }
}

/**
 * Retorna true se o usuário logado for admin.
 */
function isAdmin(): bool
{
    return ($_SESSION['tipo'] ?? '') === 'admin';
}