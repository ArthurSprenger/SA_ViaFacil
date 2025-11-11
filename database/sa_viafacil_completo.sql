-- ============================================================
-- DATABASE: SA_VIAFACIL - SCRIPT COMPLETO
-- Sistema de Gerenciamento Ferroviário Via Fácil
-- Autor: Felipe Costa
-- Data: 06/11/2025
-- ============================================================

-- Criação do banco de dados
CREATE DATABASE IF NOT EXISTS sa_viafacil_db DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE sa_viafacil_db;

-- ============================================================
-- TABELA: USUARIOS
-- Descrição: Armazena informações dos usuários do sistema
-- ============================================================
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
    status ENUM('pendente','aprovado','rejeitado') NOT NULL DEFAULT 'pendente',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    foto_perfil VARCHAR(255) DEFAULT 'default.jpg',
    INDEX idx_email (email),
    INDEX idx_tipo (tipo),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: SOLICITACOES
-- Descrição: Registra solicitações dos usuários relacionadas a estações
-- ============================================================
CREATE TABLE IF NOT EXISTS solicitacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    tipo VARCHAR(60) NOT NULL DEFAULT 'geral',
    estacao VARCHAR(120) NOT NULL,
    horario DATETIME NULL,
    descricao TEXT NULL,
    prioridade VARCHAR(20) NOT NULL DEFAULT 'media',
    status VARCHAR(20) NOT NULL DEFAULT 'pendente',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_tipo (tipo),
    INDEX idx_prioridade (prioridade),
    INDEX idx_status (status),
    INDEX idx_criado (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: SENSOR
-- Descrição: Catálogo de sensores IoT do sistema ferroviário
-- ============================================================
CREATE TABLE IF NOT EXISTS sensor (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo VARCHAR(60) NOT NULL,
    descricao VARCHAR(255) NULL,
    status ENUM('ativo','inativo','manutencao') NOT NULL DEFAULT 'ativo',
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_status (status)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: SENSOR_DATA
-- Descrição: Armazena leituras dos sensores IoT
-- ============================================================
CREATE TABLE IF NOT EXISTS sensor_data (
    id BIGINT AUTO_INCREMENT PRIMARY KEY,
    id_sensor INT NOT NULL,
    valor DECIMAL(10,3) NOT NULL,
    unidade VARCHAR(16) NOT NULL, 
    data_hora DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_sensor) REFERENCES sensor(id) ON DELETE CASCADE,
    INDEX idx_sensor (id_sensor),
    INDEX idx_data (data_hora)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: AVISOS
-- Descrição: Avisos publicados pelos administradores
-- ============================================================
CREATE TABLE IF NOT EXISTS avisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    tipo VARCHAR(20) NOT NULL DEFAULT 'informativo',
    destino VARCHAR(30) NOT NULL DEFAULT 'todos',
    status VARCHAR(20) NOT NULL DEFAULT 'ativo',
    expira_em DATETIME NULL,
    encerrado_em DATETIME NULL,
    solicitacao_id INT NULL,
    usuario_id INT NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_usuario (usuario_id),
    INDEX idx_criado (criado_em),
    INDEX idx_tipo (tipo),
    INDEX idx_destino (destino),
    INDEX idx_status (status),
    INDEX idx_expira (expira_em),
    INDEX idx_solicitacao (solicitacao_id)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- TABELA: NOTIFICACOES
-- Descrição: Sistema de notificações em tempo real via MQTT
-- ============================================================
CREATE TABLE IF NOT EXISTS notificacoes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('aviso','solicitacao','alerta') NOT NULL,
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    usuario_remetente_id INT NOT NULL,
    usuario_destinatario_id INT NULL,
    lida BOOLEAN DEFAULT FALSE,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_remetente_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (usuario_destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
    INDEX idx_tipo (tipo),
    INDEX idx_remetente (usuario_remetente_id),
    INDEX idx_destinatario (usuario_destinatario_id),
    INDEX idx_lida (lida),
    INDEX idx_criado (criado_em)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DADOS INICIAIS: USUARIOS
-- Descrição: Usuários padrão do sistema (usar bcrypt em produção)
-- ============================================================
INSERT INTO usuarios (nome, email, senha, tipo, status)
SELECT 'Usuário Normal', 'usuario@exemplo.com', MD5('senha123'), 'normal', 'aprovado'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='usuario@exemplo.com');

INSERT INTO usuarios (nome, email, senha, tipo, status)
SELECT 'Administrador', 'admin@exemplo.com', MD5('admin123'), 'admin', 'aprovado'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='admin@exemplo.com');

INSERT INTO usuarios (nome, email, senha, tipo, status)
SELECT 'Operador Pátio', 'operador@exemplo.com', MD5('operador123'), 'normal', 'aprovado'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='operador@exemplo.com');

INSERT INTO usuarios (nome, email, senha, tipo, status)
SELECT 'Felipe Costa', 'felipe@viafacil.com', MD5('felipe123'), 'admin', 'aprovado'
WHERE NOT EXISTS (SELECT 1 FROM usuarios WHERE email='felipe@viafacil.com');

-- ============================================================
-- DADOS INICIAIS: SENSORES
-- Descrição: Sensores IoT para monitoramento ferroviário
-- ============================================================
INSERT INTO sensor (tipo, descricao, status) 
SELECT 'temperatura_freio', 'Sensor de temperatura dos freios - Locomotiva A', 'ativo'
WHERE NOT EXISTS (SELECT 1 FROM sensor WHERE tipo='temperatura_freio' AND descricao LIKE '%Locomotiva A%');

INSERT INTO sensor (tipo, descricao, status)
SELECT 'vibracao_motor', 'Sensor de vibração do motor principal - Locomotiva A', 'ativo'
WHERE NOT EXISTS (SELECT 1 FROM sensor WHERE tipo='vibracao_motor' AND descricao LIKE '%Locomotiva A%');

-- ============================================================
-- DADOS INICIAIS: SENSOR_DATA (Exemplos de leituras)
-- ============================================================
INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora)
SELECT 1, 84.5, '°C', DATE_SUB(NOW(), INTERVAL 10 MINUTE)
WHERE EXISTS (SELECT 1 FROM sensor WHERE id=1)
AND NOT EXISTS (SELECT 1 FROM sensor_data WHERE id_sensor=1 AND valor=84.5);

INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora)
SELECT 1, 86.2, '°C', DATE_SUB(NOW(), INTERVAL 5 MINUTE)
WHERE EXISTS (SELECT 1 FROM sensor WHERE id=1)
AND NOT EXISTS (SELECT 1 FROM sensor_data WHERE id_sensor=1 AND valor=86.2);

INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora)
SELECT 1, 87.1, '°C', NOW()
WHERE EXISTS (SELECT 1 FROM sensor WHERE id=1)
AND NOT EXISTS (SELECT 1 FROM sensor_data WHERE id_sensor=1 AND valor=87.1);

INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora)
SELECT 2, 3.20, 'mm/s', DATE_SUB(NOW(), INTERVAL 12 MINUTE)
WHERE EXISTS (SELECT 1 FROM sensor WHERE id=2)
AND NOT EXISTS (SELECT 1 FROM sensor_data WHERE id_sensor=2 AND valor=3.20);

INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora)
SELECT 2, 3.45, 'mm/s', DATE_SUB(NOW(), INTERVAL 6 MINUTE)
WHERE EXISTS (SELECT 1 FROM sensor WHERE id=2)
AND NOT EXISTS (SELECT 1 FROM sensor_data WHERE id_sensor=2 AND valor=3.45);

INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora)
SELECT 2, 3.60, 'mm/s', NOW()
WHERE EXISTS (SELECT 1 FROM sensor WHERE id=2)
AND NOT EXISTS (SELECT 1 FROM sensor_data WHERE id_sensor=2 AND valor=3.60);

-- ============================================================
-- DADOS INICIAIS: AVISOS
-- Descrição: Avisos de exemplo para o sistema
-- ============================================================
INSERT INTO avisos (titulo, mensagem, tipo, destino, status, usuario_id, solicitacao_id)
SELECT 'INTERDIÇÃO TEMPORÁRIA - LINHA 47', 'Manutenção programada para hoje às 14h', 'alerta', 'todos', 'ativo', 2, NULL
WHERE NOT EXISTS (SELECT 1 FROM avisos WHERE titulo='INTERDIÇÃO TEMPORÁRIA - LINHA 47');

INSERT INTO avisos (titulo, mensagem, tipo, destino, status, usuario_id, solicitacao_id)
SELECT 'MANUTENÇÃO PROGRAMADA - LINHA 33', 'Trecho entre estações Central e Vila Nova', 'informativo', 'funcionarios', 'ativo', 2, NULL
WHERE NOT EXISTS (SELECT 1 FROM avisos WHERE titulo='MANUTENÇÃO PROGRAMADA - LINHA 33');

INSERT INTO avisos (titulo, mensagem, tipo, destino, status, usuario_id, solicitacao_id)
SELECT 'OBJETO NA VIA - LINHA 63', 'Aguardando remoção. Previsão: 30 minutos', 'urgente', 'todos', 'ativo', 2, NULL
WHERE NOT EXISTS (SELECT 1 FROM avisos WHERE titulo='OBJETO NA VIA - LINHA 63');

-- ============================================================
-- FIM DO SCRIPT
-- ============================================================
