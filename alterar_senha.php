<?php
require_once __DIR__ . '/auth.php';
require_privileged_admin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$redirect = static function (string $message, string $status): void {
    $_SESSION['password_change_message'] = $message;
    $_SESSION['password_change_status'] = $status;
    header('Location: admin.php?account=1');
    exit;
};

$submittedToken = $_POST['csrf_token'] ?? '';
if (!is_string($submittedToken) || !isset($_SESSION['csrf_token'])
    || !is_string($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
    $redirect('Não foi possível validar a solicitação. Atualize a página e tente novamente.', 'error');
}

$currentPassword = $_POST['current_password'] ?? '';
$newPassword = $_POST['new_password'] ?? '';
$confirmation = $_POST['confirm_password'] ?? '';
if (!is_string($currentPassword) || !is_string($newPassword) || !is_string($confirmation)) {
    $redirect('Preencha todos os campos com valores válidos.', 'error');
}
if (strlen($newPassword) < 12 || strlen($newPassword) > 72) {
    $redirect('A nova senha deve ter entre 12 e 72 bytes.', 'error');
}
if (!hash_equals($newPassword, $confirmation)) {
    $redirect('A confirmação não corresponde à nova senha.', 'error');
}

require_once __DIR__ . '/conexao.php';

try {
    $userId = $_SESSION['user_id'] ?? 0;
    $find = $pdo->prepare("SELECT senha FROM gerencia WHERE id = ? AND perfil IN ('gerencia', 'operador')");
    $find->execute([$userId]);
    $currentHash = $find->fetchColumn();

    if (!is_string($currentHash) || !password_verify($currentPassword, $currentHash)) {
        $redirect('A senha atual está incorreta.', 'error');
    }

    $newHash = password_hash($newPassword, PASSWORD_DEFAULT);
    $update = $pdo->prepare("UPDATE gerencia SET senha = ? WHERE id = ? AND senha = ? AND perfil IN ('gerencia', 'operador')");
    $update->execute([$newHash, $userId, $currentHash]);
    if ($update->rowCount() !== 1) {
        $redirect('A conta foi alterada durante a solicitação. Atualize a página e tente novamente.', 'error');
    }

    session_regenerate_id(true);
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    $redirect('Senha atualizada com sucesso.', 'success');
} catch (PDOException $e) {
    error_log('[BARRA SPACE] Falha ao atualizar a senha da conta: ' . $e->getMessage());
    $redirect('Não foi possível atualizar a senha agora. Tente novamente mais tarde.', 'error');
}
