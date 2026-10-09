CREATE DATABASE IF NOT EXISTS barraspace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE barraspace;

CREATE TABLE IF NOT EXISTS gerencia (
  id INT AUTO_INCREMENT PRIMARY KEY,
  email VARCHAR(100) NOT NULL UNIQUE,
  senha VARCHAR(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Esquema inicial: IF NOT EXISTS preserva tabelas e registros existentes.
CREATE TABLE IF NOT EXISTS condominos (
  id INT AUTO_INCREMENT PRIMARY KEY,
  ativo TINYINT(1) DEFAULT 1,
  razao_social VARCHAR(150) NOT NULL,
  andar VARCHAR(50) NOT NULL,
  sala_principal VARCHAR(20) NOT NULL,
  salas VARCHAR(100),
  chk_salas TINYINT(1) DEFAULT 1,
  atividade VARCHAR(100),
  chk_atividade TINYINT(1) DEFAULT 1,
  especialidades VARCHAR(150),
  chk_especialidades TINYINT(1) DEFAULT 1,
  telefones VARCHAR(100),
  chk_telefones TINYINT(1) DEFAULT 1,
  responsavel VARCHAR(100),
  chk_responsavel TINYINT(1) DEFAULT 0,
  email VARCHAR(100) NOT NULL,
  chk_email TINYINT(1) DEFAULT 1,
  url VARCHAR(150),
  chk_url TINYINT(1) DEFAULT 1,
  status ENUM('Adimplente', 'Pendente') DEFAULT 'Adimplente'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

CREATE TABLE IF NOT EXISTS schema_migrations (
  version VARCHAR(32) PRIMARY KEY,
  description VARCHAR(255) NOT NULL,
  applied_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
