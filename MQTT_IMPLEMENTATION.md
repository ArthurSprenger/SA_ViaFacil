# Integração MQTT - Resumo da Implementação

## Arquivos Criados/Modificados

### ✅ Novos Arquivos Criados

1. **config/mqtt_config.php**
   - Centralizações das configurações MQTT
   - Define servidor, porta, credenciais, tópicos

2. **public/get_mqtt_data.php**
   - Endpoint para receber dados MQTT em tempo real
   - Salva automaticamente no banco de dados

3. **styles/sensores.css**
   - Estilos específicos para página de sensores
   - Cards coloridos e responsivos
   - Animações de carregamento

4. **MQTT_CONFIG.md**
   - Documentação completa da configuração
   - Exemplos de código Arduino/ESP32
   - Troubleshooting

5. **public/test_mqtt.php**
   - Script de teste de conexão MQTT
   - Executa via terminal PHP

### 🔧 Arquivos Modificados

1. **public/sensores.php**
   - Adicionado link para sensores.css
   - Melhorado JavaScript de atualização
   - Tratamento de erros aprimorado

2. **public/get_sensor_data.php**
   - Migrado para usar mqtt_config.php
   - Configurações centralizadas
   - Mantém funcionalidade de leitura MQTT + banco

## Estrutura de Diretórios

```
SA_ViaFacil/
├── config/
│   ├── mqtt_config.php          ← NOVO
│   ├── db.php
│   └── certs/
│       └── cacert.pem           ← JÁ EXISTE
├── includes/
│   └── phpMQTT.php              ← JÁ EXISTE
├── public/
│   ├── sensores.php             ← MODIFICADO
│   ├── get_sensor_data.php      ← MODIFICADO
│   ├── get_mqtt_data.php        ← NOVO
│   └── test_mqtt.php            ← NOVO
├── styles/
│   ├── dashboard.css
│   └── sensores.css             ← NOVO
├── MQTT_CONFIG.md               ← NOVO
└── MQTT_SETUP.md                ← JÁ EXISTE
```

## Como Funciona

### 1. Configuração Centralizada
Todas as configurações MQTT agora estão em um único lugar:
```php
config/mqtt_config.php
```

Para alterar servidor, credenciais ou tópicos, edite apenas este arquivo.

### 2. Fluxo de Dados

```
Dispositivo IoT (ESP32/Arduino)
        ↓
    MQTT Broker (HiveMQ Cloud)
        ↓
get_sensor_data.php (subscreve e salva no banco)
        ↓
    Banco de Dados MySQL
        ↓
sensores.php (exibe dados via JavaScript)
```

### 3. Página de Sensores

A página `sensores.php` agora possui:
- ✅ Design moderno com CSS dedicado
- ✅ Cards coloridos por tipo de sensor
- ✅ Atualização automática a cada 3 segundos
- ✅ Animação de carregamento
- ✅ Responsivo para mobile

## Como Usar

### 1. Configurar Credenciais

Edite `config/mqtt_config.php` e configure suas credenciais do HiveMQ:

```php
define('MQTT_USERNAME', 'seu_usuario');
define('MQTT_PASSWORD', 'sua_senha');
```

### 2. Testar Conexão

Execute no terminal:
```bash
cd c:\xampp\htdocs\sa_certa\SA_ViaFacil
php public/test_mqtt.php
```

### 3. Configurar Dispositivo IoT

Use o código exemplo em `MQTT_CONFIG.md` para configurar seu ESP32/Arduino.

### 4. Acessar Interface Web

Navegue para:
```
http://localhost/sa_certa/SA_ViaFacil/public/sensores.php
```

## Formato de Mensagens MQTT

### Opção 1: Tópicos Individuais
```
Tópico: S1 temperatura
Mensagem: 25.5 °C
```

### Opção 2: Tópico Único com JSON
```
Tópico: viafacil/sensores
Mensagem: {"tipo":"temperatura","valor":25.5,"unidade":"°C"}
```

## Sensores no Banco de Dados

Certifique-se de que os sensores estejam cadastrados:

```sql
SELECT * FROM sensor;
```

Resultado esperado:
- ID 1: umidade
- ID 2: temperatura  
- ID 3: iluminacao

## Próximos Passos

1. ✅ Configurar credenciais corretas do HiveMQ
2. ✅ Testar conexão com test_mqtt.php
3. ✅ Programar dispositivos IoT
4. ✅ Verificar dados na interface web

## Troubleshooting

### Erro de conexão
- Verifique credenciais em `mqtt_config.php`
- Confirme que `cacert.pem` existe
- Execute `test_mqtt.php` para diagnóstico

### Dados não aparecem
- Verifique se sensores estão como 'ativo' no banco
- Confirme formato das mensagens MQTT
- Verifique logs do navegador (F12 → Console)

### Performance lenta
- O sistema escuta MQTT por 2 segundos
- Para alta frequência, considere worker persistente
- Cache valores recentes no Redis/Memcached

## Arquivos Importantes

- 📝 `MQTT_CONFIG.md` - Documentação completa
- ⚙️ `config/mqtt_config.php` - Configurações
- 🧪 `public/test_mqtt.php` - Script de teste
- 🎨 `styles/sensores.css` - Estilos da página
- 🔌 `includes/phpMQTT.php` - Biblioteca cliente

## Contato

Para dúvidas, consulte `MQTT_CONFIG.md` ou verifique os comentários nos arquivos PHP.
