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

- [ ] **Criar a página de erro 403** para acessos não autorizados.
- [ ] **Criar a página de erro 404** para páginas não encontradas.
- [ ] **Criar a página de erro 500** para erros internos do servidor.
- [ ] **Configurar o servidor web** para apresentar as páginas de erro correspondentes aos códigos HTTP.
- [ ] **Padronizar a indisponibilidade do banco de dados.** `conexao.php` já apresenta uma mensagem de manutenção em certas falhas de banco, mas ela está embutida no arquivo. Padronizar essa resposta como erro 503 e usar uma apresentação coerente com as demais páginas de erro.

## 3. Formulário de contato

- [ ] **Criar `enviar.php`** para receber o formulário de contato. Atualmente o formulário não tem destino definido e o JavaScript simula o envio sem transmitir a mensagem.
- [ ] **Validar os dados no servidor:** nome, e-mail, assunto, categoria, mensagem e consentimento.
- [ ] **Tratar o campo honeypot** no endpoint para filtrar envios automatizados.
- [ ] **Configurar o envio real** das mensagens para os e-mails da administração.
- [ ] **Atualizar o JavaScript do formulário** para chamar o endpoint e exibir sucesso somente depois da confirmação de envio; exibir erros quando necessário e permitir nova tentativa.
- [ ] **Testar os cenários do formulário:** envio válido, campos inválidos, consentimento ausente, honeypot preenchido e falha no serviço de envio.

## 4. Login e acesso

- [ ] **Corrigir o link de retorno ao site no login.** `login.html` aponta para `index.html`, que não existe; a página inicial do projeto é `index.php`.
- [ ] **Remover a recuperação de senha pela interface pública.** Não oferecer fluxo “Esqueci minha senha”; a redefinição deve ocorrer dentro do painel autenticado, com acesso de gerência e operador.
- [ ] **Alinhar os campos de login com a autenticação.** A tela diz “E-mail ou CPF” e oferece seleção de perfil, mas a autenticação consulta por e-mail e não usa o perfil. Implementar CPF e perfis, caso sejam necessários, ou ajustar a tela para informar somente o que o sistema aceita.

## 5. Painel administrativo

- [ ] **Adicionar a aba “Segurança” em `admin.php`.** A aba deve conter navegação vertical (não horizontal) para as opções “Redefinir senha” e “Usuários”, seguindo a referência visual enviada.
- [ ] **Implementar “Redefinir senha” no painel autenticado.** Permitir que gerência e operador alterem a própria senha após validar a senha atual e confirmar a nova. Armazenar com `password_hash()` e proteger contra CSRF. Não criar recuperação de senha pela interface pública.
- [ ] **Implementar a opção “Usuários” com acesso exclusivo à gerência e ao operador.** Validar o privilégio no servidor em todas as operações; não basta ocultar a opção na interface.
- [ ] **Permitir que gerência e operador criem usuários padrão.** Na opção “Usuários”, cadastrar nome do funcionário, e-mail (usado como login) e senha definida no momento da criação. Armazenar senhas com `password_hash()` e proteger as operações contra CSRF.
- [ ] **Apresentar e identificar os usuários conforme a referência.** Exibir em tabela Login, Nome, E-mail, Ativo e Privilégio de administrador. A conta da gerente deve aparecer como login `admin`, nome `Eliza` e e-mail `gerencia@barraspace.com.br`; a conta `operador` terá privilégios administrativos completos; os demais usuários serão funcionários cadastrados pela gerência ou operador, sem criação automática.
- [ ] **Exibir o nome de quem está autenticado no cabeçalho do painel.** Quando Eliza estiver logada, mostrar “Olá, Eliza” em vez de “Olá, Administração”; para um usuário padrão, mostrar o nome do funcionário associado à conta.
- [ ] **Definir o destino dos itens de menu incompletos:** “Centro de Convenções”, “Comunicados” e “Financeiro (Boletos)” estão como links `#`. Implementar as respectivas áreas ou ocultar/desabilitar os itens até que estejam disponíveis.
- [ ] **Adicionar proteção CSRF** às operações administrativas, especialmente cadastro, atualização e exclusão.
- [ ] **Revisar a exclusão de registros.** Atualmente a exclusão é acionada por `GET`; mudar para uma operação apropriada com proteção CSRF e confirmação.
- [ ] **Padronizar as respostas de erro dos formulários administrativos.** Exibir mensagens amigáveis para o usuário e registrar detalhes técnicos nos logs, sem revelar informações internas.

## 6. Privacidade e LGPD

- [ ] **Criar uma política de privacidade** explicando como os dados enviados pelo formulário são utilizados.
- [ ] **Vincular a política ao consentimento** do formulário de contato. Hoje o texto menciona a LGPD, mas não há uma página de privacidade identificada no projeto.

## 7. Testes e validação final

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
- [x] Cadastro, edição e exclusão de condôminos.
- [x] Filtros e exportação no painel.
- [ ] O formulário de contato ainda não envia mensagens: no momento, o sucesso é apenas simulado no navegador.