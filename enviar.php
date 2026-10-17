<?php
use PHPMailer\PHPMailer\Exception as MailerException;
use PHPMailer\PHPMailer\PHPMailer;

header('Content-Type: application/json; charset=UTF-8');
header('Cache-Control: no-store');

$respond = static function (int $status, array $body): void {
    http_response_code($status);
    echo json_encode($body, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
    exit;
};

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Allow: POST');
    $respond(405, ['erro' => 'Método não permitido.']);
}

$honeypot = $_POST['website'] ?? '';
if (!is_string($honeypot)) {
    $respond(400, ['erro' => 'Solicitação inválida.']);
}
if (trim($honeypot) !== '') {
    $respond(422, ['erro' => 'Solicitação inválida.']);
}

$fields = ['nome', 'email', 'assunto', 'categoria', 'mensagem'];
$values = [];
foreach ($fields as $field) {
    $value = $_POST[$field] ?? null;
    if (!is_string($value)) {
        $respond(422, ['erro' => 'Preencha corretamente todos os campos obrigatórios.']);
    }
    $values[$field] = trim($value);
}

$limits = [
    'nome' => 120,
    'email' => 254,
    'assunto' => 160,
    'categoria' => 32,
    'mensagem' => 5000,
];
foreach ($limits as $field => $limit) {
    if ($values[$field] === '' || strlen($values[$field]) > $limit) {
        $respond(422, ['erro' => 'Preencha corretamente todos os campos obrigatórios.']);
    }
}

if (filter_var($values['email'], FILTER_VALIDATE_EMAIL) === false) {
    $respond(422, ['erro' => 'Informe um endereço de e-mail válido.']);
}

if (preg_match('/[\r\n]/', $values['assunto'])) {
    $respond(422, ['erro' => 'Informe um assunto válido.']);
}

if (!in_array($values['categoria'], ['informacoes', 'sugestoes', 'reclamacoes'], true)) {
    $respond(422, ['erro' => 'Selecione uma categoria válida.']);
}

if (($_POST['consentimento'] ?? null) !== 'on') {
    $respond(422, ['erro' => 'É necessário concordar com o uso dos dados para enviar a mensagem.']);
}

$readConfig = static function (string $name): ?string {
    $value = getenv($name);
    if ($value === false) {
        $value = $_ENV[$name] ?? null;
    }

    return is_string($value) ? $value : null;
};

$requiredConfig = [
    'SMTP_HOST' => $readConfig('SMTP_HOST'),
    'SMTP_USERNAME' => $readConfig('SMTP_USERNAME'),
    'SMTP_PASSWORD' => $readConfig('SMTP_PASSWORD'),
    'SMTP_FROM' => $readConfig('SMTP_FROM'),
];
$missingConfig = [];
foreach ($requiredConfig as $name => $value) {
    if ($value === null || trim($value) === '') {
        $missingConfig[] = $name;
    }
}
if ($missingConfig !== []) {
    error_log('[BARRA SPACE] Configuração SMTP ausente: ' . implode(', ', $missingConfig));
    $respond(503, ['erro' => 'O recebimento de mensagens está temporariamente indisponível. Tente novamente mais tarde.']);
}

$host = trim($requiredConfig['SMTP_HOST']);
$username = trim($requiredConfig['SMTP_USERNAME']);
$password = $requiredConfig['SMTP_PASSWORD'];
$fromAddress = trim($requiredConfig['SMTP_FROM']);
$fromName = trim($readConfig('SMTP_FROM_NAME') ?? 'Barra Space Center');
$recipientList = $readConfig('CONTACT_TO') ?? 'pedro274.phh@gmail.com';
$portValue = $readConfig('SMTP_PORT') ?? '587';
$encryption = strtolower(trim($readConfig('SMTP_ENCRYPTION') ?? 'starttls'));
$port = filter_var($portValue, FILTER_VALIDATE_INT, ['options' => ['min_range' => 1, 'max_range' => 65535]]);
$recipients = array_map('trim', explode(',', $recipientList));

if ($port === false
    || !in_array($encryption, ['starttls', 'smtps'], true)
    || filter_var($fromAddress, FILTER_VALIDATE_EMAIL) === false
    || $recipients === []
    || $fromName === '') {
    error_log('[BARRA SPACE] Configuração SMTP ou de destinatário inválida.');
    $respond(503, ['erro' => 'O recebimento de mensagens está temporariamente indisponível. Tente novamente mais tarde.']);
}
foreach ($recipients as $recipient) {
    if (filter_var($recipient, FILTER_VALIDATE_EMAIL) === false) {
        error_log('[BARRA SPACE] Configuração CONTACT_TO inválida.');
        $respond(503, ['erro' => 'O recebimento de mensagens está temporariamente indisponível. Tente novamente mais tarde.']);
    }
}

require_once __DIR__ . '/vendor/autoload.php';

$categories = [
    'informacoes' => 'Informações',
    'sugestoes' => 'Sugestões',
    'reclamacoes' => 'Reclamações',
];

$mailer = new PHPMailer(true);
$mailer->isSMTP();
$mailer->Host = $host;
$mailer->SMTPAuth = true;
$mailer->Username = $username;
$mailer->Password = $password;
$mailer->Port = $port;
$mailer->SMTPSecure = $encryption === 'smtps'
    ? PHPMailer::ENCRYPTION_SMTPS
    : PHPMailer::ENCRYPTION_STARTTLS;
$mailer->Timeout = 15;
$mailer->CharSet = PHPMailer::CHARSET_UTF8;
$mailer->setFrom($fromAddress, $fromName);
foreach ($recipients as $recipient) {
    $mailer->addAddress($recipient);
}
$mailer->addReplyTo($values['email'], $values['nome']);
$mailer->Subject = '[Barra Space] ' . $categories[$values['categoria']] . ': ' . $values['assunto'];
$mailer->Body = implode("\n", [
    'Nova mensagem recebida pelo formulário do site Barra Space Center.',
    '',
    'Nome: ' . $values['nome'],
    'E-mail: ' . $values['email'],
    'Categoria: ' . $categories[$values['categoria']],
    'Assunto: ' . $values['assunto'],
    '',
    'Mensagem:',
    $values['mensagem'],
]);

try {
    $mailer->send();
} catch (MailerException $e) {
    error_log('[BARRA SPACE] Falha no envio SMTP do formulário de contato: ' . $e->getMessage());
    $respond(503, ['erro' => 'Não foi possível enviar sua mensagem agora. Tente novamente mais tarde.']);
}

$respond(200, ['mensagem' => 'Mensagem enviada com sucesso! Retornaremos em breve.']);
