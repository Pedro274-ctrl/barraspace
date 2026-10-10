<?php
/**
 * BARRA SPACE CENTER - INDEX 
 * Refatoração Cirúrgica: Galeria Centro de Convenções 
 */

require_once __DIR__ . '/conexao.php';

$lista_condominos = [];
$total_condominos = 0;

try {
    // Tenta buscar já com e-mail e seu toggle de exibição (chk_email)
    $sql = "SELECT 
                id, razao_social, andar, sala_principal, salas, especialidades, 
                responsavel, telefones, email, url, 
                chk_salas, chk_especialidades, chk_telefones, chk_email, chk_url
            FROM condominos 
            WHERE ativo = 1 
            ORDER BY razao_social ASC";

    $stmt = $pdo->query($sql);
    $lista_condominos = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Throwable $e) {
    // Fallback: banco ainda sem as colunas email/chk_email — mantém a seção funcionando
    // (basta rodar: ALTER TABLE condominos ADD COLUMN email VARCHAR(190) NULL,
    //  ADD COLUMN chk_email TINYINT(1) NOT NULL DEFAULT 0; para habilitar o e-mail)
    try {
        $sql_fallback = "SELECT 
                    id, razao_social, andar, sala_principal, salas, especialidades, 
                    responsavel, telefones, url, 
                    chk_salas, chk_especialidades, chk_telefones, chk_url
                FROM condominos 
                WHERE ativo = 1 
                ORDER BY razao_social ASC";
        $stmt = $pdo->query($sql_fallback);
        $lista_condominos = $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Throwable $e2) {
        error_log('[BARRA SPACE] Erro ao listar condôminos: ' . $e2->getMessage());
        $lista_condominos = [];
    }
}
$total_condominos = count($lista_condominos);

function normaliza_url_barra(?string $url): string {
    if (empty($url)) return '';
    $url = trim($url);
    $url = preg_replace('#^https?://#i', '', $url);
    $url = trim($url, '/');
    return $url;
}
function normaliza_url_href(?string $url): string {
    if (empty($url)) return '#';
    return 'https://' . normaliza_url_barra($url);
}
function asset_https(string $url): string {
    return preg_replace('#^http://#i', 'https://', $url);
}

// LÓGICA DINÂMICA: Galeria de Fotos - Centro de Convenções
$dir_fotos_convencoes = 'imagens/convencoes/'; 
$fotos_convencoes = [];
if (is_dir($dir_fotos_convencoes)) {
    $fotos_convencoes = glob($dir_fotos_convencoes . '*.{jpg,jpeg,png,webp,JPG,JPEG,PNG,WEBP}', GLOB_BRACE);
}

// Fallback visual
if (empty($fotos_convencoes)) {
    for ($i = 1; $i <= 9; $i++) {
        $fotos_convencoes[] = "https://via.placeholder.com/600x400/1a202c/c5a880?text=Foto+Convencao+0{$i}";
    }
} else {
    $fotos_convencoes = array_slice($fotos_convencoes, 0, 9); 
}

// MAPEAMENTO ESTRATÉGICO: legenda + categoria de cada foto (ordem fixa, aprovada pelo cliente)
$legendas_convencoes = [
    "Salão para Coffee-Break",                // Foto 1
    "Salão para Coffee-Break",                // Foto 2
    "Salão para Coffee-Break",                // Foto 3
    "Sala de Reunião para 15 pessoas",        // Foto 4
    "Sala de Reunião para 9 pessoas",         // Foto 5
    "Sala de Treinamento para 21 pessoas",    // Foto 6
    "Auditório para 70 pessoas",              // Foto 7
    "Auditório para 70 pessoas",              // Foto 8
    "Auditório para 70 pessoas"               // Foto 9
];
$categorias_convencoes = [
    'coffee', 'coffee', 'coffee',
    'reuniao', 'reuniao',
    'treinamento',
    'auditorio', 'auditorio', 'auditorio'
];
$rotulos_categoria = [
    'coffee'      => 'Coffee-Break',
    'reuniao'     => 'Sala de Reunião',
    'treinamento' => 'Treinamento',
    'auditorio'   => 'Auditório',
];

// Abas de filtro (ordem: Todos + categorias na ordem de primeira aparição)
$abas_convencoes = ['todos' => 'Todos os Espaços'];
foreach (array_unique($categorias_convencoes) as $cat) {
    $abas_convencoes[$cat] = $rotulos_categoria[$cat] ?? ucfirst($cat);
}

// Coleção final já escapada — evita repetição e risco de erro dentro do loop da view
$conv_items = [];
foreach ($fotos_convencoes as $i => $foto) {
    $categoria = $categorias_convencoes[$i] ?? 'auditorio';
    $legenda   = $legendas_convencoes[$i] ?? 'Espaço Premium Barra Space';
    $rotulo    = $rotulos_categoria[$categoria] ?? ucfirst($categoria);
    $conv_items[] = [
        'src'         => $foto,
        'src_esc'     => htmlspecialchars($foto, ENT_QUOTES, 'UTF-8'),
        'legenda'     => $legenda,
        'legenda_esc' => htmlspecialchars($legenda, ENT_QUOTES, 'UTF-8'),
        'categoria'   => $categoria,
        'label'       => $rotulo,
        'label_esc'   => htmlspecialchars($rotulo, ENT_QUOTES, 'UTF-8'),
    ];
}

$ano_atual = date('Y');
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Barra Space Center - O Endereço dos Grandes Negócios | Barra da Tijuca</title>
    
    <meta name="description" content="Barra Space Center na Av. das Américas, 1155 - Barra da Tijuca. Centro empresarial com 20+ anos de tradição, centro de convenções, segurança 24h e ecossistema de grandes empresas.">
    <meta name="keywords" content="Barra Space Center, sala comercial Barra da Tijuca, centro empresarial Barra, Av das Américas 1155, condomínio empresarial">
    <link rel="canonical" href="https://www.barraspace.com.br/">
    
    <meta property="og:title" content="Barra Space Center - O Endereço dos Grandes Negócios">
    <meta property="og:description" content="Infraestrutura de alto padrão na Barra da Tijuca. Conheça nossos condôminos e centro de convenções.">
    <meta property="og:type" content="website">
    <meta property="og:image" content="<?= asset_https('http://www.barraspace.com.br/wp-content/uploads/2016/12/PredioTransp1200.jpg') ?>">
    
    <meta name="theme-color" content="#0a0a0a">

    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Cormorant+Garamond:wght@400;500;600;700&family=Manrope:wght@300;400;500;600&display=swap" rel="stylesheet">
    
    <!-- ARQUIVO EXTERNO: CSS Lado Cliente -->
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <div id="preloader">
        <div class="loader-logo">
            <span>B</span><span>S</span><span>C</span>
            <p>BARRA SPACE CENTER</p>
        </div>
    </div>
    <noscript><style>#preloader{display:none !important;}</style></noscript>

    <div id="scroll-progress" aria-hidden="true"></div>
    <div class="cursor-dot" aria-hidden="true"></div>
    <div class="cursor-outline" aria-hidden="true"></div>

    <header id="header">
        <div class="container header-container">
            <a href="#home" class="logo" aria-label="Barra Space Center - Início">
                <img src="<?= asset_https('http://www.barraspace.com.br/wp-content/uploads/2016/12/LogoMarcaTransp80.jpg') ?>" alt="Barra Space Center" width="60" height="60" onerror="this.style.display='none'">
                <span>BARRA SPACE<br><small>CENTER</small></span>
            </a>
            <nav id="menu" aria-label="Menu principal">
                <ul>
                    <li><a href="#home" class="nav-link active">Início</a></li>
                    <li><a href="#sobre" class="nav-link">Sobre</a></li>
                    <li><a href="#estrutura" class="nav-link">Estrutura</a></li>
                    <li><a href="#convencoes" class="nav-link">Centro de Convenções</a></li>
                    <li><a href="#condominos" class="nav-link">Condôminos</a></li>
                    <li><a href="#contato" class="nav-link">Contato</a></li>
                </ul>
            </nav>
            <button class="menu-toggle" id="mobile-menu" aria-label="Abrir menu" aria-expanded="false"><span></span><span></span><span></span></button>
        </div>
    </header>

    <section id="home" class="hero">
        <div class="hero-bg" style="background-image: url('<?= asset_https('http://www.barraspace.com.br/wp-content/uploads/2016/12/PredioTransp1200.jpg') ?>');"></div>
        <div class="hero-overlay"></div>
        <div class="container hero-content">
            <div class="eyebrow reveal">Av. das Américas, 1155 • Barra da Tijuca</div>
            <h1 class="reveal">O endereço<br><span class="gold-italic">que valoriza</span><br>o seu negócio.</h1>
            <p class="reveal">Infraestrutura de alto padrão, localização privilegiada e um ecossistema de empresas que inspiram crescimento.</p>
            <div class="hero-actions reveal">
                <a href="#condominos" class="btn-primary magnetic">Conheça as Empresas</a>
                <a href="#contato" class="btn-ghost magnetic">Agende uma Visita</a>
            </div>
        </div>
        <div class="hero-scroll" aria-hidden="true"><span>Scroll</span><div class="line"></div></div>
    </section>

    <div class="marquee" aria-hidden="true">
        <div class="marquee-track">
            <span>CONFORTO • SEGURANÇA • CONVENIÊNCIA • LOCALIZAÇÃO PRIVILEGIADA •</span>
            <span>CONFORTO • SEGURANÇA • CONVENIÊNCIA • LOCALIZAÇÃO PRIVILEGIADA •</span>
        </div>
    </div>

    <!-- Seção Sobre -->
    <section id="sobre" class="section">
        <div class="container">
            <div class="about-grid">
                <div class="about-text">
                    <div class="eyebrow reveal">Sobre Nós</div>
                    <h2 class="section-title reveal">Um centro empresarial <br>pensado para o topo.</h2>
                    <p class="reveal">Localizado em um dos melhores pontos da Barra da Tijuca, com acesso rápido a restaurantes, shoppings e agências bancárias. O Barra Space oferece a infraestrutura ideal para que sua empresa opere com máximo de conforto, segurança e prestígio.</p>
                    <div class="stats reveal">
                        <div><strong>20+</strong><span>Anos de tradição</span></div>
                        <div><strong>100%</strong><span>Climatizado</span></div>
                        <div><strong>24h</strong><span>Segurança</span></div>
                    </div>
                </div>
                <div class="about-image reveal">
                    <div class="image-frame">
                        <img src="<?= asset_https('https://draglauciamoraes.com.br/wp-content/uploads/2020/01/11-3.jpg') ?>" alt="Fachada Barra Space Center" loading="lazy">
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Seção Estrutura -->
    <section id="estrutura" class="secao-estrutura section bg-dark">
        <div class="container">
            <div class="section-header text-center">
                <div class="eyebrow gold reveal">INFRAESTRUTURA</div>
                <h2 class="section-title reveal">Segurança e Infraestrutura</h2>
                <p class="section-subtitle reveal">Do controle de acesso à manutenção predial, cada camada da estrutura existe para que sua empresa opere sem se preocupar com o edifício.</p>
            </div>

            <div class="box-destaque reveal">
                <div class="icone">
                    <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M12 22s8-4 8-10V5l-8-3-8 3v7c0 6 8 10 8 10z"/></svg>
                </div>
                <div class="conteudo-destaque">
                    <h3>Controle de Acesso com Leitor Facial</h3>
                    <p>As catracas de acesso ao edifício contam com leitura facial, eliminando a necessidade de cartões ou crachás físicos e garantindo que apenas condôminos, colaboradores e visitantes autorizados circulem pelos pavimentos.</p>
                    <div class="tags-estrutura">
                        <span>LEITOR FACIAL NAS CATRACAS</span>
                        <span>CFTV 24 HORAS</span>
                        <span>PORTARIA DEDICADA</span>
                        <span>CONTROLE DE VISITANTES</span>
                    </div>
                </div>
            </div>

            <div class="grid-estrutura reveal">
                <div class="card-estrutura">
                    <div class="icone"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="2" y="2" width="20" height="20" rx="2" ry="2"/><line x1="2" y1="12" x2="22" y2="12"/></svg></div>
                    <h4>CFTV 24 Horas</h4>
                    <p>Monitoramento por câmeras nas áreas comuns, garagem e acessos, com cobertura contínua do edifício.</p>
                </div>
                <div class="card-estrutura">
                    <div class="icone"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M17 21v-2a4 4 0 0 0-4-4H5a4 4 0 0 0-4 4v2"/><circle cx="9" cy="7" r="4"/><path d="M23 21v-2a4 4 0 0 0-3-3.87"/><path d="M16 3.13a4 4 0 0 1 0 7.75"/></svg></div>
                    <h4>Portaria e Recepção</h4>
                    <p>Equipe treinada para triagem de visitantes e suporte imediato aos condôminos.</p>
                </div>
                <div class="card-estrutura">
                    <div class="icone"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><polyline points="8 17 12 21 16 17"/><polyline points="8 7 12 3 16 7"/><line x1="12" y1="21" x2="12" y2="3"/></svg></div>
                    <h4>Elevadores</h4>
                    <p>Elevadores sociais dedicados ao fluxo de todos os pavimentos comerciais.</p>
                </div>
                <div class="card-estrutura">
                    <div class="icone"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><path d="M3 9h18"/><path d="M9 21V9"/></svg></div>
                    <h4>Garagem</h4>
                    <p>Vagas para condôminos e visitantes, com acesso monitorado por câmeras.</p>
                </div>
                <div class="card-estrutura">
                    <div class="icone"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><polygon points="13 2 3 14 12 14 11 22 21 10 12 10 13 2"/></svg></div>
                    <h4>Gerador de Energia</h4>
                    <p>Sistema de emergência para manter áreas comuns e serviços essenciais.</p>
                </div>
                <div class="card-estrutura">
                    <div class="icone"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M12 2a14.5 14.5 0 0 0 0 20 14.5 14.5 0 0 0 0-20"/><path d="M2 12h20"/></svg></div>
                    <h4>Ar-Condicionado Central</h4>
                    <p>Climatização nas áreas comuns e no centro de convenções.</p>
                </div>
                <div class="card-estrutura">
                    <div class="icone"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="5" r="3"/><path d="M12 22V8"/><path d="M5 12h14"/><path d="M5 22l7-10 7 10"/></svg></div>
                    <h4>Acessibilidade</h4>
                    <p>Rotas e acessos pensados para pessoas com mobilidade reduzida.</p>
                </div>
                <div class="card-estrutura">
                    <div class="icone"><svg width="20" height="20" fill="none" stroke="currentColor" stroke-width="2"><path d="M14.7 6.3a1 1 0 0 0 0 1.4l1.6 1.6a1 1 0 0 0 1.4 0l3.77-3.77a6 6 0 0 1-7.94 7.94l-6.91 6.91a2.12 2.12 0 0 1-3-3l6.91-6.91a6 6 0 0 1 7.94-7.94l-3.76 3.76z"/></svg></div>
                    <h4>Manutenção Predial</h4>
                    <p>Equipe de zeladoria e manutenção contínua das áreas comuns.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- ================================================================
         CENTRO DE CONVENÇÕES — Galeria cinematográfica e interativa
         Abas por categoria + cards com tilt 3D + lightbox fullscreen
    ================================================================= -->
    <section id="convencoes" class="section section-convencoes">
        <div class="container">
            <div class="section-header text-center">
                <div class="eyebrow reveal">Estrutura Completa</div>
                <h2 class="section-title reveal">Centro de Convenções</h2>
                <p class="section-subtitle reveal" style="max-width: 620px; margin: 0 auto;">Salas de alto padrão equipadas para palestras, reuniões executivas e treinamentos, garantindo a excelência do seu evento.</p>
            </div>

            <div class="conv-tabs reveal" role="tablist" aria-label="Filtrar espaços do Centro de Convenções">
                <?php foreach ($abas_convencoes as $tab_key => $tab_label): ?>
                    <button type="button" class="conv-tab<?= $tab_key === 'todos' ? ' active' : '' ?>" data-conv-tab="<?= htmlspecialchars($tab_key, ENT_QUOTES, 'UTF-8') ?>" role="tab" aria-selected="<?= $tab_key === 'todos' ? 'true' : 'false' ?>"><?= htmlspecialchars($tab_label, ENT_QUOTES, 'UTF-8') ?></button>
                <?php endforeach; ?>
            </div>

            <div class="conv-gallery" id="convGallery">
                <?php foreach ($conv_items as $i => $item): ?>
                    <article class="conv-card reveal" data-categoria="<?= $item['categoria'] ?>" style="--d:<?= number_format($i * 0.08, 2, '.', '') ?>s">
                        <div class="conv-card-media" data-conv-open="<?= (int)$i ?>" tabindex="0" role="button" aria-label="Ampliar imagem: <?= $item['legenda_esc'] ?>">
                            <img src="<?= $item['src_esc'] ?>" loading="lazy" alt="<?= $item['legenda_esc'] ?>">
                            <span class="conv-card-shine" aria-hidden="true"></span>
                            <span class="conv-card-expand" aria-hidden="true">
                                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M15 3h6v6"/><path d="M9 21H3v-6"/><path d="M21 3l-7 7"/><path d="M3 21l7-7"/></svg>
                            </span>
                        </div>
                        <div class="conv-card-info">
                            <span class="conv-card-tag"><?= $item['label_esc'] ?></span>
                            <h4><?= $item['legenda_esc'] ?></h4>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Lightbox fullscreen -->
        <div class="conv-lightbox" id="convLightbox" aria-hidden="true" role="dialog" aria-modal="true" aria-label="Visualização ampliada do espaço">
            <div class="conv-lightbox-backdrop" data-conv-close></div>
            <button type="button" class="conv-lightbox-close" data-conv-close aria-label="Fechar visualização">
                <svg width="22" height="22" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round"><line x1="18" y1="6" x2="6" y2="18"/><line x1="6" y1="6" x2="18" y2="18"/></svg>
            </button>
            <button type="button" class="conv-lightbox-nav prev" id="convPrev" aria-label="Imagem anterior">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="15 18 9 12 15 6"/></svg>
            </button>
            <button type="button" class="conv-lightbox-nav next" id="convNext" aria-label="Próxima imagem">
                <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="9 18 15 12 9 6"/></svg>
            </button>
            <div class="conv-lightbox-stage">
                <img id="convLightboxImg" src="" alt="">
                <div class="conv-lightbox-caption">
                    <span class="conv-lightbox-tag" id="convLightboxTag"></span>
                    <h3 id="convLightboxTitle"></h3>
                    <span class="conv-lightbox-counter" id="convLightboxCounter"></span>
                </div>
            </div>
        </div>
    </section>

    <script>
    (function () {
        var CONV_DATA = <?= json_encode(array_map(function ($it) {
            return ['src' => $it['src'], 'legenda' => $it['legenda'], 'label' => $it['label']];
        }, $conv_items), JSON_UNESCAPED_UNICODE | JSON_HEX_TAG | JSON_HEX_AMP) ?>;

        var galeria   = document.getElementById('convGallery');
        var lightbox  = document.getElementById('convLightbox');
        if (!galeria || !lightbox || !CONV_DATA.length) return;

        var tabs      = document.querySelectorAll('.conv-tab');
        var cards     = galeria.querySelectorAll('.conv-card');
        var lbImg     = document.getElementById('convLightboxImg');
        var lbTag     = document.getElementById('convLightboxTag');
        var lbTitle   = document.getElementById('convLightboxTitle');
        var lbCounter = document.getElementById('convLightboxCounter');
        var btnPrev   = document.getElementById('convPrev');
        var btnNext   = document.getElementById('convNext');
        var currentIndex = 0;
        var lastFocused   = null;

        // --- Filtro por categoria ---
        tabs.forEach(function (tab) {
            tab.addEventListener('click', function () {
                tabs.forEach(function (t) { t.classList.remove('active'); t.setAttribute('aria-selected', 'false'); });
                tab.classList.add('active');
                tab.setAttribute('aria-selected', 'true');
                var alvo = tab.getAttribute('data-conv-tab');
                cards.forEach(function (card) {
                    var mostrar = (alvo === 'todos') || (card.getAttribute('data-categoria') === alvo);
                    if (mostrar) {
                        card.style.display = '';
                        requestAnimationFrame(function () { card.classList.remove('conv-card-hide'); });
                    } else {
                        card.classList.add('conv-card-hide');
                        setTimeout(function () {
                            if (card.classList.contains('conv-card-hide')) card.style.display = 'none';
                        }, 380);
                    }
                });
            });
        });

        // --- Lightbox ---
        function abrirLightbox(index) {
            currentIndex = ((index % CONV_DATA.length) + CONV_DATA.length) % CONV_DATA.length;
            atualizarLightbox();
            lastFocused = document.activeElement;
            lightbox.classList.add('open');
            lightbox.setAttribute('aria-hidden', 'false');
            document.body.style.overflow = 'hidden';
            btnNext.focus();
        }
        function fecharLightbox() {
            lightbox.classList.remove('open');
            lightbox.setAttribute('aria-hidden', 'true');
            document.body.style.overflow = '';
            if (lastFocused && typeof lastFocused.focus === 'function') lastFocused.focus();
        }
        function atualizarLightbox() {
            var item = CONV_DATA[currentIndex];
            if (!item) return;
            lbImg.style.opacity = '0';
            setTimeout(function () {
                lbImg.src = item.src;
                lbImg.alt = item.legenda;
                lbTag.textContent = item.label;
                lbTitle.textContent = item.legenda;
                lbCounter.textContent = (currentIndex + 1) + ' / ' + CONV_DATA.length;
                lbImg.style.opacity = '1';
            }, 160);
        }

        cards.forEach(function (card) {
            var media = card.querySelector('[data-conv-open]');
            if (!media) return;
            var idx = parseInt(media.getAttribute('data-conv-open'), 10);
            media.addEventListener('click', function () { abrirLightbox(idx); });
            media.addEventListener('keydown', function (e) {
                if (e.key === 'Enter' || e.key === ' ') { e.preventDefault(); abrirLightbox(idx); }
            });
        });

        document.querySelectorAll('[data-conv-close]').forEach(function (el) {
            el.addEventListener('click', fecharLightbox);
        });
        btnPrev.addEventListener('click', function () { currentIndex--; atualizarLightbox(); });
        btnNext.addEventListener('click', function () { currentIndex++; atualizarLightbox(); });

        document.addEventListener('keydown', function (e) {
            if (!lightbox.classList.contains('open')) return;
            if (e.key === 'Escape') fecharLightbox();
            if (e.key === 'ArrowLeft') { currentIndex--; atualizarLightbox(); }
            if (e.key === 'ArrowRight') { currentIndex++; atualizarLightbox(); }
        });

        // --- Swipe (touch) ---
        var touchStartX = 0;
        lightbox.addEventListener('touchstart', function (e) { touchStartX = e.changedTouches[0].screenX; }, { passive: true });
        lightbox.addEventListener('touchend', function (e) {
            var diff = e.changedTouches[0].screenX - touchStartX;
            if (Math.abs(diff) > 50) { diff < 0 ? currentIndex++ : currentIndex--; atualizarLightbox(); }
        }, { passive: true });

        // --- Tilt 3D sutil no hover (apenas desktop com mouse) ---
        if (window.matchMedia('(hover: hover) and (pointer: fine)').matches) {
            cards.forEach(function (card) {
                var media = card.querySelector('.conv-card-media');
                if (!media) return;
                media.addEventListener('mousemove', function (e) {
                    var r = media.getBoundingClientRect();
                    var px = (e.clientX - r.left) / r.width - 0.5;
                    var py = (e.clientY - r.top) / r.height - 0.5;
                    media.style.transform = 'rotateY(' + (px * 8).toFixed(2) + 'deg) rotateX(' + (py * -8).toFixed(2) + 'deg) scale3d(1.03,1.03,1.03)';
                });
                media.addEventListener('mouseleave', function () { media.style.transform = ''; });
            });
        }
    })();
    </script>

    <section id="condominos" class="section">
        <div class="container">
            <div class="section-header">
                <div class="eyebrow reveal">Ecossistema de Negócios</div>
                <h2 class="section-title reveal">Nossos Condôminos</h2>
                <div class="condominos-toolbar reveal">
                    <div class="search-box">
                        <input type="text" id="busca-condomino" placeholder="Buscar empresa, especialidade ou andar..." autocomplete="off" aria-label="Buscar empresa">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" aria-hidden="true"><circle cx="11" cy="11" r="6"></circle><line x1="21" y1="21" x2="16.65" y2="16.65"></line></svg>
                    </div>
                    <div class="filtros-condominos" role="radiogroup" aria-label="Filtros de ordenação">
                        <label class="filter-pill"><input type="radio" name="filtro" id="filtro-alfabetica" checked><span>A-Z</span></label>
                        <label class="filter-pill"><input type="radio" name="filtro" id="filtro-andares"><span>Por Andar</span></label>
                    </div>
                </div>
            </div>

            <div class="grid-condominos" id="grid-condominos">
                <?php foreach ($lista_condominos as $c): 
                    $razao = $c['razao_social'] ?? '';
                    $razao_esc = htmlspecialchars($razao, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $razao_lower = htmlspecialchars(mb_strtolower($razao, 'UTF-8'), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    
                    $especialidades = $c['especialidades'] ?? '';
                    $responsavel = $c['responsavel'] ?? '';
                    $andar_int = intval($c['andar'] ?? 0);
                    $andar_esc = htmlspecialchars((string)$andar_int, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    
                    $busca_raw = mb_strtolower($razao . ' ' . $especialidades . ' ' . $responsavel . ' ' . ($c['email'] ?? '') . ' ' . $andar_int, 'UTF-8');
                    $busca_esc = htmlspecialchars($busca_raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    
                    $sala_principal = htmlspecialchars($c['sala_principal'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $salas = htmlspecialchars($c['salas'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $esp_esc = htmlspecialchars($especialidades, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $responsavel_esc = htmlspecialchars($responsavel, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $tel_esc = htmlspecialchars($c['telefones'] ?? '', ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $email_raw = trim($c['email'] ?? '');
                    $email_esc = htmlspecialchars($email_raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $email_href = htmlspecialchars('mailto:' . $email_raw, ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $url_raw = $c['url'] ?? '';
                    $url_display = htmlspecialchars(normaliza_url_barra($url_raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    $url_href = htmlspecialchars(normaliza_url_href($url_raw), ENT_QUOTES | ENT_HTML5, 'UTF-8');
                    
                    $chk_salas = !empty($c['chk_salas']);
                    $chk_esp = !empty($c['chk_especialidades']);
                    $chk_tel = !empty($c['chk_telefones']);
                    $chk_email = !empty($c['chk_email']);
                    $chk_url = !empty($c['chk_url']);
                ?>
                <div class="condomino-box"
                    data-nome="<?= $razao_lower ?>"
                    data-busca="<?= $busca_esc ?>"
                    data-andar="<?= $andar_esc ?>">
                    <div class="cond-top">
                        <h4><?= $razao_esc ?></h4>
                        <span class="andar-badge"><?= $andar_esc ?>º Andar</span>
                    </div>
                    <?php if ($chk_salas && !empty($c['salas'])): ?>
                        <p class="cond-sala"><?= $salas ?></p>
                    <?php else: ?>
                        <p class="cond-sala">Sala <?= $sala_principal ?></p>
                    <?php endif; ?>

                    <?php if (!empty($responsavel)): ?>
                        <p class="cond-responsavel"><strong>Responsável:</strong> <?= $responsavel_esc ?></p>
                    <?php endif; ?>

                    <?php if ($chk_esp && !empty($especialidades)): ?>
                        <p class="cond-esp"><strong>Especialidade:</strong> <?= $esp_esc ?></p>
                    <?php endif; ?>
                    
                    <div class="cond-footer">
                        <?php if ($chk_tel && !empty($c['telefones'])): ?>
                            <span class="cond-tel">📞 <?= $tel_esc ?></span>
                        <?php endif; ?>
                        <?php if ($chk_email && !empty($email_raw)): ?>
                            <a class="cond-email" href="<?= $email_href ?>">✉ <?= $email_esc ?></a>
                        <?php endif; ?>
                        <?php if ($chk_url && !empty($url_raw)): ?>
                            <a href="<?= $url_href ?>" target="_blank" rel="noopener noreferrer"><?= $url_display ?></a>
                        <?php endif; ?>
                    </div>
                </div>
                <?php endforeach; ?>
                
                <?php if ($total_condominos === 0): ?>
                    <p class="empty">Nenhum condômino disponível no momento.</p>
                <?php endif; ?>
            </div>
        </div>
    </section>

    <section id="contato" class="section bg-dark">
        <div class="container">
            <div class="contato-wrapper">
                <div class="contato-info">
                    <div class="eyebrow gold">Fale Conosco</div>
                    <h2>Vamos conversar<br>sobre o seu futuro.</h2>
                    <div class="info-list">
                        <div><h4>Endereço</h4><p>Av. das Américas, 1155 - Sobre Loja<br>Barra da Tijuca - RJ - 22631-000</p></div>
                        <div><h4>Telefones</h4><p>21 2499-4337<br>21 2499-4293</p></div>
                        <div><h4>E-mails</h4><p>administracao@barraspace.com.br<br>gerencia@barraspace.com.br</p></div>
                    </div>
                    <div class="mapa"><iframe src="https://www.google.com/maps/embed?pb=!1m14!1m8!1m3!1d14690.292879795523!2d-43.33296159246824!3d-23.002716192733033!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x9bd0a6bb900f7f%3A0x44c6e47a6b824528!2sAv.%20das%20Am%C3%A9ricas%2C%201155%20-%20Barra%20da%20Tijuca%2C%20Rio%20de%20Janeiro%20-%20RJ%2C%2022631-000!5e0!3m2!1spt-BR!2sbr!4v1791580622702!5m2!1spt-BR!2sbr" width="100%" height="220" style="border:0;" loading="lazy" title="Mapa Barra Space Center"></iframe></div>
                </div>
                <div class="contato-form">
                    <form id="formContato" action="enviar.php" method="POST" novalidate>
                        <input type="text" name="website" style="display:none !important" tabindex="-1" autocomplete="off">
                        <div class="form-group"><input type="text" id="nome" name="nome" required placeholder=" " autocomplete="name"><label for="nome">Nome completo *</label></div>
                        <div class="form-group"><input type="email" id="email" name="email" required placeholder=" " autocomplete="email"><label for="email">Seu e-mail *</label></div>
                        <div class="form-row">
                            <div class="form-group"><input type="text" id="assunto" name="assunto" required placeholder=" "><label for="assunto">Assunto *</label></div>
                            <div class="form-group"><select id="categoria" name="categoria" required><option value="" disabled selected></option><option value="informacoes">Informações</option><option value="sugestoes">Sugestões</option><option value="reclamacoes">Reclamações</option></select><label for="categoria">Categoria *</label></div>
                        </div>
                        <div class="form-group"><textarea id="mensagem" name="mensagem" rows="5" required placeholder=" "></textarea><label for="mensagem">Sua mensagem *</label></div>
                        <div class="form-consent">
                            <label for="consentimento">
                                <input type="checkbox" id="consentimento" name="consentimento" required>
                                <span>Concordo com o uso dos meus dados pessoais para que a Barra Space responda à minha mensagem, conforme a LGPD.</span>
                            </label>
                        </div>
                        <button type="submit" class="btn-primary full magnetic">Enviar Mensagem</button>
                        <p id="form-feedback" role="alert" aria-live="polite"></p>
                    </form>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer-premium">
        <div class="footer-top-line" aria-hidden="true"></div>
        <div class="container grid-footer">
            <div class="brand">
                <h3>BARRA SPACE CENTER</h3>
                <p>O endereço que valoriza o seu negócio. Infraestrutura de alto padrão e localização privilegiada no coração da Barra da Tijuca.</p>
            </div>
            <div class="links">
                <h4>Navegação</h4>
                <ul>
                    <li><a href="#home">Início</a></li>
                    <li><a href="#sobre">Sobre</a></li>
                    <li><a href="#estrutura">Estrutura</a></li>
                    <li><a href="#convencoes">Convenções</a></li>
                    <li><a href="#condominos">Condôminos</a></li>
                </ul>
            </div>
            <div class="contact">
                <h4>Fale Conosco</h4>
                <p>Av. das Américas, 1155</p>
                <p>Barra da Tijuca - RJ</p>
                <p>administracao@barraspace.com.br</p>
            </div>
            <div class="footer-cta">
                <h4>Agende uma Visita</h4>
                <p>Conheça pessoalmente a estrutura e o Centro de Convenções do Barra Space.</p>
                <a href="#contato" class="btn-footer-cta">Falar com a Administração</a>
            </div>
        </div>
        <div class="container footer-bottom">
            <p>© <?= $ano_atual ?> Condomínio Barra Space Center <span class="dot-sep" aria-hidden="true">•</span> Todos os direitos reservados</p>
            <div class="footer-bottom-actions">
                <a href="login.html" class="btn-administrativo" aria-label="Acesso à gerência" title="Acesso Administrativo">Administrativo</a>
                <a href="#home" class="btn-back-top" aria-label="Voltar ao topo" title="Voltar ao topo">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><line x1="12" y1="19" x2="12" y2="5"/><polyline points="5 12 12 5 19 12"/></svg>
                </a>
            </div>
        </div>
    </footer>

    <div id="toast" role="status" aria-live="polite"></div>
    <script src="script.js"></script>
</body>
</html>
