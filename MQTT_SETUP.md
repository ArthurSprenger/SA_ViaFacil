# Configuração MQTT - ViaFácil

## Arquivos Implementados

### 1. `public/get_mqtt_message.php`
Endpoint PHP que se conecta ao broker MQTT e retorna a última mensagem de um tópico específico.

**Uso:**
```
http://localhost/SA_ViaFacil/public/get_mqtt_message.php?topic=S1 temperatura
```

**Parâmetros:**
- `topic` (obrigatório): Nome do tópico MQTT a ser monitorado

**Retorno:** Texto puro com o valor da última mensagem recebida

### 2. `public/monitor_mqtt.php`
Interface web para visualização em tempo real dos dados dos sensores MQTT.

**Acesso:**
```
http://localhost/SA_ViaFacil/public/monitor_mqtt.php
```

**Recursos:**
- Atualização automática a cada 3 segundos
- Exibe 4 sensores: Temperatura, Umidade, Iluminação e Velocidade do Trem
- Indicador de status online/offline
- Timestamp da última atualização

### 3. `config/certs/cacert.pem`
Certificado CA bundle para conexão TLS com o broker HiveMQ Cloud.

## Configuração do Broker

**Broker:** `7aecec580ecf4e5cbac2d52b35eb85b9.s1.eu.hivemq.cloud`
**Porta:** `8883` (MQTT over TLS)
**Usuário:** `Henry`
**Senha:** `HenryDSM2`

## Tópicos MQTT Disponíveis

1. `S1 temperatura` - Dados de temperatura do sensor S1
2. `S1 umidade` - Dados de umidade do sensor S1
3. `S1 iluminacao` - Dados de iluminação do sensor S1
4. `Projeto S2 Distancia1` - Distância 1 do sensor S2
5. `Projeto S2 Distancia2` - Distância 2 do sensor S2
6. `Projeto S3 Presenca3` - Sensor de presença S3
7. `Projeto S3 Ultrassom3` - Sensor ultrassônico S3
8. `projeto trem velocidade` - Velocidade do trem

## Como Funciona

1. **get_mqtt_message.php** cria uma conexão MQTT com TLS ao broker HiveMQ Cloud
2. Subscreve no tópico especificado via query parameter
3. Aguarda 2 segundos por mensagens
4. Retorna a última mensagem recebida
5. **monitor_mqtt.php** faz polling a cada 3 segundos chamando get_mqtt_message.php para cada sensor

## Requisitos

- PHP 7.4+
- Extensões PHP: `sockets`, `openssl`
- Biblioteca `phpMQTT.php` (já incluída em `/includes/`)
- Certificado CA `cacert.pem` (já incluído em `/config/certs/`)

## Testando a Conexão

```bash
# Via navegador
http://localhost/SA_ViaFacil/public/get_mqtt_message.php?topic=S1 temperatura

# Via curl (linha de comando)
curl "http://localhost/SA_ViaFacil/public/get_mqtt_message.php?topic=S1%20temperatura"
```

## Estrutura de Arquivos

```
SA_ViaFacil/
├── config/
│   └── certs/
│       └── cacert.pem          # Certificado CA
├── includes/
│   └── phpMQTT.php             # Biblioteca MQTT
└── public/
    ├── get_mqtt_message.php    # Endpoint de dados
    └── monitor_mqtt.php        # Dashboard visual
```

## Notas Importantes

- A conexão aguarda apenas 2 segundos por mensagens. Se o dispositivo não publicar nesse intervalo, nenhuma mensagem será retornada.
- O dashboard atualiza a cada 3 segundos, então há um delay total de ~5 segundos entre a publicação e a exibição.
- Para produção, considere implementar um subscriber persistente que grave os dados no banco de dados em tempo real.

## Próximos Passos

Para implementação em produção:

1. **Subscriber Persistente**: Criar um processo PHP que roda continuamente salvando dados no banco
2. **WebSockets**: Implementar comunicação em tempo real para reduzir latência
3. **Cache**: Usar Redis/Memcached para armazenar últimas leituras
4. **Logs**: Adicionar logging de erros e conexões
