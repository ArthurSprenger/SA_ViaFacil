# Sistema de Notificações em Tempo Real - ViaFácil

## Estrutura Implementada

### 📊 Tabelas do Banco de Dados

Execute o arquivo `notificacoes.sql` no MySQL:

```sql
mysql -u root sa_viafacil_db < notificacoes.sql
```

Ou importe manualmente via phpMyAdmin.

### 🔧 Arquivos Criados

1. **notificacoes.sql** - Tabelas `avisos` e `notificacoes`
2. **mqtt_notificacoes_subscriber.py** - Listener Python para notificações
3. **includes/mqtt_notificacoes.php** - Função PHP para publicar notificações
4. **public/publicar_avisos.php** - Admin publica avisos
5. **public/get_avisos.php** - API REST para buscar avisos
6. **public/aviso_funcionario.php** - Funcionários veem avisos em tempo real

## 🚀 Como Usar

### 1. Executar Listeners (Terminais Separados)

**Terminal 1 - Sensores IoT:**
```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
python mqtt_subscriber.py
```

**Terminal 2 - Notificações:**
```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
python mqtt_notificacoes_subscriber.py
```

**Terminal 3 - Simulador de Sensores (opcional):**
```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
python mqtt_publisher.py
```

### 2. Acessar Interfaces Web

**Admin:**
- Dashboard: `http://localhost/2025_atividades_FelipeC/SA_ViaFacil/SA_ViaFacil/public/dashboard.php`
- Publicar Avisos: `http://localhost/2025_atividades_FelipeC/SA_ViaFacil/SA_ViaFacil/public/publicar_avisos.php`
- Sensores IoT: `http://localhost/2025_atividades_FelipeC/SA_ViaFacil/SA_ViaFacil/public/sensores.php`

**Funcionário:**
- Dashboard: `http://localhost/2025_atividades_FelipeC/SA_ViaFacil/SA_ViaFacil/public/dashboard_funcionario.php`
- Ver Avisos: `http://localhost/2025_atividades_FelipeC/SA_ViaFacil/SA_ViaFacil/public/aviso_funcionario.php`

## 📡 Fluxo de Funcionamento

### Avisos

1. **Admin** acessa `publicar_avisos.php`
2. Preenche título e mensagem
3. Clica em "Publicar Aviso"
4. PHP salva no banco e publica via MQTT no tópico `viafacil/notificacoes/aviso`
5. **Python listener** recebe a mensagem e salva na tabela `notificacoes`
6. **Funcionários** veem automaticamente na página `aviso_funcionario.php` (atualiza a cada 5 segundos)

### Sensores IoT

1. **Simulador** publica dados no tópico `viafacil/sensores/{tipo_sensor}`
2. **Python listener** recebe e salva em `sensor_data`
3. **Admin** vê em tempo real na página `sensores.php` (atualiza a cada 3 segundos)

## 🔐 Usuários de Teste

| Email | Senha | Tipo |
|-------|-------|------|
| admin@exemplo.com | admin123 | admin |
| usuario@exemplo.com | senha123 | normal |
| operador@exemplo.com | operador123 | normal |

## ⚙️ Configuração (.env)

```env
MQTT_BROKER=broker.hivemq.com
MQTT_PORT=1883
MQTT_TOPIC=viafacil/sensores/#
MQTT_TOPIC_NOTIFICACOES=viafacil/notificacoes/#
MQTT_CLIENT_ID=viafacil_subscriber

DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=sa_viafacil_db
```

## 📋 Próximos Passos

Para implementar notificações de **solicitações**, siga o mesmo padrão:

1. Criar tabela `solicitacoes` (se ainda não existe)
2. Criar `publicar_solicitacao.php` (admin/funcionário publica)
3. Adicionar tópico MQTT `viafacil/notificacoes/solicitacao`
4. Criar `get_solicitacoes.php` (API REST)
5. Atualizar páginas de solicitações com JavaScript auto-refresh

## 🐛 Troubleshooting

**Erro: "Não foi possível conectar ao broker"**
- Verifique sua conexão com a internet
- Broker HiveMQ pode estar instável, tente: `test.mosquitto.org`

**Avisos não aparecem:**
- Verifique se o listener Python está rodando
- Cheque o console do Python para erros
- Abra DevTools do navegador (F12) → Console para ver erros JavaScript

**Sensores não atualizam:**
- Execute `python mqtt_publisher.py` para simular dados
- Verifique se `mqtt_subscriber.py` está rodando
