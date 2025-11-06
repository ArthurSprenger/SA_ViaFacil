# Sistema MQTT - ViaFácil

## Arquivos Criados

1. **includes/phpMQTT.php** - Biblioteca MQTT PHP
2. **mqtt_subscriber.php** - Cliente que escuta mensagens e salva no banco
3. **mqtt_publisher.php** - Simulador que publica dados de sensores

## Como Usar

### 1. Executar o Assinante (Escutador)

Abra um terminal PowerShell e execute:

```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
php mqtt_subscriber.php
```

Este script irá:
- Conectar ao broker HiveMQ
- Assinar o tópico `viafacil/sensores/#`
- Aguardar mensagens
- Salvar automaticamente no banco de dados

### 2. Executar o Publicador (Simulador)

Abra OUTRO terminal PowerShell e execute:

```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
php mqtt_publisher.php
```

Este script irá:
- Conectar ao broker HiveMQ
- Publicar dados de 4 sensores a cada 5 segundos:
  - temperatura_freio (60-120°C)
  - vibracao_motor (1.5-5.0 mm/s)
  - pressao_ar (7.0-9.5 bar)
  - temperatura_motor (70-110°C)

## Formato das Mensagens

```json
{
  "tipo": "temperatura_freio",
  "valor": 85.5,
  "unidade": "°C",
  "timestamp": "2025-11-06 14:30:00"
}
```

## Broker MQTT

- **Servidor**: broker.hivemq.com
- **Porta**: 1883
- **Tópico Base**: viafacil/sensores/
- **Autenticação**: Não requerida (broker público)

## Funcionalidades do Assinante

✅ Validação de JSON
✅ Validação de campos obrigatórios (tipo, valor, unidade)
✅ Criação automática de novos sensores
✅ Salvamento seguro no banco de dados
✅ Logs detalhados em tempo real
✅ Tratamento de erros

## Banco de Dados

Os dados são salvos em duas tabelas:

- **sensor**: Armazena tipos de sensores
- **sensor_data**: Armazena leituras dos sensores com timestamp

## Para Parar os Scripts

Pressione `Ctrl+C` em cada terminal.
