<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
    exit;
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Novo Cadastro - Barra Space Center</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        .campo-grupo { margin-bottom: 15px; }
        .label-padrao { display: block; font-weight: bold; margin-bottom: 5px; }
        .input-padrao { width: 100%; padding: 10px; border: 1px solid #ccc; border-radius: 4px; box-sizing: border-box; }
        .chk-privacidade { font-size: 0.85rem; color: #666; display: flex; align-items: center; gap: 6px; margin-top: 4px; }
        .chk-privacidade input { margin: 0; cursor: pointer; }
    </style>
</head>
<body style="background-color: #f4f6f9; margin: 0; padding: 20px 0;">
    <div class="admin-wrapper" style="display: flex; justify-content: center; align-items: center; min-height: 100vh;">
        <div class="table-container" style="max-width: 600px; width: 100%; padding: 30px; background: #fff; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
            <div style="text-align: center; margin-bottom: 25px;">
                <img src="http://www.barraspace.com.br/wp-content/uploads/2016/12/LogoMarcaTransp80.jpg" alt="Logo" style="height: 50px;">
                <h2 style="color: #0b3d91; margin-top: 10px;">Novo Condômino</h2>
            </div>
            
            <form action="salvar_condomino.php" method="POST">
                
                <!-- Chave Geral de Visibilidade -->
                <div style="margin-bottom: 20px; display: flex; align-items: center; justify-content: space-between; background: #e8f0fe; padding: 12px 15px; border-radius: 4px; border-left: 4px solid #0b3d91;">
                    <label for="ativo" style="font-weight:bold; margin:0; color: #0b3d91; cursor:pointer;">Exibir condômino no site público? (Ativo)</label>
                    <input type="checkbox" id="ativo" name="ativo" value="1" checked style="transform: scale(1.3); cursor:pointer;">
                </div>

                <div class="campo-grupo">
                    <label for="razao_social" class="label-padrao">Condômino / Empresa *</label>
                    <input type="text" id="razao_social" name="razao_social" required class="input-padrao">
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label for="andar" class="label-padrao">Andar *</label>
                        <select id="andar" name="andar" required class="input-padrao">
                            <option value="">Selecione...</option>
                            <option value="Sobre Loja">Sobre Loja</option>
                            <option value="1">1º Andar</option>
                            <option value="2">2º Andar</option>
                            <option value="3">3º Andar</option>
                            <option value="4">4º Andar</option>
                            <option value="5">5º Andar</option>
                            <option value="6">6º Andar</option>
                            <option value="7">7º Andar</option>
                            <option value="8">8º Andar</option>
                            <option value="9">9º Andar</option>
                            <option value="10">10º Andar</option>
                            <option value="11">11º Andar</option>
                            <option value="12">12º Andar</option>
                            <option value="13">13º Andar</option>
                            <option value="14">14º Andar</option>
                            <option value="15">15º Andar</option>
                            <option value="16">16º Andar</option>
                            <option value="17">17º Andar</option>
                            <option value="18">18º Andar</option>
                            <option value="19">19º Andar</option>
                            <option value="20">20º Andar</option>
                            <option value="21">21º Andar</option>
                            

                            <!-- Adicione os demais andares conforme necessário -->
                        </select>
                    </div>
                    <div style="flex: 1;">
                        <label for="sala_principal" class="label-padrao">Sala Principal *</label>
                        <input type="text" id="sala_principal" name="sala_principal" placeholder="Ex: 0203" required class="input-padrao">
                    </div>
                </div>

                <div class="campo-grupo">
                    <label for="salas" class="label-padrao">Sala(s) (Grupo)</label>
                    <input type="text" id="salas" name="salas" placeholder="Ex: Sala 203 / 204" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="checkbox" name="chk_salas" value="1" checked> Tornar público no site
                    </label>
                </div>

                <div style="display: flex; gap: 15px; margin-bottom: 15px;">
                    <div style="flex: 1;">
                        <label for="atividade" class="label-padrao">Atividade</label>
                        <input type="text" id="atividade" name="atividade" class="input-padrao">
                        <label class="chk-privacidade">
                            <input type="checkbox" name="chk_atividade" value="1"> Tornar público
                        </label>
                    </div>
                    <div style="flex: 1;">
                        <label for="especialidades" class="label-padrao">Especialidades</label>
                        <input type="text" id="especialidades" name="especialidades" class="input-padrao">
                        <label class="chk-privacidade">
                            <input type="checkbox" name="chk_especialidades" value="1" checked> Tornar público
                        </label>
                    </div>
                </div>

                <div class="campo-grupo">
                    <label for="telefones" class="label-padrao">Telefone(s)</label>
                    <input type="text" id="telefones" name="telefones" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="checkbox" name="chk_telefones" value="1" checked> Tornar público no site
                    </label>
                </div>

                <div class="campo-grupo">
                    <label for="responsavel" class="label-padrao">Responsável</label>
                    <input type="text" id="responsavel" name="responsavel" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="checkbox" name="chk_responsavel" value="1"> Tornar público no site
                    </label>
                </div>

                <div class="campo-grupo">
                    <label for="email" class="label-padrao">E-mail *</label>
                    <input type="email" id="email" name="email" required class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="checkbox" name="chk_email" value="1" checked> Tornar público no site
                    </label>
                </div>

                <div class="campo-grupo">
                    <label for="url" class="label-padrao">Site (URL)</label>
                    <input type="text" id="url" name="url" placeholder="ex: www.empresa.com.br" class="input-padrao">
                    <label class="chk-privacidade">
                        <input type="checkbox" name="chk_url" value="1" checked> Tornar público no site
                    </label>
                </div>

                <div class="campo-grupo" style="margin-top: 20px;">
                    <label for="status" class="label-padrao">Status Interno (Gerencial)</label>
                    <select id="status" name="status" class="input-padrao">
                        <option value="Adimplente">Adimplente</option>
                        <option value="Pendente">Pendente</option>
                    </select>
                </div>
    
                <button type="submit" style="width: 100%; background: #28a745; color: #fff; border: none; padding: 14px; border-radius: 4px; font-weight: bold; cursor: pointer; font-size: 1.1rem; margin-top: 10px;">Salvar Cadastro</button>
                <div style="text-align: center; margin-top: 20px;">
                    <a href="admin.php" style="color: #666; text-decoration: none; font-weight: bold;">← Cancelar e Voltar para Visão Geral</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>

