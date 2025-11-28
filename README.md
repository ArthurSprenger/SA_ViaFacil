# ViaFácil - Sistema de Gestão Ferroviária

Sistema web completo para gestão de passageiros, avisos, solicitações e monitoramento IoT em tempo real para operações ferroviárias.

## 📋 Descrição do Projeto

O **ViaFácil** é uma plataforma web desenvolvida para modernizar a gestão de sistemas ferroviários, integrando funcionalidades administrativas, monitoramento de sensores IoT via MQTT, e comunicação em tempo real entre funcionários e administradores.

O sistema implementa controle de acesso baseado em roles (admin/funcionário), aprovação manual de usuários, gestão de avisos com priorização automática, e monitoramento de sensores ESP32 conectados ao broker HiveMQ Cloud.

## 🏗️ Arquitetura Técnica

### Stack de Tecnologias

**Backend:**
- PHP 8.x (server-side rendering)
- MySQL 8.0 / MariaDB
- MySQLi e PDO para abstração de dados
- Arquitetura MVC parcial com separação de concerns

**Frontend:**
- HTML5 semântico
- CSS3 com Grid e Flexbox
- JavaScript Vanilla (sem frameworks)
- Design responsivo mobile-first

**IoT e Comunicação:**
- MQTT Protocol (HiveMQ Cloud Broker)
- TLS/SSL (porta 8883)
- phpMQTT (Bluerhinos) como client library
- Notificações push em tempo real

### Estrutura de Diretórios

```
SA_ViaFacil/
├── config/
│   ├── db.php                 # Conexão MySQLi
│   ├── mqtt_config.php        # Configuração MQTT + mapeamento de tópicos
│   └── certs/
│       └── cacert.pem         # Certificado CA para TLS
├── includes/
│   ├── db_connect.php         # Conexão PDO
│   ├── phpMQTT.php           # Client MQTT
│   ├── mqtt_notificacoes.php # Publicação de eventos
│   ├── avisos_service.php    # Helpers de avisos
│   └── solicitacoes_service.php # Helpers de solicitações
├── public/
│   ├── dashboard.php          # Dashboard administrativo
│   ├── dashboard_funcionario.php # Dashboard funcionários
│   ├── login.php              # Autenticação
│   ├── cadastro.php           # Registro de usuários
│   ├── sensores.php           # Monitoramento IoT
│   ├── get_sensor_data.php    # API MQTT para sensores
│   ├── mqtt_worker.php        # Worker persistente MQTT
│   └── aprovar_usuarios.php   # Gerenciamento de aprovações
├── src/
│   ├── Auth.php               # Classe de autenticação
│   └── User.php               # Repositório de usuários
├── styles/
│   ├── dashboard.css
│   ├── sensores.css
│   └── login.css
├── database/
│   └── sa_viafacil_completo.sql # Schema completo
└── assets/                    # Imagens e recursos estáticos
```

## 🔐 Sistema de Autenticação e Autorização

### Fluxo de Registro e Aprovação

1. **Cadastro:** Usuário preenche formulário com dados pessoais e endereço (busca automática via CEP usando API ViaCEP)
2. **Status Inicial:** Conta criada com `status='pendente'` e `tipo='normal'`
3. **Aprovação Manual:** Administrador acessa painel de aprovações e executa uma das ações:
   - Aprovar (`status='aprovado'`) - libera acesso
   - Rejeitar (`status='rejeitado'`) - bloqueia permanentemente
4. **Login:** Apenas usuários com `status='aprovado'` conseguem autenticar

### Controle de Acesso

**Roles implementados:**
- `admin`: Acesso total ao sistema, CRUD de usuários, gestão de avisos e solicitações
- `normal`: Acesso ao dashboard de funcionários, visualização de avisos, criação de solicitações

**Proteção Anti-Lock:**
O sistema impede que o último administrador seja removido ou rebaixado através de query de validação:
```sql
SELECT COUNT(*) FROM usuarios WHERE tipo='admin' AND id<>[id_alvo]
```
Se resultado = 0, operação é bloqueada com mensagem de erro.

### Senhas

Armazenamento com `password_hash()` usando algoritmo bcrypt (custo padrão). Validação via `password_verify()`. Campo `senha` VARCHAR(255) suporta hashes futuros.

## 📢 Sistema de Avisos

### Estrutura de Dados

```sql
avisos (
  id INT PRIMARY KEY,
  titulo VARCHAR(255),
  mensagem TEXT,
  tipo ENUM('informativo','alerta','urgente'),
  destino ENUM('todos','funcionarios','passageiros'),
  status ENUM('ativo','pausado','encerrado'),
  expira_em DATETIME NULL,
  encerrado_em DATETIME NULL,
  usuario_id INT,
  solicitacao_id INT NULL,
  criado_em DATETIME,
  atualizado_em DATETIME
)
```

### Funcionalidades

**Criação Manual:**
- Admin preenche formulário com título, mensagem, tipo, destino e expiração opcional
- INSERT no banco + publicação MQTT com payload JSON completo
- Notificação broadcast para destino especificado

**Auto-geração a partir de Solicitações:**
- Ao atualizar status de solicitação, admin pode marcar checkbox para gerar aviso automaticamente
- Título e mensagem pré-preenchidos com dados da solicitação (estação, tipo, prioridade)
- Tipo do aviso mapeado da prioridade:
  - `urgente` → tipo `urgente`
  - `alta` → tipo `alerta`
  - `media`/`baixa` → tipo `informativo`
- Sistema verifica se já existe aviso vinculado via `solicitacao_id` (UPDATE se existir, INSERT se não)

**Gerenciamento:**
- Listagem ordenada (ativos primeiro via CASE, depois por data)
- Alternar status ativo/encerrado (atualiza `encerrado_em` automaticamente)
- Exclusão permanente com confirmação JavaScript
- Mensagens truncadas em 120 caracteres na visualização (mb_strimwidth)

## 📋 Sistema de Solicitações

### Modelo de Dados

```sql
solicitacoes (
  id INT PRIMARY KEY,
  usuario_id INT,
  tipo VARCHAR(60),
  estacao VARCHAR(120),
  horario DATETIME,
  descricao TEXT,
  prioridade ENUM('baixa','media','alta','urgente'),
  status ENUM('pendente','em_andamento','resolvido','cancelado'),
  criado_em DATETIME,
  atualizado_em DATETIME
)
```

### Fluxo de Atualização

1. **Admin seleciona novo status** no dropdown da solicitação
2. **Opção de publicar aviso:** Checkbox determina se gera aviso automático
3. **UPDATE de status** executado no banco
4. **Notificação MQTT individual** enviada para o usuário solicitante
5. **Se checkbox marcado:**
   - Busca dados completos da solicitação
   - Gera título: "Solicitação [Status] - [Estação]"
   - Formata mensagem com tipo, prioridade e descrição
   - Verifica aviso existente via `solicitacao_id`
   - UPDATE ou INSERT conforme necessário
   - Publica notificação MQTT broadcast + individual

### Priorização Visual

Tags coloridas via CSS classes:
- `.prioridade-urgente` - Vermelho (#dc3545)
- `.prioridade-alta` - Laranja (#fd7e14)
- `.prioridade-media` - Amarelo (#ffc107)
- `.prioridade-baixa` - Verde (#28a745)

## 🌐 Monitoramento IoT via MQTT

### Configuração do Broker

**HiveMQ Cloud:**
- Host: `ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud`
- Porta: 8883 (TLS/SSL)
- Credenciais: Pedro/PedroDSM2, felipe/FelipeDSM2, Henry/HenryDSM2

### Dispositivos ESP32 Integrados

**S1 - DHT11 + LDR:**
- Tópico: `S1 umidade` → Umidade (%)
- Tópico: `S1 temperatura` → Temperatura (°C)
- Tópico: `S1 iluminacao` → Estado (lux)

**S2 - Sensores Ultrassônicos Duplos:**
- Tópico: `Projeto S2 Distancia1` → Distância 1 (cm)
- Tópico: `Projeto S2 Distancia2` → Distância 2 (cm)

**S3 - Presença + Ultrassom:**
- Tópico: `Projeto S3 Presenca3` → Presença (bool)
- Tópico: `Projeto S3 Ultrassom3` → Distância (cm)

**Trem - Velocidade:**
- Tópico: `projeto trem velocidade` → Velocidade (km/h)

### Processamento de Mensagens

**get_sensor_data.php:**
- Conecta ao broker MQTT via TLS
- Subscreve todos os 8 tópicos configurados
- Loop de escuta por 8 segundos (otimizado para captura vs performance)
- Early exit quando captura dados de todos sensores ativos
- Conversão de mensagens de texto para valores numéricos:
  - `acender`/`apagar` → 1/0
  - `objeto_proximo`/`objeto_longe` → 5/50
  - Valores numéricos diretos aceitos para todos os sensores
- INSERT automático no banco via prepared statements
- Retorna JSON com dados recentes + metadata de conexão

**Interface de Monitoramento (sensores.php):**
- Grid responsivo com 8 cards (um por sensor)
- Atualização automática a cada 3 segundos via fetch
- Status visual: 🟢 Online (dados recebidos) / ⚠️ Aguardando dados
- Valores formatados com 2 casas decimais + unidade de medida
- Timestamp de última leitura
- Animação pulse nos cards ativos

### Worker Persistente (Opcional)

**mqtt_worker.php:**
- Mantém conexão MQTT aberta continuamente
- Loop infinito com `$mqtt->proc()` e keepalive de 30s
- Auto-reconnect com até 5 tentativas
- Log detalhado de eventos
- Execução: `php public/mqtt_worker.php` em terminal separado
- Ideal para produção com alta frequência de dados

## 👥 Gerenciamento de Usuários

### CRUD Administrativo

**Criação:**
- Formulário inline no dashboard
- Validação de email único via prepared statement
- Senha hashada com bcrypt
- Tipo selecionável (normal/admin)

**Edição:**
- JavaScript popula formulário com `data-*` attributes
- Senha opcional: vazia = mantém anterior, preenchida = novo hash
- Validação de email duplicado excluindo próprio ID
- Atualização de sessão se admin editar a si mesmo

**Exclusão:**
- Confirmação JavaScript obrigatória
- Bloqueio de auto-exclusão
- Validação anti-lock (impede excluir último admin)

### Tabela de Usuários

```sql
usuarios (
  id INT PRIMARY KEY,
  nome VARCHAR(100),
  email VARCHAR(100) UNIQUE,
  senha VARCHAR(255),
  cep VARCHAR(10),
  logradouro VARCHAR(255),
  numero VARCHAR(20),
  complemento VARCHAR(100),
  bairro VARCHAR(100),
  cidade VARCHAR(100),
  uf VARCHAR(2),
  tipo ENUM('normal','admin'),
  status ENUM('pendente','aprovado','rejeitado'),
  criado_em DATETIME,
  foto_perfil VARCHAR(255)
)
```

**Índices:**
- `idx_email` - Busca rápida no login
- `idx_tipo` - Filtros por role
- `idx_status` - Queries de aprovação

## 🔄 Sistema de Notificações em Tempo Real

### Integração MQTT

**Arquivo:** `includes/mqtt_notificacoes.php`

**Função:** `publicarNotificacao($tipo, $titulo, $mensagem, $remetente_id, $destinatario_id, $metadata)`

**Casos de Uso:**
1. **Aviso criado:** Broadcast para destino especificado (todos/funcionários/passageiros)
2. **Solicitação atualizada:** Notificação individual para o solicitante
3. **Aviso auto-gerado:** Broadcast para funcionários + individual para solicitante

**Payload JSON:**
```json
{
  "tipo": "aviso|solicitacao|alerta",
  "titulo": "string",
  "mensagem": "string",
  "remetente_id": int,
  "destinatario_id": int|null,
  "timestamp": "ISO-8601",
  "metadata": {
    "persisted": bool,
    "tipo_aviso": "string",
    "destino": "string",
    "status": "string",
    "solicitacao_id": int,
    "prioridade": "string"
  }
}
```

### Flash Messages

**Padrão Post-Redirect-Get (PRG):**
- Função `flash($key, $html)` armazena em `$_SESSION`
- Loop no início do request recupera e limpa sessão
- Exibição única após ação (evita resubmissão de formulários)

**Tipos de flash:**
- `flash_user_add` - Criação de usuários
- `flash_user_edit` - Edição de usuários
- `flash_solicitacao` - Operações de solicitações
- `flash_aviso` - Operações de avisos

## 🗄️ Estrutura do Banco de Dados

### Schema Principal

**Arquivo:** `database/sa_viafacil_completo.sql`

**Tabelas Implementadas:**
1. `usuarios` - Cadastro e autenticação
2. `solicitacoes` - Requisições de funcionários
3. `sensor` - Cadastro de dispositivos IoT
4. `sensor_data` - Leituras de sensores
5. `avisos` - Sistema de comunicação broadcast
6. `notificacoes` - Histórico de notificações (futuro)

**Integridade Referencial:**
- Foreign keys com `ON DELETE CASCADE`
- Índices em campos de busca frequente
- DATETIME com `DEFAULT CURRENT_TIMESTAMP`
- Charset `utf8mb4` para suporte Unicode completo

### Queries de Exemplo

**Avisos ativos ordenados:**
```sql
SELECT a.*, u.nome AS autor
FROM avisos a
INNER JOIN usuarios u ON a.usuario_id = u.id
WHERE a.status='ativo'
ORDER BY a.criado_em DESC
```

**Solicitações com nome do solicitante:**
```sql
SELECT s.*, u.nome as usuario_nome
FROM solicitacoes s
INNER JOIN usuarios u ON s.usuario_id = u.id
ORDER BY s.criado_em DESC
LIMIT 50
```

**Última leitura de cada sensor:**
```sql
SELECT sd.*, s.tipo
FROM sensor_data sd
INNER JOIN sensor s ON sd.id_sensor = s.id
WHERE sd.id IN (
  SELECT MAX(id) FROM sensor_data GROUP BY id_sensor
)
```

## 🚀 Instalação e Configuração

### Pré-requisitos

- PHP 8.0 ou superior
- MySQL 8.0 / MariaDB 10.5+
- XAMPP ou servidor Apache/Nginx
- Extensões PHP: mysqli, pdo_mysql, openssl, mbstring

### Passos de Instalação

1. **Clone o repositório:**
```bash
git clone https://github.com/ArthurSprenger/SA_ViaFacil.git
cd SA_ViaFacil
```

2. **Configure o banco de dados:**
```bash
mysql -u root -p < database/sa_viafacil_completo.sql
```

3. **Ajuste credenciais em `config/db.php`:**
```php
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', ''); // Senha vazia para XAMPP
define('DB_NAME', 'sa_viafacil_db');
```

4. **Verifique configuração MQTT em `config/mqtt_config.php`:**
```php
define('MQTT_SERVER', 'ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud');
define('MQTT_PORT', 8883);
define('MQTT_USERNAME', 'Pedro');
define('MQTT_PASSWORD', 'PedroDSM2');
```

5. **Inicie o servidor:**
```bash
# XAMPP: Inicie Apache e MySQL via painel de controle
# Acesse: http://localhost/sa_certa/SA_ViaFacil/public/
```

6. **(Opcional) Inicie worker MQTT:**
```bash
php public/mqtt_worker.php
```

### Credenciais de Teste

**Administrador:**
- Email: `admin@exemplo.com`
- Senha: `admin123`

**Usuário Normal:**
- Email: `usuario@exemplo.com`
- Senha: `senha123`

## 📊 Fluxos Principais do Sistema

### Fluxo de Autenticação
```
Login → Validação email/senha → Verifica status (pendente/aprovado/rejeitado)
  ↓
Status aprovado → Verifica tipo (admin/normal)
  ↓
Admin → dashboard.php | Normal → dashboard_funcionario.php
```

### Fluxo de Aviso Automático
```
Admin atualiza solicitação + marca "Publicar aviso"
  ↓
Busca dados completos da solicitação (JOIN com usuarios)
  ↓
Mapeia prioridade → tipo de aviso
  ↓
Verifica aviso existente via solicitacao_id
  ↓
UPDATE (se existe) | INSERT (se não existe)
  ↓
Publica MQTT broadcast + notificação individual
```

### Fluxo IoT/MQTT
```
Dispositivo ESP32 publica mensagem em tópico
  ↓
get_sensor_data.php conecta broker via TLS
  ↓
Subscreve tópicos + aguarda 8 segundos
  ↓
Callback processa mensagem (converte texto→número)
  ↓
Busca sensor_id via tipo no banco
  ↓
INSERT em sensor_data via prepared statement
  ↓
Frontend atualiza via fetch a cada 3 segundos
```

## 🔧 Manutenção e Suporte

### Logs e Debug

**Ativar debug MQTT:**
```php
$mqtt->debug = true; // em get_sensor_data.php
```

**Verificar logs de erro PHP:**
```bash
tail -f /xampp/apache/logs/error.log
```

### Troubleshooting Comum

**Problema:** Sensores não aparecem dados
- Verificar ESP32 conectado ao WiFi
- Confirmar tópicos exatos no código ESP32
- Testar conexão manual via HiveMQ Web Client
- Aumentar timeout em get_sensor_data.php se necessário

**Problema:** Usuário não consegue fazer login
- Verificar status na tabela usuarios (deve ser 'aprovado')
- Confirmar senha hashada corretamente
- Checar sessão PHP ativa

**Problema:** Avisos não aparecem
- Verificar status='ativo' na tabela avisos
- Confirmar destino corresponde ao tipo de usuário
- Checar se não expirou (expira_em)

## 📝 Licença e Créditos

Projeto desenvolvido para gestão de sistemas ferroviários com foco em IoT e comunicação em tempo real.

**Tecnologias de terceiros:**
- phpMQTT (Bluerhinos) - Cliente MQTT
- HiveMQ Cloud - Broker MQTT gerenciado

---

**Versão:** 1.0.0  
**Última atualização:** Novembro 2025
