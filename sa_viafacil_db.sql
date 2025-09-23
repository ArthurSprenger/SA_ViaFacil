CREATE DATABASE IF NOT EXISTS sa_viafacil_db DEFAULT CHARACTER SET utf8mb4;
USE sa_viafacil_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    tipo ENUM('normal','admin') NOT NULL DEFAULT 'normal',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

INSERT INTO usuarios (nome, email, senha, tipo) VALUES
('Usuário Normal', 'usuario@exemplo.com', MD5('senha123'), 'normal'),
('Administrador', 'admin@exemplo.com', MD5('admin123'), 'admin');
CREATE TABLE IF NOT EXISTS solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    estacao VARCHAR(100) NOT NULL,
    horario VARCHAR(20) NOT NULL,
    situacao VARCHAR(50) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);
