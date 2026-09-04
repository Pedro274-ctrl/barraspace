// 1. Lógica da Tela de Login (Redirecionamento Simulado)
const formLogin = document.getElementById('formLogin');

if (formLogin) {
    formLogin.addEventListener('submit', function(e) {
        e.preventDefault(); // Impede o envio real do formulário
        
        const email = document.getElementById('email').value;
        const perfil = document.getElementById('perfil').value;
        
        // Simulação: Se preencheu, "loga" com sucesso
        if(email !== '') {
            // Em um sistema real, aqui iria uma chamada ao Banco de Dados / API (PHP, Node, etc)
            alert(`Login efetuado com sucesso como ${perfil.toUpperCase()}!`);
            window.location.href = "admin.html"; // Redireciona para o Painel Administrativo
        }
    });
}

// 2. Lógica do Painel Administrativo (Menu Lateral)
const toggleBtn = document.getElementById('toggleSidebar');
const sidebar = document.getElementById('sidebar');

if (toggleBtn && sidebar) {
    toggleBtn.addEventListener('click', () => {
        sidebar.classList.toggle('hidden');
    });
}

// 3. Simulação das Ações da Tabela
function editar() {
    alert("Funcionalidade: Abrir tela de edição de dados deste condômino.");
}

function excluir() {
    if(confirm("Tem certeza que deseja remover este cadastro?")) {
        alert("Cadastro removido (Simulação).");
    }
}

document.addEventListener('DOMContentLoaded', () => {
    // Lógica de Troca de Abas (Dashboard vs Gestão)
    const menuLinks = document.querySelectorAll('.menu-link');
    const viewSections = document.querySelectorAll('.view-section');

    menuLinks.forEach(link => {
        link.addEventListener('click', (e) => {
            const targetId = link.getAttribute('data-target');
            
            if (targetId) {
                e.preventDefault(); // Evita o recarregamento da página

                // 1. Remove classe 'active' de todos os menus e adiciona no clicado
                menuLinks.forEach(l => l.classList.remove('active'));
                link.classList.add('active');

                // 2. Oculta todas as seções e exibe apenas a correspondente
                viewSections.forEach(section => {
                    section.classList.add('hidden');
                });
                
                const targetSection = document.getElementById(targetId);
                if (targetSection) {
                    targetSection.classList.remove('hidden');
                }
            }
        });
    });

    // Manter o restante do seu código JS (Sidebar toggle mobile, etc.) abaixo desta linha...
});
