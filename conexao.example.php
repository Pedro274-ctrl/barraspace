<?php
/**
 * Modelo versionado da conexão com o banco.
 * Copie este arquivo para conexao.php e configure as variáveis no ambiente.
 */
$readEnv = static function (string $name): ?string {
    $value = getenv($name);
    if ($value === false) {
        $value = $_ENV[$name] ?? null;
    }

    return is_string($value) ? $value : null;
};

$host = $readEnv('DB_HOST') ?: 'localhost';
$db   = $readEnv('DB_NAME') ?: 'barraspace';
$user = $readEnv('DB_USER');
$pass = $readEnv('DB_PASS');

$respondUnavailable = static function (): void {
    http_response_code(503);

    if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json; charset=UTF-8');
        echo json_encode(['erro' => 'Serviço temporariamente indisponível.'], JSON_UNESCAPED_UNICODE);
        return;
    }

    require __DIR__ . '/503.php';
};

$missingCredentials = [];
if ($user === null || trim($user) === '') {
    $missingCredentials[] = 'DB_USER';
}
if ($pass === null || trim($pass) === '') {
    $missingCredentials[] = 'DB_PASS';
}

if ($missingCredentials !== []) {
    error_log('[BARRA SPACE] Variáveis de ambiente obrigatórias ausentes ou vazias: ' . implode(', ', $missingCredentials));
    $respondUnavailable();
    exit;
}

try {
    $dsn = "mysql:host={$host};dbname={$db};charset=utf8mb4";
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
        PDO::ATTR_STRINGIFY_FETCHES  => false,
        PDO::ATTR_TIMEOUT            => 5,
    ];
    $pdo = new PDO($dsn, $user, $pass, $options);
    $pdo->exec("SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("SET time_zone = '-03:00'");
} catch (PDOException $e) {
    error_log('[BARRA SPACE] Erro crítico de conexão DB: ' . $e->getMessage());
    $respondUnavailable();
    exit;
}
