<?php
session_start();
require 'conexao.php';

$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$senha = $_POST['senha'] ?? '';

if (!$email || !$senha) {
    die("<script>alert('Preencha e-mail e senha'); window.location='login.html';</script>");
}

$stmt = $pdo->prepare("SELECT id, email, senha, nome, perfil FROM gerencia WHERE email = ?");
$stmt->execute([$email]);
$usuario = $stmt->fetch();

if ($usuario && in_array($usuario['perfil'], ['gerencia', 'operador'], true) && password_verify($senha, $usuario['senha'])) {
    session_regenerate_id(true);
    $_SESSION['logado'] = true;
    $_SESSION['email'] = $usuario['email'];
    $_SESSION['user_id'] = $usuario['id'];
    $_SESSION['nome'] = $usuario['nome'];
    $_SESSION['perfil'] = $usuario['perfil'];
    header("Location: admin.php");
    exit;
} else {
    echo "<script>alert('Acesso negado: E-mail ou senha incorretos.'); window.location.href='login.html';</script>";
}
?>
