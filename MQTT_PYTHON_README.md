# Sistema MQTT Python - ViaFácil

## Instalação

### 1. Instalar Python (se não tiver)
Baixe em: https://www.python.org/downloads/

### 2. Instalar as dependências

```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
pip install -r requirements.txt
```

Ou instalar manualmente:

```powershell
pip install paho-mqtt python-dotenv mysql-connector-python
```

## Configuração

Edite o arquivo `.env` com suas configurações:

```env
MQTT_BROKER=broker.hivemq.com
MQTT_PORT=1883
MQTT_TOPIC=viafacil/sensores/#
MQTT_CLIENT_ID=viafacil_subscriber

DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=sa_viafacil_db
```

## Como Usar

### 1. Executar o Assinante (Escutador)

```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
python mqtt_subscriber.py
```

### 2. Executar o Publicador (Simulador)

Abra OUTRO terminal:

```powershell
cd c:\xampp\htdocs\2025_atividades_FelipeC\SA_ViaFacil\SA_ViaFacil
python mqtt_publisher.py
```

## Arquivos Criados

- **mqtt_subscriber.py** - Cliente Python que escuta e salva no banco
- **mqtt_publisher.py** - Simulador Python que publica dados
- **.env** - Arquivo de configuração com variáveis de ambiente
- **requirements.txt** - Dependências Python

## Vantagens do Python

✅ Mais estável para processos longos
✅ Melhor tratamento de exceções
✅ Biblioteca MQTT mais robusta (paho-mqtt)
✅ Variáveis de ambiente com python-dotenv
✅ Reconexão automática
✅ Código mais limpo e legível

## Comparação PHP vs Python

| Característica | PHP | Python |
|---------------|-----|--------|
| Biblioteca MQTT | phpMQTT (3rd party) | paho-mqtt (oficial) |
| Configuração | Hardcoded | .env |
| Reconexão | Manual | Automática |
| Performance | Boa | Excelente |
| Long-running | Não ideal | Ideal |

## Para Parar

Pressione `Ctrl+C` em cada terminal.
