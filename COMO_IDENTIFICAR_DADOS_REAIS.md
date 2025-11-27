# Como Identificar se os Dados são Reais ou Fictícios

## 🔍 Verificações Rápidas

### 1. Via Interface Web

Acesse a página de sensores:
```
http://localhost/sa_certa/SA_ViaFacil/public/sensores.php
```

**Indicadores visuais:**
- 🟢 **Verde "MQTT Conectado"** = Dados em tempo real
- 🔴 **Vermelho "MQTT Desconectado"** = Dados fictícios do banco

### 2. Via Diagnóstico MQTT

Acesse:
```
http://localhost/sa_certa/SA_ViaFacil/public/diagnostico_mqtt.php
```

Clique em **"Testar Conexão Agora"** para verificar:
- ✓ Conexão com broker estabelecida
- ✓ Tópicos inscritos
- ✓ Mensagens recebidas dos dispositivos

### 3. Via Console do Navegador

1. Abra a página de sensores
2. Pressione **F12** (DevTools)
3. Vá na aba **Console**
4. Procure por `Resposta MQTT:`
5. Verifique o campo `mqtt_conectado`:
   - `true` = Dados reais
   - `false` = Dados fictícios

### 4. Via API Direta

Acesse diretamente:
```
http://localhost/sa_certa/SA_ViaFacil/public/get_sensor_data.php
```

Veja o JSON retornado:
```json
{
  "mqtt_conectado": true,  ← Verifica aqui
  "mqtt_server": "ef339175de264ab783f4bea1e2a1abe9.s1.eu.hivemq.cloud",
  "mqtt_user": "Pedro",
  "total_sensores": 3,
  "dados": [...]
}
```

## 🚨 Dados Fictícios - Como Identificar

### Sinais de dados fictícios:

1. **Status MQTT Desconectado**
   - Indicador vermelho na interface
   - `mqtt_conectado: false` no JSON

2. **Timestamps Antigos**
   - Data/hora das leituras são antigas
   - Exemplo: "27/11/2025 18:36:35" (não atualiza)

3. **Valores Sempre Iguais**
   - Temperatura sempre 3.60 mm/s
   - Umidade sempre 87.10 °C
   - Valores não mudam após atualização

4. **Mensagem no Console**
   ```
   🔴 MQTT Desconectado - Exibindo dados do banco
   ```

## ✅ Dados Reais - Como Identificar

### Sinais de dados reais:

1. **Status MQTT Conectado**
   - Indicador verde na interface
   - `mqtt_conectado: true` no JSON

2. **Timestamps Atuais**
   - Data/hora atualiza constantemente
   - Exemplo: agora → "27/11/2025 20:15:42"

3. **Valores Variáveis**
   - Temperatura varia (ex: 25.1, 25.3, 25.2)
   - Umidade muda (ex: 84.5, 85.1, 84.8)
   - Iluminação alterna entre "acender"/"apagar"

4. **Diagnóstico Mostra Mensagens**
   ```
   Mensagens recebidas: 5
   ✓ Dispositivos IoT estão publicando!
   ```

## 🔧 Troubleshooting - Dados Fictícios

### Se os dados são fictícios, verifique:

#### 1. Broker MQTT Acessível?
```bash
# Via terminal
php public/test_mqtt.php
```

**Esperado:** "✓ Conectado ao broker MQTT com sucesso!"

#### 2. Credenciais Corretas?

Verifique em `config/mqtt_config.php`:
```php
define('MQTT_USERNAME', 'Pedro');  // ou 'felipe', 'Henry'
define('MQTT_PASSWORD', 'PedroDSM2');  // senha correta?
```

#### 3. Certificado CA Existe?

Verifique:
```
c:\xampp\htdocs\sa_certa\SA_ViaFacil\config\certs\cacert.pem
```

**Deve existir e ter ~200KB**

#### 4. Dispositivos IoT Conectados?

- ESP32/Arduino estão ligados?
- WiFi conectado? (FIESC_IOT_EDU)
- Código carregado corretamente?
- LED de status piscando?

#### 5. Tópicos MQTT Corretos?

Compare os tópicos no código Arduino com `mqtt_config.php`:
- ✓ `S1 umidade` (com espaço)
- ✗ `S1umidade` (sem espaço) ← ERRADO

#### 6. Firewall/Antivírus?

Porta **8883** deve estar liberada para conexões TLS

## 📊 Exemplo de Resposta Real vs Fictícia

### Dados Fictícios (do banco):
```json
{
  "mqtt_conectado": false,
  "dados": [
    {
      "id_sensor": "1",
      "valor": "87.10",
      "unidade": "°C",
      "data_hora": "27/11/2025 18:36:35"  ← Não muda
    }
  ]
}
```

### Dados Reais (MQTT):
```json
{
  "mqtt_conectado": true,
  "dados": [
    {
      "id_sensor": "1",
      "valor": "25.30",
      "unidade": "°C",
      "data_hora": "27/11/2025 20:45:12"  ← Atualiza sempre
    }
  ]
}
```

## 🎯 Checklist Rápido

- [ ] Página mostra indicador 🟢 verde?
- [ ] Console mostra `mqtt_conectado: true`?
- [ ] Timestamps atualizando em tempo real?
- [ ] Valores mudando a cada atualização?
- [ ] Diagnóstico mostra "mensagens recebidas > 0"?
- [ ] Dispositivos IoT ligados e conectados?

**Se TODAS marcadas:** Dados são REAIS ✅  
**Se ALGUMA desmarcada:** Dados são FICTÍCIOS ❌

## 💡 Dica Rápida

A forma mais rápida de verificar:

1. Abra `sensores.php`
2. Olhe o texto abaixo do título:
   - 🟢 Verde = Real
   - 🔴 Vermelho = Fictício

**Simples assim!**

## 🔗 Links Úteis

- Interface: `/public/sensores.php`
- Diagnóstico: `/public/diagnostico_mqtt.php`
- API: `/public/get_sensor_data.php`
- Teste CLI: `php public/test_mqtt.php`
