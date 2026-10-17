# Migrações do banco de dados

`banco.sql` cria o esquema inicial. Alterações em bancos já instalados são aplicadas com arquivos SQL numerados nesta pasta.

## Migração 001: perfis de contas administrativas

O arquivo `001_perfis_contas_gerencia.sql` adiciona nome e perfil à tabela `gerencia`. As contas existentes recebem o perfil `usuario` por segurança; a conta `gerencia@barraspace.com.br`, se já existir, é identificada como gerente e recebe o nome `Eliza`.

### Estado do banco local

A migração foi aplicada no banco local `barraspace` em 08/10/2026. A consulta confirmou a conta `gerencia@barraspace.com.br` com nome `Eliza` e perfil `gerencia`, e a versão `001` registrada em `schema_migrations`. Não reaplique a migração nesse banco.

Na execução local, a versão foi registrada sem descrição, portanto a coluna `description` ficou vazia. Isso não desfaz a migração; o registro pode ser complementado posteriormente sem reaplicar o `ALTER TABLE`.

## Estado da conta operador

A conta `operador` também já foi criada no banco local pelo utilitário `criar_operador.php`. Credenciais não são registradas nesta documentação nem no repositório.

Faça backup antes da migração. Execute a migração 001 uma única vez, em uma cópia de teste primeiro, com um usuário MySQL autorizado a alterar a tabela:

```bash
sudo mysql barraspace < migrations/001_perfis_contas_gerencia.sql
```

Depois de confirmar que foi aplicada com sucesso, registre a versão:

```bash
sudo mysql barraspace -e "INSERT INTO schema_migrations (version, description) VALUES ('001', 'Adiciona nome e perfil às contas administrativas')"
```

Verifique `schema_migrations` antes de executar para evitar reaplicar a alteração. Se a migração falhar parcialmente, pare e compare a estrutura da tabela com o arquivo antes de continuar; não tente executar `ALTER TABLE` novamente às cegas.

## Migração 002: estado ativo das contas

O arquivo `002_ativar_contas_gerencia.sql` adiciona `ativo` à tabela `gerencia`, com valor padrão `1`, preservando as contas existentes como ativas. O login recusa contas inativas e a lista de Segurança exibe esse estado.

Faça backup e confira `schema_migrations` antes de aplicar. Em bancos em que a versão `002` ainda não foi registrada, execute:

```bash
sudo mysql barraspace < migrations/002_ativar_contas_gerencia.sql
sudo mysql barraspace -e "INSERT INTO schema_migrations (version, description) VALUES ('002', 'Adiciona estado ativo às contas')"
```

Confirme a coluna e o registro da versão antes de liberar a aplicação atualizada. Não aplique o `ALTER TABLE` novamente se `ativo` já existir.

## Próximas migrações

Crie arquivos SQL numerados e ordenados, por exemplo `003_descricao_curta.sql`. Cada arquivo deve conter somente a alteração correspondente, sem recriar o banco inteiro ou apagar tabelas e registros sem uma estratégia explícita de preservação e reversão. Faça e verifique backup antes de aplicar; registre a versão em `schema_migrations` somente depois de confirmar o sucesso.

Não crie uma migração para repetir a instalação inicial. Aplique cada versão uma única vez em cada banco.
