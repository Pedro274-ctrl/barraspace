<?php
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

if (!in_array($values['categoria'], ['informacoes', 'sugestoes', 'reclamacoes'], true)) {
    $respond(422, ['erro' => 'Selecione uma categoria válida.']);
}

if (($_POST['consentimento'] ?? null) !== 'on') {
    $respond(422, ['erro' => 'É necessário concordar com o uso dos dados para enviar a mensagem.']);
}

$respond(503, ['erro' => 'O recebimento de mensagens está temporariamente indisponível. Tente novamente mais tarde.']);
