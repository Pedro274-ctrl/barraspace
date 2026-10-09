<?php
require_once __DIR__ . '/auth.php';
require_privileged_admin();

require_once 'conexao.php';

// Captura de campos de texto nativos[cite: 4]
$razao_social   = filter_input(INPUT_POST, 'razao_social', FILTER_SANITIZE_STRING);
$andar          = filter_input(INPUT_POST, 'andar', FILTER_SANITIZE_STRING);
$sala_principal = filter_input(INPUT_POST, 'sala_principal', FILTER_SANITIZE_STRING);
$salas          = filter_input(INPUT_POST, 'salas', FILTER_SANITIZE_STRING);
$atividade      = filter_input(INPUT_POST, 'atividade', FILTER_SANITIZE_STRING);
$especialidades = filter_input(INPUT_POST, 'especialidades', FILTER_SANITIZE_STRING);
$telefones      = filter_input(INPUT_POST, 'telefones', FILTER_SANITIZE_STRING);
$responsavel    = filter_input(INPUT_POST, 'responsavel', FILTER_SANITIZE_STRING);
$email          = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
$url            = filter_input(INPUT_POST, 'url', FILTER_SANITIZE_STRING);
$status         = filter_input(INPUT_POST, 'status', FILTER_SANITIZE_STRING);

// Captura das variáveis booleanas (checkboxes). Se não existir no POST, assume 0 (falso).
$ativo              = isset($_POST['ativo']) ? 1 : 0;
$chk_salas          = isset($_POST['chk_salas']) ? 1 : 0;
$chk_atividade      = isset($_POST['chk_atividade']) ? 1 : 0;
$chk_especialidades = isset($_POST['chk_especialidades']) ? 1 : 0;
$chk_telefones      = isset($_POST['chk_telefones']) ? 1 : 0;
$chk_responsavel    = isset($_POST['chk_responsavel']) ? 1 : 0;
$chk_email          = isset($_POST['chk_email']) ? 1 : 0;
$chk_url            = isset($_POST['chk_url']) ? 1 : 0;

// Validação dos campos obrigatórios atualizados
if ($razao_social && $andar && $sala_principal && $email) {
    try {
        $sql = "INSERT INTO condominos (
            ativo, razao_social, andar, sala_principal, salas, chk_salas,
            atividade, chk_atividade, especialidades, chk_especialidades,
            telefones, chk_telefones, responsavel, chk_responsavel,
            email, chk_email, url, chk_url, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $ativo, $razao_social, $andar, $sala_principal, $salas, $chk_salas,
            $atividade, $chk_atividade, $especialidades, $chk_especialidades,
            $telefones, $chk_telefones, $responsavel, $chk_responsavel,
            $email, $chk_email, $url, $chk_url, $status
        ]);
        
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao salvar: " . $e->getMessage()); //[cite: 4]
    }
} else {
    echo "<script>alert('Preencha os campos obrigatórios (Empresa, Andar, Sala Principal e E-mail).'); window.location.href='novo_condomino.php';</script>";
}
