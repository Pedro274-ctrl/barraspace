<?php
require_once 'conexao.php';
// Busca os condôminos no banco de dados em ordem alfabética pela empresa (apenas ativos)
$stmt = $pdo->query("SELECT * FROM condominos WHERE ativo = 1 ORDER BY razao_social ASC");
$lista_condominos = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barra Space Center - Centro Empresarial</title>
    <link rel="stylesheet" href="style.css">
    <!-- Fonte do Google -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;700&display=swap" rel="stylesheet">
</head>
<body>

    <!-- Cabeçalho / Navegação -->
    <header>
        <div class="container header-container">
            <div class="logo">
                <img src="http://www.barraspace.com.br/wp-content/uploads/2016/12/LogoMarcaTransp80.jpg" alt="Logo Barra Space Center">
            </div>
            <nav id="menu">
                <ul>
                    <li><a href="#home">Início</a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#convencoes">Centro de Convenções</a></li>
                    <li><a href="#condominos">Condôminos</a></li> <!-- Nova Aba -->
                    <li><a href="#contato">Contato</a></li>
                    <li><a href="login.html" class="btn-painel-discreto">🔒 Painel</a></li>
                </ul>
            </nav>
            <div class="menu-toggle" id="mobile-menu">
                <span></span>
                <span></span>
                <span></span>
            </div>
        </div>
    </header>

    <!-- Banner Principal -->
    <section id="home" class="hero" style="background-image: linear-gradient(rgba(0, 0, 0, 0.6), rgba(0, 0, 0, 0.6)), url('http://www.barraspace.com.br/wp-content/uploads/2016/12/PredioTransp1200.jpg');">
        <div class="container hero-content">
            <h1>Barra Space Center</h1>
            <h2>Centro Empresarial</h2>
            <p>Seu negócio no melhor ponto da Barra da Tijuca.</p>
            <a href="#contato" class="btn-primary">Fale Conosco</a>
        </div>
    </section>

    <!-- Sobre o Empreendimento -->
    <section id="sobre" class="section">
        <div class="container text-center">
            <h2 class="section-title">Sobre o Barra Space Center</h2>
            <p class="section-subtitle">Localizado em um dos melhores lugares da Barra da Tijuca.</p>
            <p>Tendo acesso de forma rápida e fácil a Restaurantes, Shoppings, Agências Bancárias, entre outros. Oferecemos a infraestrutura ideal para que sua empresa tenha o máximo de conforto, segurança e conveniência.</p>
        </div>
    </section>

    <!-- Centro de Convenções -->
    <section id="convencoes" class="section bg-light">
        <div class="container">
            <h2 class="section-title text-center">Centro de Convenções</h2>
            <p class="section-subtitle text-center">Totalmente equipado para atender suas necessidades.</p>
            
            <div class="grid-convencoes">
                <!-- Auditório -->
                <div class="card">
                    <img src="http://www.barraspace.com.br/wp-content/uploads/2021/04/WhatsApp-Image-2021-04-26-at-18.36.55.jpeg" alt="Auditório">
                    <div class="card-content">
                        <h3>Auditório</h3>
                        <p>Capacidade para 70 pessoas. Ideal para palestras e grandes apresentações.</p>
                    </div>
                </div>
                <!-- Sala de Treinamento -->
                <div class="card">
                    <img src="http://www.barraspace.com.br/wp-content/uploads/2021/04/WhatsApp-Image-2021-04-26-at-18.33.01.jpeg" alt="Sala de Treinamento">
                    <div class="card-content">
                        <h3>Sala de Treinamento</h3>
                        <p>Capacidade para 21 pessoas. Ambiente preparado para cursos e workshops.</p>
                    </div>
                </div>
                <!-- Sala de Reunião 1 -->
                <div class="card">
                    <img src="http://www.barraspace.com.br/wp-content/uploads/2021/04/WhatsApp-Image-2021-04-26-at-18.27.54.jpeg" alt="Sala de Reunião 15 pessoas">
                    <div class="card-content">
                        <h3>Sala de Reunião 1</h3>
                        <p>Capacidade para 15 pessoas. Perfeita para reuniões executivas.</p>
                    </div>
                </div>
                <!-- Sala de Reunião 2 -->
                <div class="card">
                    <img src="http://www.barraspace.com.br/wp-content/uploads/2021/04/WhatsApp-Image-2021-04-26-at-18.30.26.jpeg" alt="Sala de Reunião 9 pessoas">
                    <div class="card-content">
                        <h3>Sala de Reunião 2</h3>
                        <p>Capacidade para 9 pessoas. Ideal para encontros mais reservados.</p>
                    </div>
                </div>
                <!-- Coffee-Break -->
                <div class="card">
                    <img src="http://www.barraspace.com.br/wp-content/uploads/2021/04/WhatsApp-Image-2021-04-26-at-18.25.24.jpeg" alt="Salão de Coffee-Break">
                    <div class="card-content">
                        <h3>Salão de Coffee-Break</h3>
                        <p>Área reservada para refeições e networking durante seus eventos.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Condôminos -->
<section id="condominos" class="section bg-light">
    <div class="container">
        <h2 class="section-title text-center" style="color: #b8860b; text-transform: uppercase;">Condôminos</h2>
        
        <div class="filtros-condominos text-center">
            <label><input type="radio" name="filtro" checked> Alfabética</label>
            <label><input type="radio" name="filtro"> Andares</label>
        </div>

        <div class="grid-condominos">
            <?php foreach ($lista_condominos as $condomino): ?>
                <div class="condomino-box" style="margin-bottom: 25px;">
                    <!-- Nome da Empresa (Razão Social) -->
                    <h4 class="cond-nome" style="color: #b8860b; font-size: 1.1rem; margin-bottom: 2px;">
                        <?= htmlspecialchars($condomino['razao_social']) ?>
                    </h4>
                    
                    <!-- Salas -->
                    <?php if ($condomino['chk_salas'] == 1 && !empty($condomino['salas'])): ?>
                        <p class="cond-detalhe" style="color: #c49a45; font-size: 0.9rem; margin-bottom: 2px;">
                            <?= htmlspecialchars($condomino['salas']) ?>
                        </p>
                    <?php else: ?>
                        <p class="cond-detalhe" style="color: #c49a45; font-size: 0.9rem; margin-bottom: 2px;">
                            Sala <?= htmlspecialchars($condomino['sala_principal']) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Responsável -->
                    <?php if ($condomino['chk_responsavel'] == 1 && !empty($condomino['responsavel'])): ?>
                        <p class="cond-detalhe" style="color: #c49a45; font-size: 0.9rem; margin-bottom: 2px;">
                            <strong>Responsável:</strong> <?= htmlspecialchars($condomino['responsavel']) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Especialidades -->
                    <?php if ($condomino['chk_especialidades'] == 1 && !empty($condomino['especialidades'])): ?>
                        <p class="cond-detalhe" style="color: #c49a45; font-size: 0.9rem; margin-bottom: 2px;">
                            <strong>Especialidade:</strong> <?= htmlspecialchars($condomino['especialidades']) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Telefones -->
                    <?php if ($condomino['chk_telefones'] == 1 && !empty($condomino['telefones'])): ?>
                        <p class="cond-detalhe" style="color: #c49a45; font-size: 0.9rem; margin-bottom: 2px;">
                            📞 <?= htmlspecialchars($condomino['telefones']) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Email -->
                    <?php if ($condomino['chk_email'] == 1 && !empty($condomino['email'])): ?>
                        <p class="cond-detalhe" style="color: #c49a45; font-size: 0.9rem; margin-bottom: 2px;">
                            ✉ <?= htmlspecialchars($condomino['email']) ?>
                        </p>
                    <?php endif; ?>

                    <!-- Site (URL) -->
                    <?php if ($condomino['chk_url'] == 1 && !empty($condomino['url'])): ?>
                        <p class="cond-detalhe" style="color: #c49a45; font-size: 0.9rem; margin-bottom: 2px;">
                            <a href="http://<?= htmlspecialchars($condomino['url']) ?>" target="_blank" style="color: #c49a45; text-decoration: none;">
                                <?= htmlspecialchars($condomino['url']) ?>
                            </a>
                        </p>
                    <?php endif; ?>
                </div>
            <?php endforeach; ?>
            
            <?php if (count($lista_condominos) === 0): ?>
                <p style="text-align: center; width: 100%;">Nenhum condômino disponível no momento.</p>
            <?php endif; ?>
        </div>
    </div>
</section>

    <!-- Contato e Mapa -->
    <section id="contato" class="section">
        <div class="container">
            <h2 class="section-title text-center">Fale Conosco</h2>
            <p class="section-subtitle text-center">Envie suas sugestões e reclamações</p>
            
            <div class="contato-wrapper">
                <!-- Formulário -->
                <div class="contato-form">
                    <form id="formContato">
                        <div class="form-group">
                            <label for="nome">Nome *</label>
                            <input type="text" id="nome" name="nome" required>
                        </div>
                        <div class="form-group">
                            <label for="email">E-mail *</label>
                            <input type="email" id="email" name="email" required>
                        </div>
                        <div class="form-group">
                            <label for="assunto">Assunto *</label>
                            <input type="text" id="assunto" name="assunto" required>
                        </div>
                        <div class="form-group">
                            <label for="categoria">Categoria de Assunto *</label>
                            <select id="categoria" name="categoria" required>
                                <option value="">Selecione...</option>
                                <option value="informacoes">Informações</option>
                                <option value="sugestoes">Sugestões</option>
                                <option value="reclamacoes">Reclamações</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="mensagem">Mensagem *</label>
                            <textarea id="mensagem" name="mensagem" rows="5" required></textarea>
                        </div>
                        <button type="submit" class="btn-primary">Enviar Mensagem</button>
                    </form>
                </div>

                <!-- Info e Mapa -->
                <div class="contato-info">
                    <div class="info-box">
                        <h4>Endereço</h4>
                        <p>Av. das Américas, 1155 - Sobre Loja<br>Barra da Tijuca - CEP: 22631-000<br>Rio de Janeiro - RJ</p>
                    </div>
                    <div class="info-box">
                        <h4>Telefones</h4>
                        <p>+55 21 2499-4337<br>+55 21 2499-4293</p>
                    </div>
                    <div class="info-box">
                        <h4>E-mails</h4>
                        <p>Administração: administracao@barraspace.com.br<br>Gerência: gerencia@barraspace.com.br</p>
                    </div>
                    
                    <!-- Mapa Embutido do Google -->
                    <div class="mapa">
                        <iframe
                            src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3673.061033621415!2d-43.3235316844865!3d-23.003189984962634!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9bd0a7bc014023%3A0xc66512b8b9dcde99!2sAv.%20das%20Am%C3%A9ricas%2C%201155%20-%20Barra%20da%20Tijuca%2C%20Rio%20de%20Janeiro%20-%20RJ%2C%2022631-000!5e0!3m2!1spt-BR!2sbr!4v1680000000000!5m2!1spt-BR!2sbr"
                            width="100%" height="250" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Mapa da localização do Barra Space Center">
                        </iframe>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rodapé -->
    <footer>
        <div class="container text-center">
            <img src="http://www.barraspace.com.br/wp-content/uploads/2016/12/LogoMarcaTransp80.jpg" alt="Logo Barra Space Center" width="120" style="margin-bottom: 15px; opacity: 0.8;">
            <p>Todos os direitos reservados &copy; Condomínio do Edifício Barra Space Center</p>
        </div>
    </footer>

    <script src="script.js"></script>
</body>
</html>
