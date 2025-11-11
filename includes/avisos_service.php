<?php

if (!function_exists('ensureAvisosSchema')) {
    function ensureAvisosSchema(mysqli $conn): void
    {
        $conn->query("CREATE TABLE IF NOT EXISTS avisos (
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
            INDEX idx_criado (criado_em)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci");

        $columns = [];
        if ($result = $conn->query('SHOW COLUMNS FROM avisos')) {
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row;
            }
            $result->close();
        }

        if (!isset($columns['tipo'])) {
            $conn->query("ALTER TABLE avisos ADD COLUMN tipo VARCHAR(20) NOT NULL DEFAULT 'informativo' AFTER mensagem");
        } else {
            $conn->query("ALTER TABLE avisos MODIFY COLUMN tipo VARCHAR(20) NOT NULL DEFAULT 'informativo'");
        }

        if (!isset($columns['destino'])) {
            $conn->query("ALTER TABLE avisos ADD COLUMN destino VARCHAR(30) NOT NULL DEFAULT 'todos' AFTER tipo");
        } else {
            $conn->query("ALTER TABLE avisos MODIFY COLUMN destino VARCHAR(30) NOT NULL DEFAULT 'todos'");
        }

        if (!isset($columns['status'])) {
            $conn->query("ALTER TABLE avisos ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'ativo' AFTER destino");
        } else {
            $conn->query("ALTER TABLE avisos MODIFY COLUMN status VARCHAR(20) NOT NULL DEFAULT 'ativo'");
        }

        if (!isset($columns['expira_em'])) {
            $conn->query("ALTER TABLE avisos ADD COLUMN expira_em DATETIME NULL AFTER status");
        }

        if (!isset($columns['encerrado_em'])) {
            $conn->query("ALTER TABLE avisos ADD COLUMN encerrado_em DATETIME NULL AFTER expira_em");
        }

        if (!isset($columns['solicitacao_id'])) {
            $conn->query("ALTER TABLE avisos ADD COLUMN solicitacao_id INT NULL AFTER encerrado_em");
        }

        if (!isset($columns['atualizado_em'])) {
            $conn->query("ALTER TABLE avisos ADD COLUMN atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER criado_em");
        } else {
            $conn->query("ALTER TABLE avisos MODIFY COLUMN atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP");
        }

        $conn->query("UPDATE avisos SET tipo='informativo' WHERE tipo IS NULL OR tipo=''");
        $conn->query("UPDATE avisos SET destino='todos' WHERE destino IS NULL OR destino=''");
        $conn->query("UPDATE avisos SET status='ativo' WHERE status IS NULL OR status=''");

        $indices = [];
        if ($resultIdx = $conn->query("SHOW INDEX FROM avisos")) {
            while ($row = $resultIdx->fetch_assoc()) {
                $indices[$row['Key_name']] = true;
            }
            $resultIdx->close();
        }

        if (!isset($indices['idx_tipo'])) {
            $conn->query("ALTER TABLE avisos ADD INDEX idx_tipo (tipo)");
        }
        if (!isset($indices['idx_status'])) {
            $conn->query("ALTER TABLE avisos ADD INDEX idx_status (status)");
        }
        if (!isset($indices['idx_destino'])) {
            $conn->query("ALTER TABLE avisos ADD INDEX idx_destino (destino)");
        }
        if (!isset($indices['idx_expira'])) {
            $conn->query("ALTER TABLE avisos ADD INDEX idx_expira (expira_em)");
        }
        if (!isset($indices['idx_solicitacao'])) {
            $conn->query("ALTER TABLE avisos ADD INDEX idx_solicitacao (solicitacao_id)");
        }
    }
}

if (!function_exists('avisosTipoOptions')) {
    function avisosTipoOptions(): array
    {
        return [
            'informativo' => 'Informativo',
            'alerta' => 'Alerta',
            'urgente' => 'Urgente',
        ];
    }
}

if (!function_exists('avisosDestinoOptions')) {
    function avisosDestinoOptions(): array
    {
        return [
            'todos' => 'Todos',
            'funcionarios' => 'Funcionários',
            'administradores' => 'Administradores',
        ];
    }
}

if (!function_exists('avisosStatusOptions')) {
    function avisosStatusOptions(): array
    {
        return [
            'ativo' => 'Ativo',
            'encerrado' => 'Encerrado',
        ];
    }
}
