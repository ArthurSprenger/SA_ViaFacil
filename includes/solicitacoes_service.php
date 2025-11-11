<?php
if (!function_exists('ensureSolicitacoesSchema')) {
    function ensureSolicitacoesSchema(mysqli $conn): void
    {
        $columns = [];
        if ($result = $conn->query('SHOW COLUMNS FROM solicitacoes')) {
            while ($row = $result->fetch_assoc()) {
                $columns[$row['Field']] = $row;
            }
            $result->close();
        }

        if (!isset($columns['tipo'])) {
            $conn->query("ALTER TABLE solicitacoes ADD COLUMN tipo VARCHAR(60) NOT NULL DEFAULT 'geral' AFTER usuario_id");
        }

        $descricaoRecemCriada = false;
        if (!isset($columns['descricao'])) {
            $conn->query('ALTER TABLE solicitacoes ADD COLUMN descricao TEXT NULL AFTER horario');
            $descricaoRecemCriada = true;
        }

        if ($descricaoRecemCriada && isset($columns['situacao'])) {
            $conn->query("UPDATE solicitacoes SET descricao = situacao WHERE (descricao IS NULL OR descricao='') AND (situacao IS NOT NULL AND situacao<>'')");
        }

        if (isset($columns['horario']) && stripos($columns['horario']['Type'], 'datetime') === false) {
            $conn->query('ALTER TABLE solicitacoes MODIFY horario DATETIME NULL');
        }

        if (isset($columns['situacao'])) {
            $conn->query("ALTER TABLE solicitacoes CHANGE situacao status VARCHAR(20) NOT NULL DEFAULT 'pendente'");
            unset($columns['situacao']);
            $columns['status'] = true;
        }

        if (!isset($columns['prioridade'])) {
            $conn->query("ALTER TABLE solicitacoes ADD COLUMN prioridade VARCHAR(20) NOT NULL DEFAULT 'media' AFTER descricao");
        }

        if (!isset($columns['status'])) {
            $conn->query("ALTER TABLE solicitacoes ADD COLUMN status VARCHAR(20) NOT NULL DEFAULT 'pendente' AFTER prioridade");
        } else {
            $conn->query("ALTER TABLE solicitacoes MODIFY status VARCHAR(20) NOT NULL DEFAULT 'pendente'");
        }

        if (!isset($columns['atualizado_em'])) {
            $conn->query('ALTER TABLE solicitacoes ADD COLUMN atualizado_em DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP AFTER criado_em');
        }

        $conn->query("UPDATE solicitacoes SET status='pendente' WHERE status = '' OR status IS NULL");
        $conn->query("UPDATE solicitacoes SET status='pendente' WHERE status IN ('Pendente','pendente')");
        $conn->query("UPDATE solicitacoes SET status='em_andamento' WHERE status IN ('Em Andamento','em andamento','em-andamento','andamento')");
        $conn->query("UPDATE solicitacoes SET status='resolvido' WHERE status IN ('Resolvido','resolvido','concluida','Concluida','Concluída','concluída')");
        $conn->query("UPDATE solicitacoes SET status='cancelado' WHERE status IN ('Cancelado','cancelado')");
        $conn->query("UPDATE solicitacoes SET prioridade=LOWER(prioridade) WHERE prioridade IS NOT NULL");
        $conn->query("UPDATE solicitacoes SET prioridade='media' WHERE prioridade = '' OR prioridade IS NULL");
    }

    function solicitacaoStatusOptions(): array
    {
        return [
            'pendente' => 'Pendente',
            'em_andamento' => 'Em andamento',
            'resolvido' => 'Resolvido',
            'cancelado' => 'Cancelado',
        ];
    }

    function solicitacaoPrioridadeOptions(): array
    {
        return [
            'baixa' => 'Baixa',
            'media' => 'Média',
            'alta' => 'Alta',
            'urgente' => 'Urgente',
        ];
    }
}
