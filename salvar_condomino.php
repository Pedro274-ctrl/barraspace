<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
    exit;
}

require 'conexao.php';

// Recebe e limpa os dados enviados pelo formulário
$sala = filter_input(INPUT_POST, 'sala', FILTER_SANITIZE_STRING);
$empresa = filter_input(INPUT_POST, 'empresa', FILTER_SANITIZE_STRING);
$email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$status = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

// Verifica se os dados principais não estão vazios
if ($sala && $empresa && $email) {
    try {
        $stmt = $pdo->prepare("INSERT INTO condominos (sala, empresa, email, status) VALUES (?, ?, ?, ?)");
        $stmt->execute([$sala, $empresa, $email, $status]);
        
        // Redireciona de volta ao painel após salvar
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao salvar no banco: " . $e->getMessage());
    }
} else {
    echo "<script>alert('Por favor, preencha todos os campos obrigatórios.'); window.location.href='novo_condomino.php';</script>";
}
?>