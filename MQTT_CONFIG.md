# Configuração MQTT - ViaFácil

## Configuração do Broker

O sistema utiliza o HiveMQ Cloud como broker MQTT. As configurações estão em `config/mqtt_config.php`:

```php
MQTT_SERVER: ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud
MQTT_PORT: 8883 (TLS/SSL)
MQTT_TOPIC: viafacil/sensores
```

## Credenciais

Você precisa configurar as credenciais no arquivo `config/mqtt_config.php`:
- `MQTT_USERNAME`: Nome de usuário do HiveMQ Cloud
- `MQTT_PASSWORD`: Senha do HiveMQ Cloud

## Formato das Mensagens

### Envio de Dados dos Sensores

Os dispositivos IoT devem publicar no tópico específico de cada sensor:

**Formato do Tópico:** `S{ID} {tipo}`

Exemplos:
- `S1 temperatura`
- `S2 umidade`
- `S3 iluminacao`

**Formato da Mensagem:** `{valor} {unidade}`

Exemplos:
- `25.5 °C`
- `84.5 %`
- `350 lux`

### JSON (Alternativo)

Você também pode enviar dados em formato JSON para o tópico `viafacil/sensores`:

```json
{
  "tipo": "temperatura",
  "valor": 25.5,
  "unidade": "°C"
}
```

## Código Arduino/ESP32 (Exemplo)

```cpp
#include <WiFi.h>
#include <PubSubClient.h>

const char* ssid = "SEU_WIFI";
const char* password = "SUA_SENHA_WIFI";
const char* mqtt_server = "ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud";
const int mqtt_port = 8883;
const char* mqtt_user = "viafacil_user";
const char* mqtt_password = "ViaFacil@2025";

WiFiClientSecure espClient;
PubSubClient client(espClient);

void setup() {
  Serial.begin(115200);
  
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(500);
    Serial.print(".");
  }
  
  espClient.setInsecure(); // Para simplificar, em produção use certificado
  client.setServer(mqtt_server, mqtt_port);
  
  reconnect();
}

void reconnect() {
  while (!client.connected()) {
    Serial.print("Conectando ao MQTT...");
    String clientId = "ESP32-" + String(random(0xffff), HEX);
    
    if (client.connect(clientId.c_str(), mqtt_user, mqtt_password)) {
      Serial.println("conectado!");
    } else {
      Serial.print("falhou, rc=");
      Serial.print(client.state());
      delay(5000);
    }
  }
}

void loop() {
  if (!client.connected()) {
    reconnect();
  }
  client.loop();
  
  // Exemplo: enviar temperatura
  float temp = 25.5; // Leia do sensor real
  String payload = String(temp, 1) + " °C";
  client.publish("S1 temperatura", payload.c_str());
  
  delay(3000); // Enviar a cada 3 segundos
}
```

## Arquivos do Sistema

1. **config/mqtt_config.php** - Configurações do broker
2. **includes/phpMQTT.php** - Biblioteca cliente MQTT
3. **config/certs/cacert.pem** - Certificados CA para conexão TLS
4. **public/get_sensor_data.php** - API que recebe dados MQTT e consulta banco
5. **public/sensores.php** - Interface web de monitoramento

## Como Funciona

1. Os dispositivos IoT publicam dados nos tópicos MQTT
2. O PHP (`get_sensor_data.php`) se conecta ao broker, subscreve aos tópicos
3. Ao receber mensagens, salva os dados no banco de dados (tabela `sensor_data`)
4. A interface web consulta o endpoint a cada 3 segundos
5. Os dados mais recentes são exibidos em tempo real

## Tabelas do Banco de Dados

### sensor
```sql
id, tipo, descricao, status
```

### sensor_data
```sql
id, id_sensor, valor, unidade, data_hora
```

## Troubleshooting

### Conexão falha
- Verifique se as credenciais em `mqtt_config.php` estão corretas
- Confirme que o arquivo `cacert.pem` existe em `config/certs/`
- Teste a conexão diretamente no HiveMQ Cloud Console

### Dados não aparecem
- Verifique se os sensores estão marcados como 'ativo' no banco
- Confirme que os tópicos MQTT correspondem ao formato esperado
- Verifique os logs de erro do PHP

### Performance
- O sistema escuta o broker por 2 segundos em cada requisição
- Para sistemas com muitos sensores, considere implementar um worker persistente
- Cache os últimos valores para reduzir consultas ao banco

## Segurança

- As credenciais devem ser mantidas seguras
- Em produção, use variáveis de ambiente ao invés de hardcoded
- Mantenha o `cacert.pem` atualizado
- Use conexões TLS/SSL (porta 8883)
