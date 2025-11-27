# Mapeamento de Tópicos MQTT - ViaFácil IoT

## 📡 Configuração dos Dispositivos

### Broker MQTT
- **Host:** `ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud`
- **Porta:** `8883` (TLS/SSL)
- **Credenciais disponíveis:**
  - Pedro / PedroDSM2
  - felipe / FelipeDSM2
  - Henry / HenryDSM2

## 🔌 Mapeamento de Tópicos e Sensores

### S1 - DHT11 + LDR (Pedro)
| Tópico MQTT | Tipo Sensor | ID Banco | Formato Mensagem | Unidade |
|-------------|-------------|----------|------------------|---------|
| `S1 umidade` | umidade | 1 | Valor numérico | % |
| `S1 temperatura` | temperatura | 2 | Valor numérico | °C |
| `S1 iluminacao` | iluminacao | 3 | "acender" / "apagar" | estado |

**Exemplos de mensagens:**
```
S1 umidade → "84.5"
S1 temperatura → "25.3"
S1 iluminacao → "acender" ou "apagar"
```

### S2 - Sensores Ultrassônicos (Felipe)
| Tópico MQTT | Tipo Sensor | ID Banco | Formato Mensagem | Unidade |
|-------------|-------------|----------|------------------|---------|
| `Projeto S2 Distancia1` | distancia1 | 4 | "objeto_proximo" / "objeto_longe" / valor | cm |
| `Projeto S2 Distancia2` | distancia2 | 5 | "objeto_proximo" / "objeto_longe" / valor | cm |

**Exemplos de mensagens:**
```
Projeto S2 Distancia1 → "objeto_proximo" (converte para 5cm)
Projeto S2 Distancia1 → "objeto_longe" (converte para 50cm)
Projeto S2 Distancia1 → "15" (valor direto em cm)
```

### S3 - Ultrassom + Presença (Henry)
| Tópico MQTT | Tipo Sensor | ID Banco | Formato Mensagem | Unidade |
|-------------|-------------|----------|------------------|---------|
| `Projeto S3 Presenca3` | presenca | 6 | "0" ou "1" | bool |
| `Projeto S3 Ultrassom3` | ultrassom | 7 | "objeto_proximo" / "objeto_longe" / valor | cm |

**Exemplos de mensagens:**
```
Projeto S3 Presenca3 → "1" (presença detectada)
Projeto S3 Presenca3 → "0" (sem presença)
Projeto S3 Ultrassom3 → "objeto_proximo"
```

### Trem - Velocidade (Henry)
| Tópico MQTT | Tipo Sensor | ID Banco | Formato Mensagem | Unidade |
|-------------|-------------|----------|------------------|---------|
| `projeto trem velocidade` | velocidade | 8 | Valor numérico (positivo/negativo) | km/h |

**Exemplos de mensagens:**
```
projeto trem velocidade → "50" (50 km/h para frente)
projeto trem velocidade → "-30" (-30 km/h em ré)
projeto trem velocidade → "0" (parado)
```

## 🔄 Fluxo de Dados

```
Dispositivo IoT (ESP32)
    ↓
    Publica no tópico MQTT
    ↓
Broker HiveMQ Cloud
    ↓
PHP subscreve aos tópicos (get_sensor_data.php)
    ↓
Processa mensagem e converte formato
    ↓
Salva no banco de dados (sensor_data)
    ↓
Interface web consulta dados (sensores.php)
    ↓
Exibe em tempo real
```

## 📊 Processamento de Mensagens

### Conversões Automáticas

1. **Iluminação (S1)**
   - "acender" → valor = 1, unidade = "estado"
   - "apagar" → valor = 0, unidade = "estado"

2. **Distâncias (S2 e S3)**
   - "objeto_proximo" → valor = 5 cm
   - "objeto_longe" → valor = 50 cm
   - Valores numéricos → mantém o valor

3. **Presença (S3)**
   - "0" → 0 (sem presença)
   - "1" → 1 (com presença)

4. **Temperatura e Umidade (S1)**
   - Valores numéricos diretos
   - Exemplo: "25.5" → 25.5

5. **Velocidade (Trem)**
   - Aceita positivos e negativos
   - Exemplo: "50", "-30", "0"

## 🗄️ Estrutura do Banco de Dados

### Tabela: sensor
```sql
id | tipo        | descricao                    | status
---|-------------|------------------------------|--------
1  | umidade     | Sensor de umidade - S1       | ativo
2  | temperatura | Sensor de temperatura - S1   | ativo
3  | iluminacao  | Sensor de iluminação - S1    | ativo
4  | distancia1  | Sensor ultrassônico 1 - S2   | ativo
5  | distancia2  | Sensor ultrassônico 2 - S2   | ativo
6  | presenca    | Sensor de presença - S3      | ativo
7  | ultrassom   | Sensor ultrassônico - S3     | ativo
8  | velocidade  | Sensor de velocidade - Trem  | ativo
```

### Tabela: sensor_data
```sql
id | id_sensor | valor | unidade | data_hora
---|-----------|-------|---------|------------------
1  | 1         | 84.5  | %       | 2025-11-27 10:30
2  | 2         | 25.3  | °C      | 2025-11-27 10:30
3  | 3         | 1     | estado  | 2025-11-27 10:30
...
```

## 🔧 Arquivos de Configuração

### config/mqtt_config.php
Define todos os tópicos e mapeamentos:
```php
define('MQTT_TOPICS', [
    'S1 umidade' => ['sensor_id' => 1, 'tipo' => 'umidade', 'unidade' => '%'],
    'S1 temperatura' => ['sensor_id' => 2, 'tipo' => 'temperatura', 'unidade' => '°C'],
    // ... mais tópicos
]);
```

### public/get_sensor_data.php
- Conecta ao broker MQTT
- Subscreve a todos os tópicos
- Processa mensagens recebidas
- Converte formatos
- Salva no banco de dados
- Retorna dados em JSON

### public/sensores.php
- Interface web
- Atualiza a cada 3 segundos
- Exibe cards coloridos por sensor
- Design responsivo

## 🧪 Testando

### 1. Via Terminal (Teste de Conexão)
```bash
cd c:\xampp\htdocs\sa_certa\SA_ViaFacil
php public/test_mqtt.php
```

### 2. Via Navegador
```
http://localhost/sa_certa/SA_ViaFacil/public/sensores.php
```

### 3. Teste Direto da API
```
http://localhost/sa_certa/SA_ViaFacil/public/get_sensor_data.php
```

## 📝 Observações Importantes

1. **Tempo de Escuta:** O PHP escuta o broker por 2 segundos a cada requisição
2. **Frequência de Atualização:** A página atualiza a cada 3 segundos
3. **Credenciais:** Use qualquer das 3 credenciais disponíveis (Pedro, Felipe ou Henry)
4. **Certificado:** O arquivo `cacert.pem` é necessário para conexão TLS
5. **Sensores Ativos:** Apenas sensores com status='ativo' são processados

## 🐛 Troubleshooting

### Dados não aparecem
✅ Verifique se os sensores estão como 'ativo' no banco
✅ Confirme que os dispositivos estão publicando nos tópicos corretos
✅ Execute test_mqtt.php para verificar conexão
✅ Verifique logs do navegador (F12 → Console)

### Erro de conexão MQTT
✅ Confirme credenciais em mqtt_config.php
✅ Verifique se cacert.pem existe
✅ Teste conexão com HiveMQ Cloud Console

### Valores incorretos
✅ Verifique formato das mensagens enviadas
✅ Confirme mapeamento de tópicos em mqtt_config.php
✅ Verifique processamento em get_sensor_data.php

## 🚀 Próximos Passos

1. ✅ Importar SQL atualizado com novos sensores
2. ✅ Configurar credenciais corretas
3. ✅ Programar/conectar dispositivos IoT
4. ✅ Acessar interface web e monitorar

## 📞 Resumo Rápido

- **3 Dispositivos S1, S2, S3** + **1 Trem** = **8 sensores no total**
- **Tópicos únicos** para cada sensor (não usa padrão S1/S2/S3 genérico)
- **Formatos variados:** números, "acender/apagar", "objeto_proximo/longe"
- **Conversão automática** de mensagens textuais para valores numéricos
- **Banco de dados atualizado** com todos os 8 sensores
- **Interface responsiva** com atualização em tempo real
