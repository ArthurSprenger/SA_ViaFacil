CREATE DATABASE IF NOT EXISTS sa_viafacil_db DEFAULT CHARACTER SET utf8mb4;
USE sa_viafacil_db;

CREATE TABLE IF NOT EXISTS usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nome VARCHAR(100) NOT NULL,
    email VARCHAR(100) NOT NULL UNIQUE,
    senha VARCHAR(255) NOT NULL,
    cep VARCHAR(10) NULL,
    logradouro VARCHAR(255) NULL,
    numero VARCHAR(20) NULL,
    complemento VARCHAR(100) NULL,
    bairro VARCHAR(100) NULL,
    cidade VARCHAR(100) NULL,
    uf VARCHAR(2) NULL,
    tipo ENUM('normal','admin') NOT NULL DEFAULT 'normal',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    foto_perfil VARCHAR(255) DEFAULT 'default.jpg'
);

INSERT INTO usuarios (nome, email, senha, tipo)
SELECT 'Usuário Normal', 'usuario@exemplo.com', MD5('senha123'), 'normal'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='usuario@exemplo.com');

INSERT INTO usuarios (nome, email, senha, tipo)
SELECT 'Administrador', 'admin@exemplo.com', MD5('admin123'), 'admin'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='admin@exemplo.com');

INSERT INTO usuarios (nome, email, senha, tipo)
SELECT 'Operador Pátio', 'operador@exemplo.com', MD5('operador123'), 'normal'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='operador@exemplo.com');

INSERT INTO usuarios (nome, email, senha, tipo)
SELECT 'Felipe Costa', 'felipe@viafacil.com', MD5('felipe123'), 'admin'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='felipe@viafacil.com');

CREATE TABLE IF NOT EXISTS solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    estacao VARCHAR(100) NOT NULL,
    horario VARCHAR(20) NOT NULL,
    situacao VARCHAR(50) NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
);

CREATE TABLE IF NOT EXISTS sensor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(60) NOT NULL,
    descricao VARCHAR(255) NULL,
    status ENUM('ativo','inativo','manutencao') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS sensor_data (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_sensor INT NOT NULL,
    valor DECIMAL(10,3) NOT NULL,
    unidade VARCHAR(16) NOT NULL, 
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sensor) REFERENCES sensor(id) ON DELETE CASCADE
);

INSERT INTO sensor (tipo, descricao, status) VALUES
('temperatura_freio', 'Sensor de temperatura dos freios - Locomotiva A', 'ativo'),
('vibracao_motor', 'Sensor de vibração do motor principal - Locomotiva A', 'ativo');

INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora) VALUES
(1, 84.5, '°C', DATE_SUB(NOW(), INTERVAL 10 MINUTE)),
(1, 86.2, '°C', DATE_SUB(NOW(), INTERVAL 5 MINUTE)),
(1, 87.1, '°C', NOW()),
(2, 3.20, 'mm/s', DATE_SUB(NOW(), INTERVAL 12 MINUTE)),
(2, 3.45, 'mm/s', DATE_SUB(NOW(), INTERVAL 6 MINUTE)),
(2, 3.60, 'mm/s', NOW());
