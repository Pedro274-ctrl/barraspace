<?php
require_once __DIR__ . '/auth.php';
require_authenticated_user();

require_once 'conexao.php';

$is_standard_user = $_SESSION['perfil'] === 'usuario';
if (!isset($_SESSION['csrf_token']) || !is_string($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}
$csrf_token = $_SESSION['csrf_token'];
$password_change_message = $_SESSION['password_change_message'] ?? null;
$password_change_status = $_SESSION['password_change_status'] ?? null;
$user_creation_message = $_SESSION['user_creation_message'] ?? null;
$user_creation_status = $_SESSION['user_creation_status'] ?? null;
unset(
    $_SESSION['password_change_message'],
    $_SESSION['password_change_status'],
    $_SESSION['user_creation_message'],
    $_SESSION['user_creation_status']
);

$user_name = trim($_SESSION['nome'] ?? '') ?: $_SESSION['email'];
$user_name_esc = htmlspecialchars($user_name, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$user_initial = htmlspecialchars(strtoupper(substr($user_name, 0, 1)), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
$user_role = match ($_SESSION['perfil']) {
    'operador' => 'Operador',
    'usuario' => 'Usuário',
    default => 'Gerente',
};

// Filtros
$busca = filter_input(INPUT_GET, 'busca', FILTER_SANITIZE_STRING);
$filtro_ativo = filter_input(INPUT_GET, 'filtro_ativo', FILTER_SANITIZE_STRING);
$exportar = filter_input(INPUT_GET, 'export', FILTER_SANITIZE_STRING);

// Variável de controle para manter a aba correta aberta após um filtro
$is_search = $is_standard_user || !empty($busca) || $filtro_ativo !== '';
$is_account = ($_GET['account'] ?? '') === '1';
$requested_security_section = $_GET['security_section'] ?? 'password';
$security_section = is_string($requested_security_section)
    && in_array($requested_security_section, ['password', 'users'], true)
    ? $requested_security_section
    : 'password';
if ($security_section === 'users') {
    require_user_management_access();
}
if ($is_account) {
    require_privileged_admin();
}
if ($is_standard_user && $exportar === 'excel') {
    require_privileged_admin();
}
$accounts = [];
if ($is_account && $security_section === 'users') {
    $accountsStatement = $pdo->query('SELECT id, email, nome, perfil, ativo FROM gerencia ORDER BY perfil, nome, email');
    $accounts = $accountsStatement->fetchAll(PDO::FETCH_ASSOC);
}

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

    <div class="cursor-dot" aria-hidden="true"></div>
    <div class="cursor-outline" aria-hidden="true"></div>
    <div class="admin-wrapper">
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-logo">
                <img src="http://www.barraspace.com.br/wp-content/uploads/2016/12/LogoMarcaTransp80.jpg" alt="Logo Barra Space">
            </div>
            <ul class="sidebar-menu">
                <!-- Classes 'active' e 'menu-link' com data-target controlam a navegação no JS -->
                <?php if ($is_standard_user): ?>
                <li><a href="#" class="menu-link active" data-target="view-condominos"><i class="fas fa-users"></i> Gestão de Condôminos</a></li>
                <?php else: ?>
                <li><a href="#" class="menu-link <?= !$is_search && !$is_account ? 'active' : '' ?>" data-target="view-dashboard"><i class="fas fa-chart-pie"></i> Dashboard</a></li>
                <li><a href="#" class="menu-link <?= $is_search ? 'active' : '' ?>" data-target="view-condominos"><i class="fas fa-users"></i> Gestão de Condôminos</a></li>
                <li><a href="#" class="menu-link <?= $is_account ? 'active' : '' ?>" data-target="view-account"><i class="fas fa-key"></i> Segurança</a></li>
                <li><a href="#"><i class="fas fa-handshake"></i> Centro de Convenções</a></li>
                <li><a href="#"><i class="fas fa-bullhorn"></i> Comunicados</a></li>
                <li><a href="#"><i class="fas fa-file-invoice-dollar"></i> Financeiro (Boletos)</a></li>
                <?php endif; ?>
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
                        <div class="avatar"><?= $user_initial ?></div>
                        <div class="user-details">
                            <span class="greeting">Olá, <strong><?= $user_name_esc ?></strong></span>
                            <span class="role"><?= $user_role ?></span>
                        </div>
                    </div>
                    <a href="logout.php" class="btn-logout-header" title="Sair do Sistema">
                        <i class="fas fa-sign-out-alt"></i> Sair
                    </a>
                </div>
            </header>

            <div class="dashboard-content">
                <?php if (!$is_standard_user): ?>
                <!-- VISTA 1: DASHBOARD (Visão Geral) -->
                <div id="view-dashboard" class="view-section <?= $is_search || $is_account ? 'hidden' : '' ?>">
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
                <?php endif; ?>

                <!-- VISTA 2: GESTÃO DE CONDÔMINOS (Tabela) -->
                <div id="view-condominos" class="view-section <?= $is_search && !$is_account ? '' : 'hidden' ?>">
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
                                    <input id="busca" type="text" name="busca" class="lux-input search-input" placeholder="Buscar sala, empresa..." value="<?= htmlspecialchars($busca ?? '') ?>">
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

                <?php if (!$is_standard_user): ?>
                <div id="view-account" class="view-section <?= $is_account ? '' : 'hidden' ?>">
                    <div class="security-layout">
                        <nav class="security-navigation" aria-label="Opções de segurança">
                            <a href="admin.php?account=1&amp;security_section=password" class="<?= $security_section === 'password' ? 'active' : '' ?>" <?= $security_section === 'password' ? 'aria-current="page"' : '' ?>>
                                <i class="fas fa-key" aria-hidden="true"></i> Redefinir senha
                            </a>
                            <a href="admin.php?account=1&amp;security_section=users" class="<?= $security_section === 'users' ? 'active' : '' ?>" <?= $security_section === 'users' ? 'aria-current="page"' : '' ?>>
                                <i class="fas fa-users" aria-hidden="true"></i> Usuários
                            </a>
                        </nav>

                        <?php if ($security_section === 'password'): ?>
                        <section class="table-container password-settings" aria-labelledby="security-section-title">
                            <div class="table-header">
                                <h2 id="security-section-title">Redefinir senha</h2>
                                <p>Altere a senha da sua conta autenticada. A alteração está disponível para gerentes e operadores.</p>
                            </div>

                            <?php if (is_string($password_change_message)): ?>
                                <p class="password-feedback <?= $password_change_status === 'success' ? 'success' : 'error' ?>" role="status">
                                    <?= htmlspecialchars($password_change_message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>

                            <form action="alterar_senha.php" method="POST" class="password-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                <div class="password-field">
                                    <label for="current_password">Senha atual</label>
                                    <input class="lux-input" type="password" id="current_password" name="current_password" autocomplete="current-password" required>
                                </div>
                                <div class="password-field">
                                    <label for="new_password">Nova senha (12 a 72 bytes)</label>
                                    <input class="lux-input" type="password" id="new_password" name="new_password" autocomplete="new-password" minlength="12" maxlength="72" required>
                                </div>
                                <div class="password-field">
                                    <label for="confirm_password">Confirme a nova senha</label>
                                    <input class="lux-input" type="password" id="confirm_password" name="confirm_password" autocomplete="new-password" minlength="12" maxlength="72" required>
                                </div>
                                <button type="submit" class="btn lux-btn-primary">Atualizar senha</button>
                            </form>
                        </section>
                        <?php else: ?>
                        <section class="table-container security-users-placeholder" aria-labelledby="security-section-title">
                            <h2 id="security-section-title">Usuários</h2>
                            <p>Cadastre uma conta de acesso para um funcionário. A conta criada poderá gerenciar condôminos, mas não terá acesso à Segurança.</p>
                            <?php if (is_string($user_creation_message)): ?>
                                <p class="password-feedback <?= $user_creation_status === 'success' ? 'success' : 'error' ?>" role="status">
                                    <?= htmlspecialchars($user_creation_message, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>
                                </p>
                            <?php endif; ?>
                            <form action="criar_usuario.php" method="POST" class="password-form">
                                <input type="hidden" name="csrf_token" value="<?= htmlspecialchars($csrf_token, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?>">
                                <div class="password-field">
                                    <label for="new_user_name">Nome do funcionário</label>
                                    <input class="lux-input" type="text" id="new_user_name" name="nome" maxlength="100" autocomplete="name" required>
                                </div>
                                <div class="password-field">
                                    <label for="new_user_email">E-mail de login</label>
                                    <input class="lux-input" type="email" id="new_user_email" name="email" maxlength="100" autocomplete="email" required>
                                </div>
                                <div class="password-field">
                                    <label for="new_user_password">Senha inicial (12 a 72 bytes)</label>
                                    <input class="lux-input" type="password" id="new_user_password" name="senha" minlength="12" maxlength="72" autocomplete="new-password" required>
                                </div>
                                <div class="password-field">
                                    <label for="new_user_password_confirmation">Confirme a senha</label>
                                    <input class="lux-input" type="password" id="new_user_password_confirmation" name="confirmacao_senha" minlength="12" maxlength="72" autocomplete="new-password" required>
                                </div>
                                <button type="submit" class="btn lux-btn-primary">Cadastrar usuário</button>
                            </form>
                            <div class="table-responsive users-table-wrap">
                                <table class="admin-table users-table">
                                    <thead>
                                        <tr>
                                            <th>Login</th>
                                            <th>Nome</th>
                                            <th>E-mail</th>
                                            <th>Ativo</th>
                                            <th>Privilégio de administrador</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php foreach ($accounts as $account): ?>
                                            <?php
                                            $accountLogin = match ($account['perfil']) {
                                                'gerencia' => 'admin',
                                                'operador' => 'operador',
                                                default => $account['email'],
                                            };
                                            $accountName = trim((string) $account['nome']) ?: $accountLogin;
                                            $hasAdminPrivilege = in_array($account['perfil'], ['gerencia', 'operador'], true);
                                            ?>
                                            <tr>
                                                <td><?= htmlspecialchars($accountLogin, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($accountName, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                                <td><?= htmlspecialchars($account['email'], ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8') ?></td>
                                                <td><?= (int) $account['ativo'] === 1 ? 'Sim' : 'Não' ?></td>
                                                <td><?= $hasAdminPrivilege ? 'Sim' : 'Não' ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        <?php if ($accounts === []): ?>
                                            <tr>
                                                <td colspan="5" class="empty-state">Nenhuma conta cadastrada.</td>
                                            </tr>
                                        <?php endif; ?>
                                    </tbody>
                                </table>
                            </div>
                        </section>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endif; ?>

            </div>
        </main>
    </div>

    <script src="cursor.js"></script>
    <script src="admin.js"></script>
</body>
</html>
