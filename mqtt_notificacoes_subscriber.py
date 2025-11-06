import paho.mqtt.client as mqtt
import mysql.connector
import json
import os
from datetime import datetime
from dotenv import load_dotenv

load_dotenv()

MQTT_BROKER = os.getenv('MQTT_BROKER')
MQTT_PORT = int(os.getenv('MQTT_PORT'))
MQTT_TOPIC_NOTIFICACOES = "viafacil/notificacoes/#"
MQTT_CLIENT_ID = "viafacil_notificacoes_subscriber"

DB_CONFIG = {
    'host': os.getenv('DB_HOST'),
    'user': os.getenv('DB_USER'),
    'password': os.getenv('DB_PASSWORD'),
    'database': os.getenv('DB_NAME')
}

def get_db_connection():
    return mysql.connector.connect(**DB_CONFIG)

def on_connect(client, userdata, flags, rc):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    if rc == 0:
        print(f"[{timestamp}] ✓ Conectado ao broker MQTT!")
        client.subscribe(MQTT_TOPIC_NOTIFICACOES)
        print(f"[{timestamp}] ✓ Inscrito no tópico: {MQTT_TOPIC_NOTIFICACOES}")
        print(f"[{timestamp}] Aguardando notificações...")
        print("-" * 60)
    else:
        print(f"[ERRO] Falha na conexão. Código: {rc}")

def on_message(client, userdata, msg):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    print(f"\n[{timestamp}] Nova notificação recebida!")
    print(f"  Tópico: {msg.topic}")
    print(f"  Payload: {msg.payload.decode()}")
    
    try:
        data = json.loads(msg.payload.decode())
    except json.JSONDecodeError:
        print("  [ERRO] JSON inválido")
        return
    
    tipo = data.get('tipo')
    titulo = data.get('titulo')
    mensagem = data.get('mensagem')
    usuario_remetente_id = data.get('usuario_remetente_id')
    usuario_destinatario_id = data.get('usuario_destinatario_id')
    
    if not all([tipo, titulo, mensagem, usuario_remetente_id]):
        print("  [ERRO] Campos obrigatórios ausentes")
        return
    
    try:
        conn = get_db_connection()
        cursor = conn.cursor()
        
        if tipo == 'aviso':
            cursor.execute(
                "INSERT INTO avisos (titulo, mensagem, usuario_id) VALUES (%s, %s, %s)",
                (titulo, mensagem, usuario_remetente_id)
            )
            print(f"  [✓] Aviso salvo no banco!")
        
        cursor.execute(
            "INSERT INTO notificacoes (tipo, titulo, mensagem, usuario_remetente_id, usuario_destinatario_id) VALUES (%s, %s, %s, %s, %s)",
            (tipo, titulo, mensagem, usuario_remetente_id, usuario_destinatario_id)
        )
        
        conn.commit()
        print(f"  [✓] Notificação salva: {tipo} - {titulo}")
        
        cursor.close()
        conn.close()
        
    except mysql.connector.Error as err:
        print(f"  [ERRO] Banco de dados: {err}")
    except Exception as e:
        print(f"  [ERRO] Inesperado: {e}")

def main():
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Iniciando sistema de notificações...")
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Broker: {MQTT_BROKER}:{MQTT_PORT}")
    print("-" * 60)
    
    client = mqtt.Client(client_id=MQTT_CLIENT_ID, callback_api_version=mqtt.CallbackAPIVersion.VERSION1)
    client.on_connect = on_connect
    client.on_message = on_message
    
    try:
        client.connect(MQTT_BROKER, MQTT_PORT, 60)
        client.loop_forever()
    except KeyboardInterrupt:
        print("\n\n[INFO] Encerrando sistema de notificações...")
        client.disconnect()
        print("[INFO] Desconectado!")
    except Exception as e:
        print(f"[ERRO] Fatal: {e}")

if __name__ == "__main__":
    main()
