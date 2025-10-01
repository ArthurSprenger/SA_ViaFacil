CREATE DATABASE IF NOT EXISTS login_db;
USE login_db;

CREATE TABLE IF NOT EXISTS usuarios (
    pk INT AUTO_INCREMENT PRIMARY KEY,
    username VARCHAR(120) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cargo ENUM('adm','func') NOT NULL
);

-- Usuário de exemplo (apenas protótipo; não use senha simples em produção)
INSERT INTO usuarios (username, senha, cargo) VALUES ('admin', '123', 'adm')
ON DUPLICATE KEY UPDATE username=VALUES(username);
