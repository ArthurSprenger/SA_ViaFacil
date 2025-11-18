# Sistema MQTT - ViaFácil

## Arquivos Criados

1. **includes/phpMQTT.php** – Biblioteca MQTT pura em PHP.
2. **mqtt_subscriber.php** – Cliente que fica “ouvindo” o broker e grava no MySQL.
3. **mqtt_publisher.php** – Simulador opcional para gerar dados locais.

## Configuração

1. Ajuste o arquivo `.env` com as credenciais do cluster HiveMQ Cloud (valores padrão já apontam para o broker usado pelos dispositivos S1/S2/S3/Trem):

```env
MQTT_BROKER=7aecec580ecf4e5cbac2d52b35eb85b9.s1.eu.hivemq.cloud
MQTT_PORT=8883
MQTT_USERNAME=Henry
MQTT_PASSWORD=HenryDSM2
MQTT_TLS_ENABLED=true
MQTT_TLS_CAFILE=
MQTT_TOPICS="S1 umidade,S1 temperatura,S1 iluminacao,Projeto S2 Distancia1,Projeto S2 Distancia2,Projeto S3 Presenca3,Projeto S3 Ultrassom3,projeto trem velocidade"
MQTT_TOPIC=viafacil/sensores/#
MQTT_CLIENT_ID=viafacil_subscriber
```

> 💡 O arquivo `config/certs/DigiCertGlobalRootCA.crt.pem` contém a AC utilizada pela HiveMQ Cloud. Caso exporte outro certificado, aponte o caminho em `MQTT_TLS_CAFILE`.

## Como Usar

### 1. Executar o Assinante (PHP)

```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
php mqtt_subscriber.php
```

O assinante faz o seguinte:
- Conecta ao HiveMQ Cloud com TLS + autenticação.
- Assina todos os tópicos listados em `MQTT_TOPICS` (S1, S2, S3, Trem etc.).
- Entende payloads JSON **ou** valores simples enviados pelos ESP32.
- Valida campos essenciais e cria o sensor automaticamente, se necessário.
- Persiste cada leitura em `sensor_data`, habilitando o dashboard em tempo real.

### 2. Executar o Publicador (opcional)

```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
php mqtt_publisher.php
```

Útil para testes locais; publica leituras aleatórias a cada 5 segundos no broker público da HiveMQ (sem autenticação).

## Formato das Mensagens

O assinante aceita:

```json
{
  "tipo": "temperatura_freio",
  "valor": 85.5,
  "unidade": "°C"
}
```

ou payloads simples como `"24.7"`, `"Detectado"`, etc., conforme os tópicos mapeados.

## Broker MQTT

- **Servidor (produção)**: `7aecec580ecf4e5cbac2d52b35eb85b9.s1.eu.hivemq.cloud`
- **Porta**: `8883` (TLS)
- **Usuário/Senha**: definidos no `.env`
- **Broker de teste**: `broker.hivemq.com:1883` (usado somente pelo simulador PHP)

## Funcionalidades do Assinante

✅ TLS + autenticação com HiveMQ Cloud
✅ Reconhecimento de múltiplos tópicos simultâneos
✅ Validação de payloads JSON ou texto
✅ Criação automática de sensores ausentes
✅ Persistência segura no MySQL (`sensor` e `sensor_data`)
✅ Logs em tempo real para depuração

## Banco de Dados

- **sensor**: catálogo dos dispositivos monitorados (preenchido automaticamente).
- **sensor_data**: histórico de leituras com valor, unidade e timestamp.

## Encerramento

Pressione `Ctrl+C` no terminal para parar qualquer script.
