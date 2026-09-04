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
$exportar = filter_input(INPUT_GET, 'export', FILTER_SANITIZE_STRING);

// Variável de controle para manter a aba correta aberta após um filtro
$is_search = (!empty($busca) || $filtro_ativo !== '') ? true : false;

// Query de busca segura
if ($busca) {
    $stmt = $pdo->prepare("SELECT * FROM condominos WHERE sala_principal LIKE ? OR razao_social LIKE ? OR email LIKE ? OR especialidades LIKE ? ORDER BY sala_principal ASC");
    $termo = "%$busca%";
    $stmt->execute([$termo, $termo, $termo, $termo]);
} else {
    $stmt = $pdo->query("SELECT * FROM condominos ORDER BY sala_principal ASC");
}

$resultados = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Funções auxiliares para transição segura de colunas
$val = function($c, $campo, $fallback = '') {
    return htmlspecialchars($c[$campo] ?? $c[$fallback] ?? '');
};

$is_ativo = function($c) {
    return !isset($c['ativo']) || $c['ativo'] == '1';
};

// Aplica o filtro de visibilidade
$lista_condominos = array_filter($resultados, function($c) use ($filtro_ativo, $is_ativo) {
    if ($filtro_ativo === '1' && !$is_ativo($c)) return false;
    if ($filtro_ativo === '0' && $is_ativo($c)) return false;
    return true;
});

// LÓGICA DE EXPORTAÇÃO PARA EXCEL (Inserida de forma cirúrgica)
if ($exportar === 'excel') {
    header("Content-Type: application/vnd.ms-excel; charset=utf-8");
    header("Content-Disposition: attachment; filename=Relatorio_Condominos_BarraSpace.xls");
    header("Pragma: no-cache");
    header("Expires: 0");
    
    echo '<html xmlns:x="urn:schemas-microsoft-com:office:excel">';
    echo '<head><meta charset="UTF-8"></head><body>';
    echo '<table border="1" style="font-family: Arial, sans-serif;">';
    echo '<tr style="background-color: #0b3d91; color: #ffffff;">';
    echo '<th>Sala(s)</th><th>Empresa / Razão Social</th><th>Responsável</th><th>Guia Público (Site)</th><th>Status Gerencial</th>';
    echo '</tr>';
    
    foreach ($lista_condominos as $condomino) {
        $sala_texto = $val($condomino, 'sala_principal', 'sala') . ' ' . $val($condomino, 'salas');
        $empresa = $val($condomino, 'razao_social', 'empresa');
        $responsavel = $val($condomino, 'responsavel') ?: 'N/A';
        $status_site = $is_ativo($condomino) ? 'Visível' : 'Oculto';
        $status_gerencial = $val($condomino, 'status') == 'Adimplente' ? 'Adimplente' : 'Pendente';
        
        echo "<tr>";
        echo "<td>{$sala_texto}</td><td>{$empresa}</td><td>{$responsavel}</td><td>{$status_site}</td><td>{$status_gerencial}</td>";
        echo "</tr>";
    }
    echo '</table></body></html>';
    exit; // Interrompe a execução para não imprimir o HTML do painel no Excel
}
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Barra Space Center</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
</head>
<body>

    <div class="admin-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img src="http://www.barraspace.com.br/wp-content/uploads/2016/12/LogoMarcaTransp80.jpg" alt="Logo Barra Space">
            </div>
            <ul class="sidebar-menu">
                <!-- Classes 'active' e 'menu-link' com data-target controlam a navegação no JS -->
                <li><a href="#" class="menu-link <?= !$is_search ? 'active' : '' ?>" data-target="view-dashboard"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="#" class="menu-link <?= $is_search ? 'active' : '' ?>" data-target="view-condominos"><i class="fas fa-users"></i> Gestão de Condôminos</a></li>
                <li><a href="#"><i class="fas fa-handshake"></i> Centro de Convenções</a></li>
                <li><a href="#"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Financeiro (Boletos)</a></li>
            </ul>
        </aside>

        <main class="main-content">
            <header class="admin-header">
                <div class="toggle-btn" id="toggleSidebar">
                    <span></span><span></span><span></span>
                </div>
                
                <!-- Usuário e Botão de Sair Movidos para o Canto Superior Direito -->
                <div class="user-actions">
                    <div class="user-info">
                        <div class="avatar">A</div>
                        <div class="user-details">
                            <span class="greeting">Olá, <strong>Administração</strong></span>
                            <span class="role">Gerente</span>
                        </div>
                    </div>
                    <a href="logout.php" class="btn-logout-header" title="Sair do Sistema">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </header>

            <div class="dashboard-content">
                
                <!-- VISTA 1: DASHBOARD (Visão Geral) -->
                <div id="view-dashboard" class="view-section <?= $is_search ? 'hidden' : '' ?>">
                    <div class="header-title fade-in">
                        <h1>Visão Geral do Complexo</h1>
                        <p>Acompanhe os indicadores em tempo real.</p>
                    </div>
                    
                    <div class="cards-grid fade-in">
                        <div class="admin-card lux-card">
                            <div class="card-icon"><i class="fas fa-building"></i></div>
                            <div class="card-data">
                                <h3>Total de Salas</h3>
                                <div class="number">120</div>
                            </div>
                        </div>
                        <div class="admin-card lux-card">
                            <div class="card-icon"><i class="fas fa-door-open"></i></div>
                            <div class="card-data">
                                <h3>Salas Ocupadas</h3>
                                <div class="number"><?= count($lista_condominos) ?></div>
                            </div>
                        </div>
                        <div class="admin-card lux-card highlight">
                            <div class="card-icon"><i class="fas fa-calendar-check"></i></div>
                            <div class="card-data">
                                <h3>Reservas Hoje</h3>
                                <div class="number text-gold">4 Ativas</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- VISTA 2: GESTÃO DE CONDÔMINOS (Tabela) -->
                <div id="view-condominos" class="view-section <?= $is_search ? '' : 'hidden' ?>">
                    <div class="table-container fade-in-up">
                        <div class="table-header">
                            <h2>Gestão de Condôminos</h2>
                            
                            <!-- Formulário de Busca e Botões Integrados -->
                            <form action="admin.php" method="GET" class="filter-form">
                                <div class="input-group">
                                    <select id="filtro_ativo" name="filtro_ativo" class="lux-input">
                                        <option value="">Status Site (Todos)</option>
                                        <option value="1" <?= $filtro_ativo === '1' ? 'selected' : '' ?>>Visíveis no Site</option>
                                        <option value="0" <?= $filtro_ativo === '0' ? 'selected' : '' ?>>Ocultos no Site</option>
                                    </select>
                                </div>

                                <div class="input-group search-group">
                                    <i class="fas fa-search search-icon"></i>
                                    <input id="busca" type="text" name="busca" class="lux-input search-input" placeholder="Buscar sala, empresa, CNPJ..." value="<?= htmlspecialchars($busca ?? '') ?>">
                                </div>
                                
                                <button type="submit" class="btn lux-btn-primary">Filtrar</button>
                                
                                <?php if (!empty($busca) || $filtro_ativo !== ''): ?>
                                    <!-- Limpar remove todos os filtros e volta o sistema ao estado original -->
                                    <a href="admin.php" class="btn lux-btn-danger">Limpar</a>
                                <?php endif; ?>

                                <span class="divider"></span>

                                <!-- Botão de Exportar Excel -->
                                <button type="submit" name="export" value="excel" class="btn lux-btn-excel" title="Exportar dados atuais para Excel">
                                    <i class="fas fa-file-excel"></i> Exportar
                                </button>
                                
                                <a href="novo_condomino.php" class="btn lux-btn-success">+ Novo Cadastro</a>
                            </form>
                        </div>
                        
                        <div class="table-responsive">
                            <table class="admin-table">
                                <thead>
                                    <tr>
                                        <th>Sala / Bloco</th>
                                        <th>Empresa & Responsável</th>
                                        <th>Status (Site)</th>
                                        <th>Status Financeiro</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody id="tableBody">
                                    <?php foreach ($lista_condominos as $condomino): ?>
                                    <tr class="table-row">
                                        <td>
                                            <div class="sala-destaque"><?= $val($condomino, 'sala_principal', 'sala') ?></div>
                                            <?php if ($val($condomino, 'salas')): ?>
                                                <div class="sala-sub"><?= $val($condomino, 'salas') ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong class="empresa-nome"><?= $val($condomino, 'razao_social', 'empresa') ?></strong>
                                            <?php if ($val($condomino, 'responsavel')): ?>
                                                <div class="empresa-resp"><i class="fas fa-user-tie"></i> <?= $val($condomino, 'responsavel') ?></div>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($is_ativo($condomino)): ?>
                                                <span class="badge badge-publico"><i class="fas fa-eye"></i> Visível</span>
                                            <?php else: ?>
                                                <span class="badge badge-oculto"><i class="fas fa-eye-slash"></i> Oculto</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($val($condomino, 'status') == 'Adimplente'): ?>
                                                <span class="badge badge-adimplente">Adimplente</span>
                                            <?php else: ?>
                                                <span class="badge badge-pendente">Pendente</span>
                                            <?php endif; ?>
                                        </td>
                                        <td class="action-cells">
                                            <a href="editar.php?id=<?= $condomino['id'] ?>" class="btn-action edit" title="Editar"><i class="fas fa-pen"></i></a>
                                            <a href="excluir.php?id=<?= $condomino['id'] ?>" class="btn-action delete" title="Excluir" onclick="return confirm('Tem certeza que deseja remover permanentemente este condomínio?');"><i class="fas fa-trash"></i></a>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                    
                                    <?php if (count($lista_condominos) === 0): ?>
                                    <tr>
                                        <td colspan="5" class="empty-state">
                                            <i class="fas fa-folder-open"></i>
                                            <p>Nenhum registro encontrado para a sua busca.</p>
                                        </td>
                                    </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>

            </div>
        </main>
    </div>

    <script src="admin.js"></script>
</body>
</html>
