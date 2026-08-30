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
</head>
<body>
    <div class="admin-wrapper">
        <main class="main-content" style="width: 100%; padding: 40px; display: flex; justify-content: center;">
            <div class="table-container" style="max-width: 600px; width: 100%;">
                <h2 style="margin-bottom: 20px; color: #0b3d91;">Cadastrar Novo Condômino</h2>
                
                <form action="salvar_condomino.php" method="POST">
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Número da Sala</label>
                        <input type="text" name="sala" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Nome da Empresa / Responsável</label>
                        <input type="text" name="empresa" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 15px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">E-mail de Contato</label>
                        <input type="email" name="email" required style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
                    </div>
                    <div class="form-group" style="margin-bottom: 25px;">
                        <label style="display:block; margin-bottom:5px; font-weight:bold;">Status Financeiro</label>
                        <select name="status" style="width:100%; padding:10px; border:1px solid #ccc; border-radius:4px;">
                            <option value="Adimplente">Adimplente</option>
                            <option value="Pendente">Pendente</option>
                        </select>
                    </div>
                    
                    <button type="submit" class="btn-new" style="width: 100%; font-size: 1rem; padding: 12px;">Salvar Cadastro</button>
                    <div style="text-align: center; margin-top: 15px;">
                        <a href="admin.php" style="color: #666; text-decoration: none;">← Voltar para o Painel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>