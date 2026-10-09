ALTER TABLE gerencia
  ADD COLUMN nome VARCHAR(100) NOT NULL DEFAULT '' AFTER email,
  ADD COLUMN perfil ENUM('gerencia', 'operador', 'usuario') NOT NULL DEFAULT 'usuario' AFTER senha;

UPDATE gerencia
SET nome = 'Eliza',
    perfil = 'gerencia'
WHERE email = 'gerencia@barraspace.com.br';
