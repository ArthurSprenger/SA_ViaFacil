import paho.mqtt.client as mqtt
import mysql.connector
import json
import os
from datetime import datetime
from dotenv import load_dotenv

load_dotenv()

MQTT_BROKER = os.getenv('MQTT_BROKER')
MQTT_PORT = int(os.getenv('MQTT_PORT'))
MQTT_TOPIC = os.getenv('MQTT_TOPIC')
MQTT_CLIENT_ID = os.getenv('MQTT_CLIENT_ID')

DB_CONFIG = {
    'host': os.getenv('DB_HOST'),
    'user': os.getenv('DB_USER'),
    'password': os.getenv('DB_PASSWORD'),
    'database': os.getenv('DB_NAME')
}

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
        client.subscribe(MQTT_TOPIC)
        print(f"[{timestamp}] ✓ Inscrito no tópico: {MQTT_TOPIC}")
        print(f"[{timestamp}] Aguardando mensagens... (Ctrl+C para sair)")
        print("-" * 60)
    else:
        print(f"[ERRO] Falha na conexão. Código: {rc}")

def on_message(client, userdata, msg):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    print(f"\n[{timestamp}] Nova mensagem recebida!")
    print(f"  Tópico: {msg.topic}")
    print(f"  Payload: {msg.payload.decode()}")
    
    try:
        data = json.loads(msg.payload.decode())
    except json.JSONDecodeError:
        print("  [ERRO] JSON inválido - mensagem ignorada")
        return
    
    tipo = data.get('tipo')
    valor = data.get('valor')
    unidade = data.get('unidade')
    
    if not tipo or valor is None or not unidade:
        print("  [ERRO] Campos obrigatórios ausentes (tipo, valor, unidade)")
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
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Tópico: {MQTT_TOPIC}")
    print("-" * 60)
    
    client = mqtt.Client(client_id=MQTT_CLIENT_ID, callback_api_version=mqtt.CallbackAPIVersion.VERSION1)
    
    client.on_connect = on_connect
    client.on_message = on_message
    client.on_disconnect = on_disconnect
    
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
