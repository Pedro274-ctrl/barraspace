#!/bin/bash
echo "=== INSTALADOR BARRASPACE - UBUNTU ==="
sudo apt update
sudo apt install -y apache2 mysql-server php php-mysql php-cli php-mbstring libapache2-mod-php

echo "Iniciando serviços..."
sudo systemctl enable --now apache2 mysql

echo "Configurando banco..."
sudo mysql <<EOF
CREATE DATABASE IF NOT EXISTS barraspace CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER IF NOT EXISTS 'barraspace'@'localhost' IDENTIFIED BY '123456';
GRANT ALL PRIVILEGES ON barraspace.* TO 'barraspace'@'localhost';
FLUSH PRIVILEGES;
EOF

echo "Criando tabelas..."
mysql -u barraspace -p123456 barraspace < banco.sql

echo ""
echo "✅ Pronto! Agora rode:"
echo "cd $(pwd) && php -S localhost:8000"
echo "e acesse http://localhost:8000/setup.php"
