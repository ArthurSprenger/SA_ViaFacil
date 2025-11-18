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

Edite o arquivo `.env` com as credenciais do cluster HiveMQ Cloud (os valores padrão já estão configurados com o broker fornecido pelos dispositivos S1/S2/S3/Trem):

```env
MQTT_BROKER=7aecec580ecf4e5cbac2d52b35eb85b9.s1.eu.hivemq.cloud
MQTT_PORT=8883
MQTT_USERNAME=Henry
MQTT_PASSWORD=HenryDSM2
MQTT_TLS_ENABLED=true
MQTT_TLS_CAFILE=
MQTT_TOPICS="S1 umidade,S1 temperatura,S1 iluminacao,Projeto S2 Distancia1,Projeto S2 Distancia2,Projeto S3 Presenca3,Projeto S3 Ultrassom3,projeto trem velocidade"
MQTT_CLIENT_ID=viafacil_subscriber

DB_HOST=localhost
DB_USER=root
DB_PASSWORD=
DB_NAME=sa_viafacil_db
```

> 💡 **Dica:** Caso tenha exportado o certificado da HiveMQ Cloud, informe o caminho em `MQTT_TLS_CAFILE`. Caso contrário, o cliente usará o repositório de certificados confiáveis do sistema operacional.

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

- **mqtt_subscriber.py** - Cliente Python que escuta os tópicos HiveMQ Cloud e salva no banco
- **mqtt_publisher.py** - Simulador Python legado (útil para testes locais)
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
