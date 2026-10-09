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

    header('Content-Type: text/html; charset=UTF-8');
    echo '<!DOCTYPE html><html lang="pt-BR"><head><meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Barra Space - Manutenção</title><style>body{font-family:sans-serif;background:#0A1020;color:#FCFAF7;display:flex;align-items:center;justify-content:center;min-height:100vh;margin:0}.box{text-align:center;padding:24px}</style></head><body><div class="box"><h1>Barra Space Center</h1><p>Serviço temporariamente indisponível. Tente novamente em instantes.</p></div></body></html>';
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
