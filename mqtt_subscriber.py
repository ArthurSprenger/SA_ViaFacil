import json
import os
import re
import ssl
from datetime import datetime
from typing import Callable, Dict, List, Optional, Tuple

import mysql.connector
import paho.mqtt.client as mqtt
from dotenv import load_dotenv

load_dotenv()

MQTT_BROKER = os.getenv('MQTT_BROKER', 'broker.hivemq.com')
MQTT_PORT = int(os.getenv('MQTT_PORT', '8883'))
MQTT_TOPIC = os.getenv('MQTT_TOPIC', '')
MQTT_TOPICS = os.getenv('MQTT_TOPICS', '')
MQTT_USERNAME = os.getenv('MQTT_USERNAME')
MQTT_PASSWORD = os.getenv('MQTT_PASSWORD')
MQTT_TLS_ENABLED = os.getenv('MQTT_TLS_ENABLED', 'true').lower() in ('1', 'true', 'yes')
MQTT_TLS_CAFILE = os.getenv('MQTT_TLS_CAFILE')
MQTT_CLIENT_ID = os.getenv('MQTT_CLIENT_ID', 'viafacil_subscriber')

DB_CONFIG = {
    'host': os.getenv('DB_HOST', 'localhost'),
    'user': os.getenv('DB_USER', 'root'),
    'password': os.getenv('DB_PASSWORD', ''),
    'database': os.getenv('DB_NAME', 'sa_viafacil_db')
}

NUMERIC_REGEX = re.compile(r'-?\d+(?:[\.,]\d+)?')

def parse_numeric_payload(payload: str) -> Optional[float]:
    match = NUMERIC_REGEX.search(payload)
    if not match:
        return None
    value = match.group(0).replace(',', '.')
    try:
        return float(value)
    except ValueError:
        return None

def parse_presence_payload(payload: str) -> Optional[float]:
    normalized = payload.strip().lower()
    if normalized in {'1', 'true', 'ativo', 'on', 'detectado'}:
        return 1.0
    if normalized in {'0', 'false', 'inativo', 'off', 'livre'}:
        return 0.0
    return parse_numeric_payload(payload)

SENSOR_TOPIC_MAP: Dict[str, Dict[str, object]] = {
    'S1 umidade':      {'tipo': 's1_umidade',      'unidade': '%',    'parser': parse_numeric_payload},
    'S1 temperatura':  {'tipo': 's1_temperatura',  'unidade': '°C',   'parser': parse_numeric_payload},
    'S1 iluminacao':   {'tipo': 's1_iluminacao',   'unidade': 'lux',  'parser': parse_numeric_payload},
    'Projeto S2 Distancia1': {'tipo': 's2_distancia1', 'unidade': 'cm',   'parser': parse_numeric_payload},
    'Projeto S2 Distancia2': {'tipo': 's2_distancia2', 'unidade': 'cm',   'parser': parse_numeric_payload},
    'Projeto S3 Presenca3':  {'tipo': 's3_presenca',   'unidade': 'status', 'parser': parse_presence_payload},
    'Projeto S3 Ultrassom3': {'tipo': 's3_ultrassom',  'unidade': 'cm',   'parser': parse_numeric_payload},
    'projeto trem velocidade': {'tipo': 'trem_velocidade', 'unidade': 'km/h', 'parser': parse_numeric_payload},
}

def resolve_topics() -> List[str]:
    explicit = [t.strip() for t in MQTT_TOPICS.replace('"', '').split(',') if t.strip()]
    fallback = MQTT_TOPIC.strip()
    topics = explicit
    if fallback:
        topics.append(fallback)
    return sorted(set(topics))

SUBSCRIPTION_LIST = resolve_topics()

def extract_measurement(topic: str, payload_raw: bytes) -> Tuple[str, float, str]:
    payload = payload_raw.decode(errors='ignore').strip()

    try:
        data = json.loads(payload)
        tipo = data.get('tipo')
        valor = data.get('valor')
        unidade = data.get('unidade')
        if tipo and valor is not None:
            return tipo, float(valor), unidade or ''
    except json.JSONDecodeError:
        pass

    config = SENSOR_TOPIC_MAP.get(topic)
    if not config:
        raise ValueError(f"Tópico '{topic}' não está mapeado e payload não é JSON")

    parser = config.get('parser', parse_numeric_payload)
    valor = parser(payload)
    if valor is None:
        raise ValueError(f"Não foi possível interpretar valor numérico para o tópico '{topic}'")

    return config['tipo'], float(valor), config.get('unidade', '')

def get_db_connection():
    return mysql.connector.connect(**DB_CONFIG)

def get_or_create_sensor(cursor, tipo):
    cursor.execute("SELECT id FROM sensor WHERE tipo = %s AND status = 'ativo' LIMIT 1", (tipo,))
    result = cursor.fetchone()
    
    if result:
        return result[0]
    
    print(f"  [AVISO] Sensor tipo '{tipo}' não encontrado - criando novo sensor...")
    descricao = f"Sensor {tipo} - Auto-criado via MQTT"
    cursor.execute("INSERT INTO sensor (tipo, descricao, status) VALUES (%s, %s, 'ativo')", (tipo, descricao))
    sensor_id = cursor.lastrowid
    print(f"  [OK] Novo sensor criado com ID: {sensor_id}")
    return sensor_id

def on_connect(client, userdata, flags, rc):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    if rc == 0:
        print(f"[{timestamp}] ✓ Conectado ao broker com sucesso!")
        topics = SUBSCRIPTION_LIST or [MQTT_TOPIC]
        for topic in topics:
            client.subscribe(topic)
            print(f"[{timestamp}] ✓ Inscrito no tópico: {topic}")
        print(f"[{timestamp}] Aguardando mensagens... (Ctrl+C para sair)")
        print("-" * 60)
    else:
        print(f"[ERRO] Falha na conexão. Código: {rc}")

def on_message(client, userdata, msg):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    print(f"\n[{timestamp}] Nova mensagem recebida!")
    print(f"  Tópico: {msg.topic}")
    print(f"  Payload: {msg.payload.decode(errors='ignore')}")

    try:
        tipo, valor, unidade = extract_measurement(msg.topic, msg.payload)
    except ValueError as err:
        print(f"  [ERRO] {err}")
        return
     
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        
        id_sensor = get_or_create_sensor(cursor, tipo)
        
        cursor.execute(
            "INSERT INTO sensor_data (id_sensor, valor, unidade, data_hora) VALUES (%s, %s, %s, NOW())",
            (id_sensor, valor, unidade)
        )
        
        conn.commit()
        
        print(f"  [✓] Dados salvos no banco com sucesso!")
        print(f"      ID Sensor: {id_sensor} | Valor: {valor} {unidade}")
        
        cursor.close()
        conn.close()
        
    except mysql.connector.Error as err:
        print(f"  [ERRO] Falha no banco de dados: {err}")
    except Exception as e:
        print(f"  [ERRO] Erro inesperado: {e}")

def on_disconnect(client, userdata, rc):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    if rc != 0:
        print(f"\n[{timestamp}] [AVISO] Desconectado inesperadamente. Tentando reconectar...")

def main():
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Iniciando cliente MQTT assinante...")
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Conectando ao broker: {MQTT_BROKER}:{MQTT_PORT}")
    if SUBSCRIPTION_LIST:
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Tópicos: {', '.join(SUBSCRIPTION_LIST)}")
    elif MQTT_TOPIC:
        print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Tópico: {MQTT_TOPIC}")
    print("-" * 60)
    
    client = mqtt.Client(client_id=MQTT_CLIENT_ID, callback_api_version=mqtt.CallbackAPIVersion.VERSION1)

    if MQTT_USERNAME and MQTT_PASSWORD:
        client.username_pw_set(MQTT_USERNAME, MQTT_PASSWORD)

    if MQTT_TLS_ENABLED:
        context = ssl.create_default_context()
        if MQTT_TLS_CAFILE:
            context.load_verify_locations(MQTT_TLS_CAFILE)
        client.tls_set_context(context)
        client.tls_insecure_set(False)
    
    client.on_connect = on_connect
    client.on_message = on_message
    client.on_disconnect = on_disconnect
    client.reconnect_delay_set(min_delay=2, max_delay=30)
    
    try:
        client.connect(MQTT_BROKER, MQTT_PORT, 60)
        client.loop_forever()
    except KeyboardInterrupt:
        print("\n\n[INFO] Encerrando cliente MQTT...")
        client.disconnect()
        print("[INFO] Cliente desconectado com sucesso!")
    except Exception as e:
        print(f"[ERRO] Erro fatal: {e}")

if __name__ == "__main__":
    main()
