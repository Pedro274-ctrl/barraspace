# Barra Space Center

Site institucional e painel administrativo para o Barra Space Center. Apresenta a estrutura do centro empresarial e centraliza a gestão das empresas condôminas em uma única aplicação web.

**Status:** Em desenvolvimento  
**Stack:** PHP, MySQL (PDO), HTML, CSS, JavaScript, Composer, PHPMailer

---

## Sobre o projeto

O projeto reúne em um só lugar a apresentação institucional do empreendimento e as rotinas de gestão. O site público exibe informações sobre o centro, infraestrutura e centro de convenções. O diretório de empresas é alimentado pelo banco de dados e mantém apenas cadastros ativos visíveis publicamente. O painel administrativo permite manter esses cadastros atualizados.

Desenvolvido como projeto real de gestão, com foco em organização de dados, controle de acesso e manutenção evolutiva.

## Funcionalidades

**Site público**
- Páginas institucionais: empreendimento, estrutura, centro de convenções, contato
- Diretório de empresas condôminas com listagem dinâmica
- Busca e ordenação no diretório
- Exibição apenas de cadastros com visibilidade ativa

**Painel administrativo**
- Consulta, cadastro, edição e exclusão de condôminos
- Filtros por visibilidade (ativo/inativo) e exportação de relatórios
- Controle de acesso por perfil:
  - Gerência: acesso total
  - Operador: gestão de cadastros
  - Usuário padrão: acesso restrito
- Alteração de senha para contas administrativas
- Autenticação com sessão e proteção de rotas

**Outros**
- Formulário de contato com validação no servidor
- Envio de e-mail via SMTP com PHPMailer (depende de configuração no ambiente)
- Senhas armazenadas com hash

## Tecnologias

- **Backend:** PHP, MySQL, PDO (consultas parametrizadas)
- **Frontend:** HTML, CSS, JavaScript (interface responsiva)
- **Dependências:** Composer, PHPMailer
- **Banco:** MySQL / MariaDB com schema em `banco.sql`
- **Infra:** Git, Apache/Nginx, SMTP

Conceitos aplicados: CRUD, autenticação e autorização (RBAC), validação server-side, consultas parametrizadas, migrações SQL, proteção de operações administrativas.

## Estrutura do projeto

```text
Barra-space/
├── index.php                  # Site institucional e diretório público de condôminos
├── login.html                 # Tela de login do painel
├── admin.php                  # Painel administrativo e gestão de contas
├── novo_condomino.php         # Formulário de cadastro de condômino
├── salvar_condomino.php       # Processamento de novos cadastros
├── editar.php                 # Formulário de edição de condômino
├── atualizar_condomino.php    # Processamento de alterações
├── excluir.php                # Exclusão de condômino
├── autenticar.php             # Validação do login
├── auth.php                   # Autenticação e autorização por perfil
├── alterar_senha.php          # Alteração de senha no painel
├── criar_usuario.php          # Cadastro de usuário pelo painel
├── logout.php                 # Encerramento da sessão
├── criar_admin.php            # Criação da conta inicial (uso via CLI)
├── criar_operador.php         # Criação da conta de operador (uso via CLI)
├── atualizar_admin.php        # Atualização de conta administrativa (uso via CLI)
├── enviar.php                 # Validação e envio do formulário de contato
├── 403.php, 404.php           # Páginas de acesso negado e página não encontrada
├── 500.php, 503.php           # Páginas de erro interno e serviço indisponível
├── conexao.example.php        # Modelo de conexão com o banco
├── conexao.php                # Configuração local ignorada pelo Git
├── banco.sql                  # Esquema inicial do banco de dados
├── migrations/                # Migrações SQL e instruções relacionadas
├── imagens/                   # Logos, imagem principal e fotos do centro
├── style.css, script.js       # Estilos e interações do site público
├── admin.css, admin.js        # Estilos e interações do painel
├── cursor.js                  # Efeito de cursor do site
├── composer.json              # Dependências PHP do projeto
├── composer.lock              # Versões bloqueadas das dependências
├── vendor/                    # Bibliotecas instaladas pelo Composer
├── .htaccess                  # Regras do servidor Apache
├── apache-error-pages.conf    # Configuração de páginas de erro do Apache
└── apache-vhost-local.conf    # Exemplo de VirtualHost para desenvolvimento local
```

O diretório `imagens/` contém as pastas `hero/` e `convencoes/`, além dos arquivos de logo. A pasta `vendor/` é gerada pelo Composer e não deve ser versionada manualmente. O arquivo `conexao.php` é local e ignorado pelo Git - use `conexao.example.php` como modelo.

## Como rodar localmente

### Requisitos

- PHP (CLI ou integrado ao servidor web)
- Extensão `pdo_mysql`
- Extensões `openssl` e `mbstring` (para SMTP seguro e UTF-8)
- Composer
- MySQL ou MariaDB
- Git

No Ubuntu:

```bash
sudo apt update
sudo apt install git php-cli php-mysql mysql-server
php --version
php -m | grep -i pdo_mysql
mysql --version
```

### Instalação

```bash
# 1. Clonar o repositório
git clone https://github.com/Pedro274-ctrl/barra-space-center.git
cd barra-space-center

# 2. Instalar dependências
composer install

# 3. Criar banco e importar schema
mysql -u root -p -e "CREATE DATABASE barra_space_center CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;"
mysql -u root -p barra_space_center < banco.sql

# 4. Configurar ambiente
cp .env.example .env
# Edite no .env:
# DB_HOST, DB_NAME, DB_USER, DB_PASS
# SMTP_HOST, SMTP_PORT, SMTP_USER, SMTP_PASS, SMTP_FROM

# 5. Rodar servidor local
php -S localhost:8000
```

Acesse `http://localhost:8000`

## Configuração de e-mail

O formulário de contato usa PHPMailer. Configure o SMTP no `.env`:

```
SMTP_HOST=smtp.seuprovedor.com
SMTP_PORT=587
SMTP_USER=seu@email.com
SMTP_PASS=sua_senha_de_app
SMTP_FROM=noreply@barraspacecenter.com.br
```

Se o SMTP não estiver configurado, o formulário valida os dados mas não envia e-mail. Isso é esperado em ambiente de desenvolvimento.

## Banco de dados

O arquivo `banco.sql` contém a estrutura das tabelas de condôminos, usuários e perfis. A aplicação usa PDO com prepared statements em todas as consultas.

Principais tabelas:
- `condominos` - dados das empresas, visibilidade, contato
- `usuarios` - contas administrativas, perfil e hash de senha

## Segurança

- Hash de senhas (password_hash)
- PDO com prepared statements
- Validação server-side de todos os formulários
- Proteção de rotas admin por sessão e perfil
- Filtros de visibilidade para exibição pública

## Escopo atual

Funcionalidades já implementadas:
- [x] Site público completo
- [x] Diretório dinâmico com busca e ordenação
- [x] CRUD de condôminos
- [x] RBAC por perfil
- [x] Filtros e exportação
- [x] Autenticação e alteração de senha
- [x] Formulário de contato com PHPMailer

Em desenvolvimento:
- [ ] Módulo Comunicados (item no menu, sem funcionalidade ainda)
- [ ] Módulo Financeiro / Boletos (item no menu, sem funcionalidade ainda)

O envio de e-mail e o funcionamento completo dependem da configuração do SMTP no servidor onde o projeto for hospedado.

## Próximos passos

- Implementação dos módulos de Comunicados e Financeiro
- Logs de auditoria de alterações no painel
- Paginação no diretório público para grandes volumes
- Testes de integração do formulário

## Autor

**Pedro Henrique Costa Santos**
Engenharia de Software - Estácio - 4º semestre - Rio de Janeiro / Remoto
GitHub: @Pedro274-ctrl | LinkedIn: pedro-henrique-costa-0a8648260

Projeto em desenvolvimento como parte do portfólio de desenvolvimento Full Stack com foco em automação e gestão.
