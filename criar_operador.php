<?php
if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

$databaseUser = getenv('DB_USER');
$databasePassword = getenv('DB_PASS');
if ($databaseUser === false) {
    $databaseUser = $_ENV['DB_USER'] ?? null;
}
if ($databasePassword === false) {
    $databasePassword = $_ENV['DB_PASS'] ?? null;
}
if (!is_string($databaseUser) || trim($databaseUser) === ''
    || !is_string($databasePassword) || trim($databasePassword) === '') {
    fwrite(STDERR, "Configure DB_USER e DB_PASS no ambiente antes de executar este utilitário.\n");
    exit(1);
}

require_once __DIR__ . '/conexao.php';

$prompt = static function (string $message): string {
    fwrite(STDOUT, $message);
    $input = fgets(STDIN);

    if ($input === false) {
        throw new RuntimeException('Não foi possível ler a entrada do terminal.');
    }

    return rtrim($input, "\r\n");
};

$promptHidden = static function (string $message): string {
    if (!function_exists('exec')) {
        throw new RuntimeException('O PHP precisa permitir o comando stty para ocultar a senha.');
    }

    fwrite(STDOUT, $message);
    exec('stty -echo', $output, $status);
    if ($status !== 0) {
        throw new RuntimeException('Não foi possível ocultar a senha. Execute o script em um terminal interativo.');
    }

    try {
        $password = fgets(STDIN);
    } finally {
        exec('stty echo', $output, $restoreStatus);
        fwrite(STDOUT, PHP_EOL);
    }

    if ($restoreStatus !== 0) {
        throw new RuntimeException('Não foi possível restaurar a exibição do terminal.');
    }
    if ($password === false) {
        throw new RuntimeException('Não foi possível ler a senha do terminal.');
    }

    return rtrim($password, "\r\n");
};

$lockAcquired = false;
$exitStatus = 0;

try {
    $lock = $pdo->query("SELECT GET_LOCK('barraspace_create_operator', 10)");
    if ((int) $lock->fetchColumn() !== 1) {
        throw new RuntimeException('Não foi possível obter o bloqueio para criar a conta. Tente novamente.');
    }
    $lockAcquired = true;

    $existingOperator = (int) $pdo->query("SELECT COUNT(*) FROM gerencia WHERE perfil = 'operador'")->fetchColumn();
    if ($existingOperator > 0) {
        throw new RuntimeException('Já existe uma conta operador. Nenhuma conta foi criada.');
    }

    $email = trim($prompt('E-mail para login da conta operador: '));
    if (strlen($email) > 100 || filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
        throw new RuntimeException('Informe um endereço de e-mail válido com até 100 caracteres.');
    }

    $password = $promptHidden('Senha (mínimo 12 caracteres): ');
    $confirmation = $promptHidden('Confirme a senha: ');

    if (strlen($password) < 12 || strlen($password) > 72) {
        throw new RuntimeException('A senha deve ter entre 12 e 72 bytes. Use uma senha longa e exclusiva.');
    }
    if (!hash_equals($password, $confirmation)) {
        throw new RuntimeException('As senhas não coincidem.');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    unset($password, $confirmation);

    $insert = $pdo->prepare("INSERT INTO gerencia (email, nome, senha, perfil) VALUES (?, 'Operador', ?, 'operador')");
    $insert->execute([$email, $hash]);

    fwrite(STDOUT, "Conta operador criada com privilégios administrativos completos.\n");
} catch (PDOException $e) {
    error_log('[BARRA SPACE] Falha ao criar a conta operador: ' . $e->getMessage());
    fwrite(STDERR, "Erro no banco de dados. Verifique se o e-mail já está em uso e consulte o log do PHP.\n");
    $exitStatus = 1;
} catch (RuntimeException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    $exitStatus = 1;
} finally {
    if ($lockAcquired) {
        try {
            $pdo->query("SELECT RELEASE_LOCK('barraspace_create_operator')");
        } catch (PDOException $e) {
            error_log('[BARRA SPACE] Não foi possível liberar o bloqueio de criação da conta operador: ' . $e->getMessage());
        }
    }
}

exit($exitStatus);
