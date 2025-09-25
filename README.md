# ViaFácil - Interface do Aplicativo

Este repositório contém os protótipos das telas principais do aplicativo **ViaFácil**, voltado para a gestão de passageiros, notificações e comunicação de avisos em tempo real.

## Visão Geral das Telas

### 🔹 Frame 1 - Tela de Boas-vindas
- Exibe o logotipo do ViaFácil.
- Botão de acesso: `LOGIN`.

### 🔹 Frame 2 - Tela de Login
- Campos para inserção de **usuário** e **senha**.
- Link de recuperação de senha: `esqueceu sua senha`.
- Botão de login: `ENTRAR`.
- Link de suporte técnico.

### 🔹 Frame 3 - Recuperação de Senha (Etapa 1)
- Solicitação de e-mail para envio do link de redefinição.
- Campo: `e-mail para receber o link`.
- Botão: `ENVIAR`.
- Link de suporte técnico.

### 🔹 Frame 4 - Recuperação de Senha (Etapa 2)
- Definição da nova senha.
- Campos: `Digite sua nova senha`, `Confirmar senha`.
- Botão: `ENTRAR`.
- Link de suporte técnico.

### 🔹 Frame 5 - Tela Principal do App
- Acesso às principais funcionalidades:
  - Notificações
  - Passageiros
  - Avisos
  - Rotas
- Campo de envio de aviso com botão `ENVIAR AVISO`.
- Seção de **Solicitações**.

##  Tecnologias e Ferramentas
- Protótipo criado em ferramenta de design (provavelmente Figma ou Adobe XD).
- Interface pensada para dispositivos móveis.

## Funcionalidades Previstas

## Atualização: Integração Inicial de Usuários e Sensores

Esta etapa inclui:

1. Tabela `usuarios` populada com 3 usuários de teste (senhas em MD5 apenas para prototipagem; recomenda-se migrar para `password_hash`).
2. Criação das tabelas de monitoramento:
  - `sensor` (cadastro de dispositivos)
  - `sensor_data` (leituras associadas)
3. Inserção de sensores de exemplo: `temperatura_freio`, `vibracao_motor` com leituras simuladas.
4. Dashboard agora exibe:
  - Listagem de usuários (nome, e-mail, tipo)
  - Seção “Monitoramento de Sensores” com placeholder ou dados agregados (última leitura / total de registros) quando as tabelas existem.

### Script SQL
Arquivo: `sa_viafacil_db.sql` contém criação e inserts das tabelas:

```sql
CREATE TABLE sensor (...);
CREATE TABLE sensor_data (...);
```

Para aplicar:
1. Executar o script no MySQL / MariaDB.
2. Confirmar credenciais em `config/db.php`.

### Próximos Passos (Sugeridos)
- Implementar visualização em tempo real (Ajax ou WebSocket) das leituras mais recentes.
- Gráfico simples (ex: Chart.js) para evolução de temperatura / vibração.
- Filtro por tipo de sensor no dashboard.
- Alertas quando valores excederem limites (ex: temperatura > 120°C).

### Segurança (Futuro)
- Migrar MD5 para `password_hash()`.
- Adicionar controle de sessão e expiração.
- Criar níveis de permissão mais granulares.

## Suporte
Há links nas telas para entrar em contato com o **Suporte Técnico** diretamente a partir da interface.

---