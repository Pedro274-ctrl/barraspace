// Funcionalidade do Menu Mobile (Hamburger)
const mobileMenu = document.getElementById('mobile-menu');
const nav = document.getElementById('menu');

mobileMenu.addEventListener('click', () => {
    nav.classList.toggle('active');
});

// Fechar menu mobile ao clicar em um link
const navLinks = document.querySelectorAll('nav ul li a');
navLinks.forEach(link => {
    link.addEventListener('click', () => {
        if (nav.classList.contains('active')) {
            nav.classList.remove('active');
        }
    });
});

// Interação simples no Formulário de Contato
const formContato = document.getElementById('formContato');

formContato.addEventListener('submit', function(e) {
    e.preventDefault(); // Evita recarregar a página (comportamento padrão)
    
    // Coletando os dados inseridos
    const nome = document.getElementById('nome').value;
    const categoria = document.getElementById('categoria').value;
    
    // Feedback visual (pode ser trocado por um Modal/SweetAlert depois)
    alert(`Obrigado, ${nome}! Sua mensagem sobre '${categoria}' foi registrada com sucesso. Em breve a administração do Barra Space Center entrará em contato.`);
    
    // Limpar o formulário
    formContato.reset();
});

// Smooth Scroll para os links de navegação âncora
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function (e) {
        // Se for o botão para área restrita/login, não faz scroll pois será outra página na fase 3
        if(this.getAttribute('href') === '#login') {
            e.preventDefault();
            alert("Área Restrita do Condômino. Esta funcionalidade será implementada na Próxima Fase (Painel Backend).");
            return;
        }

        e.preventDefault();
        const targetId = this.getAttribute('href');
        const targetElement = document.querySelector(targetId);

        if(targetElement) {
            window.scrollTo({
                top: targetElement.offsetTop - 90, // Subtrai o tamanho do header fixo
                behavior: 'smooth'
            });
        }
    });
});