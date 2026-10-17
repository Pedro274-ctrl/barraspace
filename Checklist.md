# Checklist de pendências do projeto Barra Space

Lista organizada por prioridade e ordem sugerida de execução. Marque cada item conforme for concluído.

## 1. Preparação e instalação segura

- [x] **Remover o instalador obsoleto.** O script orientava acessar `setup.php`, que não existe no projeto. O `instalar.sh` foi removido; a preparação do ambiente local e da hospedagem deve ser documentada separadamente.
- [x] **Remover credenciais padrão.** A conexão exige `DB_USER` e `DB_PASS`, sem usuário ou senha padrão. O modelo versionado `conexao.example.php` e as instruções locais estão no `README.md`; nenhum segredo é incluído no repositório.
- [x] **Proteger os dados do banco durante instalações e atualizações.** `banco.sql` agora cria tabelas somente quando ausentes, sem apagar ou alterar as existentes. As instruções para migrações numeradas, backup e registro em `schema_migrations` estão no `README.md`.
- [x] **Documentar instalação e operação.** `README.md` agora cobre requisitos, instalação local no Ubuntu, variáveis de ambiente, publicação, backups e migrações seguras.
- [x] **Definir a criação da primeira conta administrativa.** `criar_admin.php` permite cadastrar a conta inicial somente pela CLI, verifica se já há conta e grava a senha com `password_hash()`.
- [x] **Implementar a alteração segura de senha** para contas administrativas existentes por meio do utilitário `atualizar_admin.php`, disponível somente pela CLI.
- [x] **Aplicar a migração 001 de perfis no banco local.** Confirmado em 08/10/2026: a conta `gerencia@barraspace.com.br` está como `Eliza` com perfil `gerencia`, e a versão `001` consta em `schema_migrations`.
- [x] **Criar a conta `operador`.** Conta provisionada no banco local pelo utilitário `criar_operador.php`, com senha definida no terminal e armazenada com `password_hash()`; nenhuma credencial foi adicionada ao repositório.

## 2. Tratamento de erros do site

- [x] **Criar a página de erro 403** para acessos não autorizados em `403.php` e apresentá-la quando a autorização administrativa for negada em `auth.php`.
- [x] **Criar a página de erro 404** para páginas não encontradas em `404.php`, com resposta HTTP 404.
- [x] **Criar a página de erro 500** para erros internos do servidor em `500.php`, com resposta HTTP 500.
- [ ] **Ativar e validar o VirtualHost Apache local** para as páginas 403, 404 e 500. A configuração está em `apache-vhost-local.conf`; seguir os comandos documentados no `README.md` usando uma conta com `sudo` e testar cada código HTTP.
- [ ] **Configurar e validar as páginas 403, 404 e 500 na hospedagem de produção da UOL pelo cPanel** ao publicar o site; testar os códigos HTTP no domínio de produção.
- [x] **Padronizar a indisponibilidade do banco de dados.** `conexao.php` e `conexao.example.php` respondem com HTTP 503; respostas HTML usam `503.php`, enquanto requisições AJAX mantêm resposta JSON sem expor detalhes técnicos.

## 3. Formulário de contato

- [x] **Criar `enviar.php`** como endpoint POST do formulário. Até configurar o transporte de e-mail, solicitações válidas respondem HTTP 503 sem afirmar que foram enviadas.
- [x] **Validar no servidor** nome, e-mail, assunto, categoria, mensagem e consentimento.
- [x] **Tratar o campo honeypot** no endpoint para rejeitar envios automatizados.
- [x] **Integrar o envio SMTP ao formulário** com PHPMailer; o destinatário de teste é `pedro274.phh@gmail.com`, configurável por `CONTACT_TO`.
- [ ] **Configurar as credenciais SMTP privadas e validar a entrega real** no ambiente local e na hospedagem; antes de produção, trocar `CONTACT_TO` pelos e-mails da administração.
- [x] **Atualizar o JavaScript do formulário** para chamar o endpoint, exibir sucesso somente após confirmação e apresentar erros permitindo nova tentativa.
- [ ] **Testar os cenários do formulário:** envio válido, campos inválidos, consentimento ausente, honeypot preenchido e falha no serviço de envio.

## 4. Login e acesso

- [x] **Corrigir o link de retorno ao site no login.** O link em `login.html` aponta para `index.php`, a página inicial do projeto.
- [x] **Remover a recuperação de senha pela interface pública.** A senha agora pode ser alterada dentro do painel autenticado por contas de gerência e operador.
- [x] **Alinhar os campos de login com a autenticação.** A tela solicita somente o e-mail; o perfil é determinado pela conta autenticada.

## 5. Painel administrativo

- [x] **Adicionar a aba “Segurança” em `admin.php`.** A aba contém navegação vertical para as opções “Redefinir senha” e “Usuários”.
- [x] **Implementar “Redefinir senha” no painel autenticado.** Gerência e operador podem alterar a própria senha após validar a senha atual e confirmar a nova; a senha é armazenada com `password_hash()` e a operação é protegida contra CSRF. Não há recuperação de senha pública.
- [x] **Restringir a opção “Usuários” à gerência e ao operador.** O servidor valida o perfil ao carregar a opção e ao cadastrar cada conta; operações futuras de gerenciamento também deverão reutilizar essa validação.
- [x] **Permitir que gerência e operador criem usuários padrão.** O cadastro solicita nome, e-mail de login e senha com confirmação, armazena com `password_hash()` e usa CSRF. O usuário padrão pode somente gerenciar condôminos e não acessa Segurança.
- [x] **Apresentar e identificar os usuários conforme a referência.** A tabela exibe Login, Nome, E-mail, Ativo e Privilégio de administrador; contas de gerência e operador recebem os logins `admin` e `operador`, e funcionários usam o e-mail como login. A migração 002 adiciona o estado ativo e contas inativas não podem autenticar.
- [ ] **Exibir o nome de quem está autenticado no cabeçalho do painel.** Quando Eliza estiver logada, mostrar “Olá, Eliza” em vez de “Olá, Administração”; para um usuário padrão, mostrar o nome do funcionário associado à conta.
- [ ] **Definir o destino dos itens de menu incompletos:** “Centro de Convenções”, “Comunicados” e “Financeiro (Boletos)” estão como links `#`. Implementar as respectivas áreas ou ocultar/desabilitar os itens até que estejam disponíveis.
- [ ] **Adicionar proteção CSRF** às operações administrativas, especialmente cadastro, atualização e exclusão.
- [ ] **Revisar a exclusão de registros.** Atualmente a exclusão é acionada por `GET`; mudar para uma operação apropriada com proteção CSRF e confirmação.
- [ ] **Padronizar as respostas de erro dos formulários administrativos.** Exibir mensagens amigáveis para o usuário e registrar detalhes técnicos nos logs, sem revelar informações internas.

## 6. Privacidade e LGPD

- [ ] **Criar uma política de privacidade** explicando como os dados enviados pelo formulário são utilizados.
- [ ] **Vincular a política ao consentimento** do formulário de contato. Hoje o texto menciona a LGPD, mas não há uma página de privacidade identificada no projeto.

## 7. Responsividade e compatibilidade

Levantamento inicial: `index.php`, `login.html` e `admin.php` já declaram a meta viewport. `style.css` tem ajustes para telas de até 1024px, 768px e 640px, menu móvel, grades flexíveis e suporte a preferência por movimento reduzido. `admin.css` tem ajustes até 768px, sidebar móvel, filtros em coluna, áreas de toque ampliadas e tabela com rolagem horizontal. Porém, `editar.php` e `novo_condomino.php` não declaram a meta viewport e usam estilos próprios/in-line; não há evidência de testes que confirmem o funcionamento nos navegadores e dispositivos abaixo.

- [ ] **Substituir por imagens locais todos os links externos de imagens identificados no mapeamento.** Salvar os arquivos nas pastas organizadas do projeto, atualizar referências em PHP, HTML e CSS (incluindo imagens de fundo e metadados sociais) e verificar carregamento, dimensões e caminhos.
- [x] **Usar o logo local `imagens/logo1.png` no cabeçalho do site e na tela de login.**
- [x] **Usar no fundo de `login.html` a mesma imagem local do hero** (`imagens/hero/1.jpeg`), com uma camada escura para manter a leitura do formulário.
- [x] **Adicionar o cursor animado do site público ao login e ao painel administrativo**, compartilhando a implementação e respeitando dispositivos de toque e a preferência por movimento reduzido.
- [ ] **Adicionar favicons locais do projeto.** Incluir os arquivos necessários na pasta de imagens, configurar as referências `rel="icon"` nas páginas e validar a exibição nos navegadores e dispositivos previstos.
- [ ] **Adicionar a meta viewport às páginas `editar.php` e `novo_condomino.php`** e revisar o layout dos formulários para telas estreitas, sem rolagem horizontal indevida.
- [ ] **Revisar o site público em celular e tablet:** navegação/menu, hero e botões, seções e grades, lista de condôminos, formulário de contato, galeria/lightbox e rodapé.
- [ ] **Corrigir o menu móvel quando aberto após rolar a página.** Na captura analisada, o painel do menu fica restrito à altura do cabeçalho e os links se sobrepõem ao conteúdo. Investigar a interação entre o `backdrop-filter` do cabeçalho no estado `scrolled` e o posicionamento `fixed` do menu; fazer o menu cobrir corretamente a tela sem interferir na navegação da página.
- [ ] **Investigar e corrigir o fundo do hero que não carrega no Firefox.** A imagem `PredioTransp1200.jpg` responde no servidor, mas a verificação HTTPS encontrou o certificado de `www.barraspace.com.br` expirado em 01/10/2026. Verificar/renovar o certificado no servidor e confirmar no painel de rede do Firefox que a imagem é carregada sem erro de certificado; validar também no Chrome após limpar ou ignorar cache, sem contornar a validação TLS no site.
- [ ] **Investigar os travamentos no Firefox com ferramentas de desempenho antes de alterar o JavaScript.** Registrar um perfil reproduzível, identificar tarefas/loops que consumam tempo (incluindo o `requestAnimationFrame` contínuo do cursor em `script.js`) e corrigir somente o que as medições confirmarem; comparar fluidez antes/depois.
- [ ] **Revisar login e painel administrativo em telas pequenas:** sidebar, cabeçalho, cartões, filtros, ações e formulários; manter a tabela utilizável em celular com rolagem horizontal somente em sua área.
- [ ] **Validar em iPhone com Safari e em Android com Chrome**, incluindo orientação retrato/paisagem e diferentes larguras de tela.
- [ ] **Validar também em navegadores desktop atuais:** Chrome, Firefox, Edge e Safari; corrigir incompatibilidades de layout ou interação encontradas.
- [ ] **Verificar os critérios de uso fluido:** conteúdo sem cortes, sem rolagem horizontal da página, textos legíveis, controles acessíveis por toque e teclado, imagens ajustadas, zoom funcional e respeito à preferência por movimento reduzido.
- [ ] **Registrar os resultados da matriz de testes** por página, navegador, dispositivo/largura e orientação; repetir a validação após mudanças de CSS ou componentes.

## 8. Testes e validação final

- [ ] **Verificar as páginas de erro** e seus códigos HTTP no servidor.
- [ ] **Testar instalação inicial e atualização do banco** sem perda de dados.
- [ ] **Testar login válido e inválido**, redefinição de senha dentro do painel e autorização da gerência e do operador; confirmar que não há recuperação de senha pública.
- [ ] **Testar cadastro, edição e exclusão** no painel administrativo, incluindo proteção CSRF e respostas de erro.
- [ ] **Testar os links e menus do site e do painel** para garantir que apontem para destinos existentes ou estejam claramente indisponíveis.
- [ ] **Criar testes automatizados ou uma rotina de verificação manual** para envio bem-sucedido e falho do contato, erros HTTP, login e operações de cadastro do painel. Não foram identificados testes automatizados na estrutura analisada.

## Funcionalidades já identificadas no projeto

- [x] Site público.
- [x] Listagem pública de condôminos.
- [x] Login administrativo.
- [ ] Responsividade e compatibilidade verificadas nos navegadores e dispositivos previstos.
- [x] Cadastro, edição e exclusão de condôminos.
- [x] Filtros e exportação no painel.
- [ ] O formulário de contato ainda não envia mensagens: no momento, o sucesso é apenas simulado no navegador.