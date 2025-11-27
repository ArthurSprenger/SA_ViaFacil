-- Atualizar sensores para corresponder aos tópicos MQTT dos dispositivos IoT
USE sa_viafacil_db;

-- Limpar dados antigos
TRUNCATE TABLE sensor_data;
DELETE FROM sensor;

-- Inserir sensores do S1
INSERT INTO sensor (tipo, descricao, status) VALUES 
('umidade', 'Sensor de umidade - S1', 'ativo'),
('temperatura', 'Sensor de temperatura - S1', 'ativo'),
('iluminacao', 'Sensor de iluminação (LDR) - S1', 'ativo');

SELECT 'Sensores atualizados com sucesso!' AS Mensagem;
SELECT * FROM sensor;
