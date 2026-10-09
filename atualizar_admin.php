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
    $lock = $pdo->query("SELECT GET_LOCK('barraspace_create_first_admin', 10)");
    if ((int) $lock->fetchColumn() !== 1) {
        throw new RuntimeException('Não foi possível obter o bloqueio para alterar a conta. Tente novamente.');
    }
    $lockAcquired = true;

    $currentEmail = trim($prompt('E-mail atual da conta que deseja alterar: '));
    if (filter_var($currentEmail, FILTER_VALIDATE_EMAIL) === false) {
        throw new RuntimeException('Informe um endereço de e-mail atual válido.');
    }

    $find = $pdo->prepare('SELECT id FROM gerencia WHERE email = ?');
    $find->execute([$currentEmail]);
    $adminId = $find->fetchColumn();
    if ($adminId === false) {
        throw new RuntimeException('Não foi encontrada uma conta com esse e-mail. Nenhuma alteração foi feita.');
    }

    $newEmail = trim($prompt('Novo e-mail da conta: '));
    if (strlen($newEmail) > 100 || filter_var($newEmail, FILTER_VALIDATE_EMAIL) === false) {
        throw new RuntimeException('Informe um endereço de e-mail válido com até 100 caracteres.');
    }

    $password = $promptHidden('Nova senha (mínimo 12 caracteres): ');
    $confirmation = $promptHidden('Confirme a nova senha: ');

    if (strlen($password) < 12 || strlen($password) > 72) {
        throw new RuntimeException('A senha deve ter entre 12 e 72 bytes. Use uma senha longa e exclusiva.');
    }
    if (!hash_equals($password, $confirmation)) {
        throw new RuntimeException('As senhas não coincidem.');
    }

    $hash = password_hash($password, PASSWORD_DEFAULT);
    unset($password, $confirmation);

    $update = $pdo->prepare('UPDATE gerencia SET email = ?, senha = ? WHERE id = ?');
    $update->execute([$newEmail, $hash, $adminId]);

    fwrite(STDOUT, "Conta administrativa atualizada com sucesso.\n");
} catch (PDOException $e) {
    error_log('[BARRA SPACE] Falha ao atualizar a conta administrativa: ' . $e->getMessage());
    fwrite(STDERR, "Erro no banco de dados. Verifique se o novo e-mail já está em uso e consulte o log do PHP.\n");
    $exitStatus = 1;
} catch (RuntimeException $e) {
    fwrite(STDERR, $e->getMessage() . PHP_EOL);
    $exitStatus = 1;
} finally {
    if ($lockAcquired) {
        try {
            $pdo->query("SELECT RELEASE_LOCK('barraspace_create_first_admin')");
        } catch (PDOException $e) {
            error_log('[BARRA SPACE] Não foi possível liberar o bloqueio da conta administrativa: ' . $e->getMessage());
        }
    }
}

exit($exitStatus);
