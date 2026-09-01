<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
    exit;
}

require_once 'conexao.php';

// Filtros
$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING);
$filtro_ativo = filter_input(INPUT_GET, 'filtro_ativo', FILTER_SANITIZE_STRING);

// Query de busca segura (mantém colunas antigas para evitar erro de SQL antes da Fase 2)
if ($busca) {
    // Busca atualizada com as novas colunas do banco
    $stmt = $pdo->prepare("SELECT * FROM condominos WHERE sala_principal LIKE ? OR razao_social LIKE ? OR email LIKE ? OR especialidades LIKE ? ORDER BY sala_principal ASC");
    $termo = "%$busca%";
    $stmt->execute([$termo, $termo, $termo, $termo]);
} else {
    // Ordenação atualizada para sala_principal
    $stmt = $pdo->query("SELECT * FROM condominos ORDER BY sala_principal ASC");
}

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Funções auxiliares para transição segura de colunas (Fase 1 para Fase 2)
$val = function($c, $campo, $fallback = '') {
    return htmlspecialchars($c[$campo] ?? $c[$fallback] ?? '');
};

$is_ativo = function($c) {
    // Se a coluna 'ativo' não existir ainda, assume 1 (Visível)
    return !isset($c['ativo']) || $c['ativo'] == '1';
};

// Aplica o filtro de visibilidade via PHP para evitar quebra no SQL atual
$lista_condominos = array_filter($resultados, function($c) use ($filtro_ativo, $is_ativo) {
    if ($filtro_ativo === '1' && !$is_ativo($c)) {
        return false;
    }

    if ($filtro_ativo === '0' && $is_ativo($c)) {
        return false;
    }

    return true;
});

?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Barra Space Center</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
    <style>
        .badge.publico { background-color: #28a745; color: white; }
        .badge.oculto { background-color: #6c757d; color: white; }
    </style>
</head>
<body>

    <div class="admin-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img src="http://www.barraspace.com.br/wp-content/uploads/2016/12/LogoMarcaTransp80.jpg" alt="Logo">
            </div>
            <ul class="sidebar-menu">
                <li><a href="#" class="active">Dashboard</a></li>
                <li><a href="#">Gestão de Condôminos</a></li>
                <li><a href="#">Centro de Convenções</a></li>
                <li><a href="#">Comunicados</a></li>
                <li><a href="#">Financeiro (Boletos)</a></li>
            </ul>
            <div class="sidebar-footer">
                <a href="logout.php" class="btn-logout">Sair do Sistema</a>
            </div>
        </aside>

        <main class="main-content">
            <header class="admin-header">
                <div class="toggle-btn" id="toggleSidebar">
                    <span></span><span></span><span></span>
                </div>
                <div class="user-info">
                    <span>Olá, <strong>Administração</strong></span>
                    <div class="avatar">A</div>
                </div>
            </header>

            <div class="dashboard-content">
                <h1>Visão Geral</h1>
                
                <div class="cards-grid">
                    <div class="admin-card">
                        <h3>Total de Salas</h3>
                        <div class="number">120</div>
                    </div>
                    <div class="admin-card">
                        <h3>Salas Ocupadas / Listadas</h3>
                        <div class="number"><?= count($lista_condominos) ?></div>
                    </div>
                    <div class="admin-card">
                        <h3>Reservas (Convenções)</h3>
                        <div class="number text-blue">4 Hoje</div>
                    </div>
                </div>

                <div class="table-header" style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 15px; margin-bottom: 20px;">
                    <h2>Lista de Condôminos</h2>
                    
                    <!-- Formulário de Busca e Filtro -->
                    <form action="admin.php" method="GET" style="display: flex; gap: 8px; flex-grow: 1; max-width: 650px;">
                        
                        <label for="filtro_ativo" style="position:absolute; left:-9999px;">Filtrar por visibilidade</label>
                        <select id="filtro_ativo" name="filtro_ativo" style="padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px; background: #fff;">
                            <option value="">Todos (Site)</option>
                            <option value="1" <?= $filtro_ativo === '1' ? 'selected' : '' ?>>Somente Visíveis no Site</option>
                            <option value="0" <?= $filtro_ativo === '0' ? 'selected' : '' ?>>Ocultos no Site</option>
                        </select>

                        <label for="busca" style="position:absolute; left:-9999px;">Buscar</label>
                        <input id="busca" type="text" name="busca" placeholder="Buscar por sala, empresa ou e-mail..." value="<?= htmlspecialchars($busca ?? '') ?>" style="flex-grow: 1; padding: 8px 12px; border: 1px solid #ccc; border-radius: 4px;">
                        
                        <button type="submit" style="background-color: #0b3d91; color: #fff; border: none; padding: 8px 15px; border-radius: 4px; cursor: pointer; font-weight: bold;">Buscar</button>
                        
                        <?php if (!empty($busca) || $filtro_ativo !== ''): ?>
                            <a href="admin.php" style="background-color: #dc3545; color: #fff; padding: 8px 15px; text-decoration: none; border-radius: 4px; display: flex; align-items: center; font-weight: bold;">Limpar</a>
                        <?php endif; ?>
                    </form>

                    <a href="novo_condomino.php" class="btn-new" style="text-decoration: none; display: inline-block;">+ Novo Cadastro</a>
                </div>
                
                <table class="admin-table">
                    <thead>
                        <tr>
                            <th>Sala</th>
                            <th>Empresa / Responsável</th>
                            <th>Guia Público</th>
                            <th>Status Gerencial</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($lista_condominos as $condomino): ?>
                        <tr>
                            <!-- Aplicação das funções auxiliares para mesclar estrutura antiga e nova -->
                            <td>
                                <strong><?= $val($condomino, 'sala_principal', 'sala') ?></strong>
                                <?php if ($val($condomino, 'salas')): ?>
                                    <br><small style="color:#666;"><?= $val($condomino, 'salas') ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?= $val($condomino, 'razao_social', 'empresa') ?>
                                <?php if ($val($condomino, 'responsavel')): ?>
                                    <br><small style="color:#666;">Resp: <?= $val($condomino, 'responsavel') ?></small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($is_ativo($condomino)): ?>
                                    <span class="badge publico">Visível no Site</span>
                                <?php else: ?>
                                    <span class="badge oculto">Oculto</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($val($condomino, 'status') == 'Adimplente'): ?>
                                    <span class="badge active">Adimplente</span>
                                <?php else: ?>
                                    <span class="badge warning">Pendente</span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <a href="editar.php?id=<?= $condomino['id'] ?>" class="btn-action edit" style="text-decoration:none;">Editar</a>
                                <a href="excluir.php?id=<?= $condomino['id'] ?>" class="btn-action delete" style="text-decoration:none;" onclick="return confirm('Tem certeza que deseja remover?');">Remover</a>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        
                        <?php if (count($lista_condominos) === 0): ?>
                        <tr>
                            <td colspan="5" style="text-align: center; padding: 20px;">Nenhum registro encontrado.</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

    <script src="admin.js"></script>
</body>
</html>
