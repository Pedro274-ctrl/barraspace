<?php
function require_privileged_admin(): void
{
    if (session_status() !== PHP_SESSION_ACTIVE) {
        session_start();
    }

    if (empty($_SESSION['logado'])) {
        header('Location: login.html');
        exit;
    }

    if (!in_array($_SESSION['perfil'] ?? '', ['gerencia', 'operador'], true)) {
        require __DIR__ . '/403.php';
        exit;
    }
}
