<?php
require_once __DIR__ . '/auth.php';
require_user_management_access();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    header('Allow: POST');
    exit;
}

$redirect = static function (string $message, string $status): void {
    $_SESSION['user_creation_message'] = $message;
    $_SESSION['user_creation_status'] = $status;
    header('Location: admin.php?account=1&security_section=users');
    exit;
};

$submittedToken = $_POST['csrf_token'] ?? '';
if (!is_string($submittedToken) || !isset($_SESSION['csrf_token'])
    || !is_string($_SESSION['csrf_token'])
    || !hash_equals($_SESSION['csrf_token'], $submittedToken)) {
    $redirect('Não foi possível validar a solicitação. Atualize a página e tente novamente.', 'error');
}

$name = $_POST['nome'] ?? null;
$email = $_POST['email'] ?? null;
$password = $_POST['senha'] ?? null;
$confirmation = $_POST['confirmacao_senha'] ?? null;
if (!is_string($name) || !is_string($email) || !is_string($password) || !is_string($confirmation)) {
    $redirect('Preencha corretamente todos os campos obrigatórios.', 'error');
}

$name = trim($name);
$email = trim($email);
if (!mb_check_encoding($name, 'UTF-8') || $name === '' || mb_strlen($name, 'UTF-8') > 100) {
    $redirect('Informe um nome com até 100 caracteres.', 'error');
}
if (strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
    $redirect('Informe um endereço de e-mail válido com até 100 caracteres.', 'error');
}
if (strlen($password) < 12 || strlen($password) > 72) {
    $redirect('A senha deve ter entre 12 e 72 bytes.', 'error');
}
if (!hash_equals($password, $confirmation)) {
    $redirect('A confirmação não corresponde à senha.', 'error');
}

$passwordHash = password_hash($password, PASSWORD_DEFAULT);
unset($password, $confirmation);

require_once __DIR__ . '/conexao.php';

try {
    $insert = $pdo->prepare("INSERT INTO gerencia (email, nome, senha, perfil) VALUES (?, ?, ?, 'usuario')");
    $insert->execute([$email, $name, $passwordHash]);
} catch (PDOException $e) {
    if ((int) ($e->errorInfo[1] ?? 0) === 1062) {
        $redirect('Já existe uma conta cadastrada com esse e-mail.', 'error');
    }

    error_log('[BARRA SPACE] Falha ao cadastrar usuário: ' . $e->getMessage());
    $redirect('Não foi possível cadastrar o usuário agora. Tente novamente mais tarde.', 'error');
}

$redirect('Usuário cadastrado com sucesso. Ele poderá acessar somente a gestão de condôminos.', 'success');
