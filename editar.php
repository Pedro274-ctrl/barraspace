<?php
require_once __DIR__ . '/auth.php';
require_authenticated_user();

require_once 'conexao.php';

$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
if (!$id) {
    header("Location: admin.php");
    exit;
}

$stmt = $pdo->prepare("SELECT * FROM condominos WHERE id = ?");
$stmt->execute([$id]);
$condomino = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$condomino) {
    header("Location: admin.php");
    exit;
}

// Funções auxiliares para evitar erros se as colunas novas ainda não existirem no banco
$val = function($campo, $fallback = '') use ($condomino) {
    return htmlspecialchars($condomino[$campo] ?? $condomino[$fallback] ?? '');
};

$chk = function($campo) use ($condomino) {
    // Se a coluna não existir ainda, assume '1' (marcado) por padrão para não ocultar dados antigos sem querer
    return (!isset($condomino[$campo]) || $condomino[$campo] == '1') ? 'checked' : '';
};
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Cadastro - Barra Space Center</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        .campo-grupo { margin-bottom: 15px; }
        .label-padrao { display: block; font-weight: bold; margin-bottom: 5px; }
        .input-padrao { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .chk-privacidade { font-size: 0.85rem; color: #666; display: flex; align-items: center; gap: 6px; margin-top: 4px; }
        .chk-privacidade input { margin: 0; cursor: pointer; }
    </style>
</head>
<body style="background-color: #f4f6f9; font-family: 'Roboto', sans-serif; margin: 0; padding: 20px 0;">
    <div style="display: flex; justify-content: center; align-items: center; min-height: 100vh; padding: 20px;">
        <div style="max-width: 600px; width: 100%; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="text-align: center; margin-bottom: 25px;">
                <h2 style="color: #17a2b8; margin-top: 10px;">Editar Condômino</h2>
            </div>
            
            <form action="atualizar_condomino.php" method="POST">
                <input type="hidden" name="id" value="<?= $condomino['id'] ?>">
                
                <!-- Chave Geral de Visibilidade -->
                <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; background: #e8f0fe; padding: 12px 15px; border-radius: 4px; border-left: 4px solid #17a2b8;">
                    <label for="ativo" style="font-weight:bold; margin:0; color: #17a2b8; cursor:pointer;">Exibir condômino no site público? (Ativo)</label>
                    <!-- Envia 0 se desmarcado, 1 se marcado -->
                    <input type="hidden" name="ativo" value="0">
                    <input type="checkbox" id="ativo" name="ativo" value="1" <?= $chk('ativo') ?> style="transform: scale(1.3); cursor:pointer;">
                </div>

                <div class="campo-grupo">
                    <label for="razao_social" class="label-padrao">Condômino / Empresa *</label>
                    <input type="text" id="razao_social" name="razao_social" required value="<?= $val('razao_social', 'empresa') ?>" class="input-padrao">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label for="andar" class="label-padrao">Andar *</label>
                        <?php $andar_atual = $condomino['andar'] ?? ''; ?>
                        <select id="andar" name="andar" required class="input-padrao">
                            <option value="">Selecione...</option>
                            <option value="Sobre Loja" <?= $andar_atual == 'Sobre Loja' ? 'selected' : '' ?>>Sobre Loja</option>
                            <option value="1" <?= $andar_atual == '1' ? 'selected' : '' ?>>1º Andar</option>
                            <option value="2" <?= $andar_atual == '2' ? 'selected' : '' ?>>2º Andar</option>
                            <option value="3" <?= $andar_atual == '3' ? 'selected' : '' ?>>3º Andar</option>
                            <option value="4" <?= $andar_atual == '4' ? 'selected' : '' ?>>4º Andar</option>
                            <option value="5" <?= $andar_atual == '5' ? 'selected' : '' ?>>5º Andar</option>
                            <option value="6" <?= $andar_atual == '6' ? 'selected' : '' ?>>6º Andar</option>
                            <option value="7" <?= $andar_atual == '7' ? 'selected' : '' ?>>7º Andar</option>
                            <option value="8" <?= $andar_atual == '8' ? 'selected' : '' ?>>8º Andar</option>
                            <option value="9" <?= $andar_atual == '9' ? 'selected' : '' ?>>9º Andar</option>
                            <option value="10" <?= $andar_atual == '10' ? 'selected' : '' ?>>10º Andar</option>
                            <option value="11" <?= $andar_atual == '11' ? 'selected' : '' ?>>11º Andar</option>
                            <option value="12" <?= $andar_atual == '12' ? 'selected' : '' ?>>12º Andar</option>
                            <option value="13" <?= $andar_atual == '13' ? 'selected' : '' ?>>13º Andar</option>
                            <option value="14" <?= $andar_atual == '14' ? 'selected' : '' ?>>14º Andar</option>
                            <option value="15" <?= $andar_atual == '15' ? 'selected' : '' ?>>15º Andar</option>
                            <option value="16" <?= $andar_atual == '16' ? 'selected' : '' ?>>16º Andar</option>
                            <option value="17" <?= $andar_atual == '17' ? 'selected' : '' ?>>17º Andar</option>
                            <option value="18" <?= $andar_atual == '18' ? 'selected' : '' ?>>18º Andar</option>
                            <option value="19" <?= $andar_atual == '19' ? 'selected' : '' ?>>19º Andar</option>
                            <option value="20" <?= $andar_atual == '20' ? 'selected' : '' ?>>20º Andar</option>
                            <option value="21" <?= $andar_atual == '21' ? 'selected' : '' ?>>21º Andar</option>
                            <!-- Adapte os andares conforme necessidade -->
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label for="sala_principal" class="label-padrao">Sala Principal *</label>
                        <input type="text" id="sala_principal" name="sala_principal" required value="<?= $val('sala_principal') ?>" class="input-padrao">
                    </div>
                </div>

                <div class="campo-grupo">
                    <label for="salas" class="label-padrao">Sala(s) (Grupo)</label>
                    <input type="text" id="salas" name="salas" value="<?= $val('salas', 'sala') ?>" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="hidden" name="chk_salas" value="0">
                        <input type="checkbox" name="chk_salas" value="1" <?= $chk('chk_salas') ?>> Tornar público no site
                    </label>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label for="atividade" class="label-padrao">Atividade</label>
                        <input type="text" id="atividade" name="atividade" value="<?= $val('atividade') ?>" class="input-padrao">
                        <label class="chk-privacidade">
                            <input type="hidden" name="chk_atividade" value="0">
                            <input type="checkbox" name="chk_atividade" value="1" <?= $chk('chk_atividade') ?>> Tornar público
                        </label>
                    </div>
                    <div style="flex: 1;">
                        <label for="especialidades" class="label-padrao">Especialidades</label>
                        <input type="text" id="especialidades" name="especialidades" value="<?= $val('especialidades', 'especialidade') ?>" class="input-padrao">
                        <label class="chk-privacidade">
                            <input type="hidden" name="chk_especialidades" value="0">
                            <input type="checkbox" name="chk_especialidades" value="1" <?= $chk('chk_especialidades') ?>> Tornar público
                        </label>
                    </div>
                </div>

                <div class="campo-grupo">
                    <label for="telefones" class="label-padrao">Telefone(s)</label>
                    <input type="text" id="telefones" name="telefones" value="<?= $val('telefones', 'telefone') ?>" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="hidden" name="chk_telefones" value="0">
                        <input type="checkbox" name="chk_telefones" value="1" <?= $chk('chk_telefones') ?>> Tornar público no site
                    </label>
                </div>

                <div class="campo-grupo">
                    <label for="responsavel" class="label-padrao">Responsável</label>
                    <input type="text" id="responsavel" name="responsavel" value="<?= $val('responsavel') ?>" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="hidden" name="chk_responsavel" value="0">
                        <input type="checkbox" name="chk_responsavel" value="1" <?= $chk('chk_responsavel') ?>> Tornar público no site
                    </label>
                </div>

                <div class="campo-grupo">
                    <label for="email" class="label-padrao">E-mail *</label>
                    <input type="email" id="email" name="email" required value="<?= $val('email') ?>" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="hidden" name="chk_email" value="0">
                        <input type="checkbox" name="chk_email" value="1" <?= $chk('chk_email') ?>> Tornar público no site
                    </label>
                </div>

                <div class="campo-grupo">
                    <label for="url" class="label-padrao">Site (URL)</label>
                    <input type="text" id="url" name="url" value="<?= $val('url', 'site') ?>" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="hidden" name="chk_url" value="0">
                        <input type="checkbox" name="chk_url" value="1" <?= $chk('chk_url') ?>> Tornar público no site
                    </label>
                </div>

                <div class="campo-grupo" style="margin-top: 20px;">
                    <label for="status" class="label-padrao">Status Interno (Gerencial)</label>
                    <?php $status_atual = $condomino['status'] ?? ''; ?>
                    <select id="status" name="status" class="input-padrao">
                        <option value="Adimplente" <?= $status_atual == 'Adimplente' ? 'selected' : '' ?>>Adimplente</option>
                        <option value="Pendente" <?= $status_atual == 'Pendente' ? 'selected' : '' ?>>Pendente</option>
                    </select>
                </div>
                
                <button type="submit" style="width: 100%; background: #17a2b8; color: #fff; border: none; padding: 14px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; margin-top: 10px;">Atualizar Dados</button>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="admin.php" style="color: #666; text-decoration: none; font-weight: bold;">← Cancelar e Voltar</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
