-- Tabelas para sistema de notificações

CREATE TABLE IF NOT EXISTS avisos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    titulo VARCHAR(255) NOT NULL,
    mensagem TEXT NOT NULL,
    usuario_id INT NOT NULL,
    criado_em DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

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
    FOREIGN KEY (usuario_destinatario_id) REFERENCES usuarios(id) ON DELETE CASCADE
);

INSERT INTO avisos (titulo, mensagem, usuario_id) VALUES
('INTERDIÇÃO TEMPORÁRIA - LINHA 47', 'Manutenção programada para hoje às 14h', 1),
('MANUTENÇÃO PROGRAMADA - LINHA 33', 'Trecho entre estações Central e Vila Nova', 1),
('OBJETO NA VIA - LINHA 63', 'Aguardando remoção. Previsão: 30 minutos', 1);
