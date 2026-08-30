<?php
session_start();
require 'conexao.php';

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    die("<script>alert('Preencha e-mail e senha'); window.location='login.html';</script>");
}

$stmt = $pdo->prepare("SELECT * FROM gerencia WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario && password_verify($senha, $usuario['senha'])) {
    $_SESSION['logado'] = true;
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['user_id'] = $usuario['id'];
    header("Location: admin.php");
    exit;
} else {
    echo "<script>alert('Acesso negado: E-mail ou senha incorretos.'); window.location.href='login.html';</script>";
}
?>
