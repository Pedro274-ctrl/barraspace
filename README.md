# Barra Space Center

Site em PHP com banco de dados MySQL. O projeto não usa Composer nem dependências JavaScript instaladas via npm. Ainda não há uma versão mínima de PHP formalmente definida; use uma versão suportada pelo provedor e valide o projeto nesse ambiente antes da publicação.

## Requisitos

- PHP CLI para desenvolvimento local ou PHP integrado ao servidor web na hospedagem.
- Extensão PHP PDO MySQL (`pdo_mysql`).
- MySQL ou MariaDB compatível com o esquema em `banco.sql`.
- Git, caso esteja clonando o repositório.

No Ubuntu, o conjunto básico para desenvolvimento local pode ser instalado com:

```bash
sudo apt update
sudo apt install git php-cli php-mysql mysql-server
```

Confirme as ferramentas e a extensão:

```bash
php --version
php -m | grep -i pdo_mysql
mysql --version
```

## Configuração da conexão com o banco

O projeto usa PHP com PDO MySQL. Configure as variáveis de ambiente antes de iniciar o site:

Após clonar o repositório, copie o modelo de conexão versionado para o arquivo local que a aplicação carrega:

```bash
cp conexao.example.php conexao.php
```

`conexao.php` é ignorado pelo Git para que configurações locais não sejam publicadas. O arquivo `conexao.example.php` contém a implementação sem segredos e pode ser copiado novamente em uma instalação nova.

| Variável | Obrigatória | Padrão | Descrição |
| --- | --- | --- | --- |
| `DB_HOST` | Não | `localhost` | Endereço do servidor MySQL |
| `DB_NAME` | Não | `barraspace` | Nome do banco de dados |
| `DB_USER` | Sim | — | Usuário do banco |
| `DB_PASS` | Sim | — | Senha do usuário do banco |

`DB_USER` e `DB_PASS` não têm valores padrão. Se estiverem ausentes ou vazias, o site responde com HTTP 503 e registra a configuração ausente no log do PHP. Erros de conexão também são registrados no log, sem exibir detalhes técnicos aos visitantes.

### Desenvolvimento local no Ubuntu

1. Clone o repositório e entre na pasta do projeto.
2. Crie o banco e as tabelas iniciais:

```bash
sudo mysql < banco.sql
```

`banco.sql` cria `barraspace` e as tabelas `gerencia`, `condominos` e `schema_migrations` somente quando ainda não existem. Não remove nem atualiza tabelas existentes.

3. Aplique a migração de perfis uma única vez:

   ```bash
   sudo mysql barraspace < migrations/001_perfis_contas_gerencia.sql
   sudo mysql barraspace -e "INSERT INTO schema_migrations (version, description) VALUES ('001', 'Adiciona nome e perfil às contas administrativas')"
   ```

   A migração marca como gerente a conta existente `gerencia@barraspace.com.br` e atribui `usuario` às demais contas. Confira o histórico e a estrutura antes de executar; detalhes em [`migrations/README.md`](migrations/README.md). **No banco local deste projeto, a migração 001 já foi aplicada; não execute esses comandos novamente nesse banco.**

4. Crie um usuário MySQL próprio para a aplicação. Em um terminal MySQL administrativo (`sudo mysql`), substitua o exemplo de senha por uma senha local forte e execute:

```sql
CREATE USER 'barraspace_local'@'localhost' IDENTIFIED BY 'senha_aqui';
GRANT SELECT, INSERT, UPDATE, DELETE ON barraspace.* TO 'barraspace_local'@'localhost';
```

Não reutilize essa senha em outros serviços nem a salve em arquivos versionados. Se o usuário já existir, ajuste-o em vez de repetir `CREATE USER`.

5. Copie o modelo de conexão para o arquivo local carregado pela aplicação:

```bash
cp conexao.example.php conexao.php
```

`conexao.php` é ignorado pelo Git; não o force para o repositório. O modelo versionado não contém credenciais.

6. Inicie o site na mesma sessão de terminal. O comando pede a senha sem exibi-la e inicia o servidor de desenvolvimento do PHP:

```bash
export DB_HOST=localhost
export DB_NAME=barraspace
export DB_USER=barraspace_local
read -rsp "senha_aqui:" DB_PASS
echo
export DB_PASS
php -S 127.0.0.1:8000
```

7. Acesse `http://127.0.0.1:8000`. Pare o servidor com `Ctrl+C`.

As variáveis definidas no terminal valem apenas para aquela sessão. Ao abrir outro terminal, defina-as novamente. Não use o servidor embutido do PHP para produção.

### Hospedagem

Antes de publicar:

1. Configure um servidor web com PHP e a extensão PDO MySQL habilitados.
2. Crie um banco e um usuário exclusivos para a aplicação na hospedagem. Conceda apenas os privilégios necessários sobre esse banco.
3. Configure `DB_USER` e `DB_PASS` como variáveis de ambiente do processo PHP. Configure também `DB_HOST` e `DB_NAME` se forem diferentes dos padrões. Não salve credenciais no código, no GitHub ou em arquivos acessíveis publicamente.
4. Configure o domínio e HTTPS no servidor web e aponte a raiz pública para a pasta do site.
5. Aplique o esquema inicial apenas em um banco novo. Para um banco já existente, faça backup, compare o esquema e planeje migrações antes de qualquer alteração.
6. Desative a exibição de erros PHP para visitantes e configure o servidor para registrar detalhes em logs protegidos.
7. Como o projeto ainda não separa os arquivos públicos do código e da documentação, configure o servidor para bloquear o acesso HTTP a `.git/`, `README.md`, `Checklist.md`, `banco.sql` e `migrations/`. Não publique backups nem arquivos de configuração locais.

Os passos específicos de publicação variam conforme o provedor. O servidor embutido `php -S` é somente para desenvolvimento local.

## Operação e manutenção do banco

### Backup

Antes de uma migração ou manutenção, faça backup e confirme que o arquivo gerado pode ser lido:

```bash
mkdir -p "$HOME/backups" &&
backup="$HOME/backups/barraspace_$(date +%F_%H%M%S).sql" &&
mysqldump --single-transaction -u barraspace -p barraspace > "$backup" &&
test -s "$backup" &&
printf 'Backup salvo em %s\n' "$backup"
```

O comando solicita a senha do MySQL sem incluí-la no histórico do shell. O arquivo é salvo na pasta `backups` da sua home, fora da raiz pública do site e do repositório; proteja-o como dado sensível. Defina e teste também um procedimento de restauração antes de depender de backups em produção.

### Migrações

`banco.sql` é somente o esquema inicial. Não o use como mecanismo de atualização: `CREATE TABLE IF NOT EXISTS` preserva tabelas existentes, mas não atualiza suas colunas.

Para cada alteração futura:

1. Crie um arquivo SQL numerado em `migrations/` (por exemplo, `001_descricao_da_alteracao.sql`).
2. Faça e verifique um backup.
3. Confira se a versão ainda não consta em `schema_migrations`.
4. Aplique a migração no banco correto, seguindo a ordem numérica, e confira o resultado.
5. Só depois do sucesso, registre a versão:

   ```sql
   INSERT INTO schema_migrations (version, description)
   VALUES ('001', 'Descricao da alteracao');
   ```

Não inclua comandos destrutivos sem uma justificativa explícita, uma estratégia de preservação dos dados e um plano de reversão. Consulte também [`migrations/README.md`](migrations/README.md).

### Conta administrativa inicial

`banco.sql` cria a tabela `gerencia`, mas não cria uma conta administrativa. Depois de aplicar a migração `001`, configurar `conexao.php` e as variáveis `DB_*`, execute o utilitário abaixo no terminal, a partir da pasta do projeto:

```bash
php criar_admin.php
```

O utilitário só funciona pela CLI, recusa criar uma conta se já houver qualquer registro em `gerencia`, pede e-mail, nome exibido e senha duas vezes sem exibi-la. O nome padrão é `Eliza`. A senha deve ter entre 12 e 72 bytes e é armazenada com `password_hash()`. Não exponha nem execute esse script por uma URL.

### Conta operador

Depois de criar a conta de gerência, crie a conta de suporte `operador`:

```bash
php criar_operador.php
```

O utilitário pede um e-mail para login e uma senha exclusiva duas vezes, sem exibi-la; o nome exibido será `Operador`. Ele cria no máximo uma conta com o perfil `operador`, que tem os mesmos privilégios administrativos da gerência. A senha é armazenada com `password_hash()` e não é salva no código. Execute o script somente em terminal interativo com as variáveis `DB_*` configuradas. O painel e as operações administrativas aceitam apenas contas com perfil `gerencia` ou `operador`.

No banco local deste projeto, a conta `operador` já foi criada. Não execute o utilitário novamente nesse banco; ele recusará uma segunda conta operador.

Para alterar o e-mail e a senha de uma conta existente, execute:

```bash
php atualizar_admin.php
```

Informe o e-mail atual da conta, o novo e-mail e uma nova senha exclusiva (mínimo de 12 caracteres), duas vezes. A senha é solicitada sem ser exibida, armazenada com `password_hash()` e não deve ser passada como argumento de linha de comando. O utilitário altera somente a conta identificada e também só funciona pela CLI. Esse procedimento exige acesso autorizado ao terminal do servidor e às credenciais do banco; a redefinição pela interface, quando implementada, deve ficar restrita ao painel autenticado.

## Segurança do repositório

- `conexao.php`, `.env`, backups e quaisquer arquivos com credenciais ou dados locais não devem ser publicados.
- O arquivo `conexao.example.php` é o modelo seguro para criar a configuração local.
- Revise `git status` e o conteúdo staged antes de publicar alterações no GitHub.

> **Atenção:** embora `banco.sql` não apague tabelas existentes, faça backup antes de qualquer operação no banco, especialmente antes de aplicar migrações.
