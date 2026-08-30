<?php
session_start();
if (!isset($_SESSION['logado'])) {
    header("Location: login.html");
    exit;
}

require 'conexao.php';
$stmt = $pdo->query("SELECT * FROM condominos ORDER BY sala ASC");
$lista_condominos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Painel Administrativo - Barra Space Center</title>
    <link rel="stylesheet" href="admin.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
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
                        <h3>Salas Ocupadas</h3>
                        <div class="number"><?= count($lista_condominos) ?></div>
                    </div>
                    <div class="admin-card">
                        <h3>Reservas (Convenções)</h3>
                        <div class="number text-blue">4 Hoje</div>
                    </div>
                </div>

                <div class="table-container">
                    <div class="table-header">
                        <h2>Lista de Condôminos Recentes</h2>
                        <a href="novo_condomino.php" class="btn-new" style="text-decoration: none; display: inline-block;">+ Novo Cadastro</a>
                    </div>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Sala</th>
                                <th>Empresa / Responsável</th>
                                <th>E-mail</th>
                                <th>Status</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lista_condominos as $condomino): ?>
                            <tr>
                                <td><?= htmlspecialchars($condomino['sala']) ?></td>
                                <td><?= htmlspecialchars($condomino['empresa']) ?></td>
                                <td><?= htmlspecialchars($condomino['email']) ?></td>
                                <td>
                                    <?php if ($condomino['status'] == 'Adimplente'): ?>
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
                                <td colspan="5" style="text-align: center; padding: 20px;">Nenhum condômino cadastrado ainda.</td>
                            </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="admin.js"></script>
</body>
</html>