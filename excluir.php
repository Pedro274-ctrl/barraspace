<?php
require_once __DIR__ . '/auth.php';
require_privileged_admin();

require_once 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if ($id) {
    try {
        $stmt = $pdo->prepare("DELETE FROM condominos WHERE id = ?");
        $stmt->execute([$id]);
    } catch (PDOException $e) {
        die("Erro ao excluir: " . $e->getMessage());
    }
}

header("Location: admin.php");
exit;
