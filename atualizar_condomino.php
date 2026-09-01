<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
    exit;
}

require_once 'conexao.php';

// Validação do ID obrigatório[cite: 5]
$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);

// Captura de campos de texto renomeados e novos
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

// Captura das variáveis booleanas de privacidade (checkboxes)
// Os inputs hidden que criamos no editar.php enviam '0' caso o checkbox não seja marcado.
$ativo              = (isset($_POST['ativo']) && $_POST['ativo'] == '1') ? 1 : 0;
$chk_salas          = (isset($_POST['chk_salas']) && $_POST['chk_salas'] == '1') ? 1 : 0;
$chk_atividade      = (isset($_POST['chk_atividade']) && $_POST['chk_atividade'] == '1') ? 1 : 0;
$chk_especialidades = (isset($_POST['chk_especialidades']) && $_POST['chk_especialidades'] == '1') ? 1 : 0;
$chk_telefones      = (isset($_POST['chk_telefones']) && $_POST['chk_telefones'] == '1') ? 1 : 0;
$chk_responsavel    = (isset($_POST['chk_responsavel']) && $_POST['chk_responsavel'] == '1') ? 1 : 0;
$chk_email          = (isset($_POST['chk_email']) && $_POST['chk_email'] == '1') ? 1 : 0;
$chk_url            = (isset($_POST['chk_url']) && $_POST['chk_url'] == '1') ? 1 : 0;

// Validação dos novos campos obrigatórios
if ($id && $razao_social && $andar && $sala_principal && $email) {
    try {
        $sql = "UPDATE condominos SET
            ativo = ?, razao_social = ?, andar = ?, sala_principal = ?, salas = ?, chk_salas = ?,
            atividade = ?, chk_atividade = ?, especialidades = ?, chk_especialidades = ?,
            telefones = ?, chk_telefones = ?, responsavel = ?, chk_responsavel = ?,
            email = ?, chk_email = ?, url = ?, chk_url = ?, status = ?
            WHERE id = ?";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $ativo, $razao_social, $andar, $sala_principal, $salas, $chk_salas,
            $atividade, $chk_atividade, $especialidades, $chk_especialidades,
            $telefones, $chk_telefones, $responsavel, $chk_responsavel,
            $email, $chk_email, $url, $chk_url, $status,
            $id
        ]);
        
        header("Location: admin.php");
        exit;
    } catch (PDOException $e) {
        die("Erro ao atualizar: " . $e->getMessage()); //[cite: 5]
    }
} else {
    // Retorno com alerta detalhado[cite: 5]
    echo "<script>alert('Dados inválidos ou campos obrigatórios ausentes.'); window.location.href='editar.php?id=" . $id . "';</script>";
}
