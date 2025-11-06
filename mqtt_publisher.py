import paho.mqtt.client as mqtt
import json
import random
import time
import os
from datetime import datetime
from dotenv import load_dotenv

load_dotenv()

MQTT_BROKER = os.getenv('MQTT_BROKER')
MQTT_PORT = int(os.getenv('MQTT_PORT'))
MQTT_CLIENT_ID = "viafacil_publisher"

SENSORES = [
    {
        'tipo': 'temperatura_freio',
        'unidade': '°C',
        'min': 60,
        'max': 120
    },
    {
        'tipo': 'vibracao_motor',
        'unidade': 'mm/s',
        'min': 1.5,
        'max': 5.0
    },
    {
        'tipo': 'pressao_ar',
        'unidade': 'bar',
        'min': 7.0,
        'max': 9.5
    },
    {
        'tipo': 'temperatura_motor',
        'unidade': '°C',
        'min': 70,
        'max': 110
    }
]

def on_connect(client, userdata, flags, rc):
    timestamp = datetime.now().strftime('%Y-%m-%d %H:%M:%S')
    if rc == 0:
        print(f"[{timestamp}] ✓ Conectado ao broker!")
        print(f"[{timestamp}] Publicando dados a cada 5 segundos... (Ctrl+C para sair)")
        print("-" * 60)
    else:
        print(f"[ERRO] Falha na conexão. Código: {rc}")

def main():
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Iniciando Simulador MQTT...")
    print(f"[{datetime.now().strftime('%Y-%m-%d %H:%M:%S')}] Broker: {MQTT_BROKER}:{MQTT_PORT}")
    print("-" * 60)
    
    client = mqtt.Client(client_id=MQTT_CLIENT_ID, callback_api_version=mqtt.CallbackAPIVersion.VERSION1)
    client.on_connect = on_connect
    
    try:
        client.connect(MQTT_BROKER, MQTT_PORT, 60)
        client.loop_start()
        
        contador = 0
        
        while True:
            contador += 1
            
            for sensor in SENSORES:
                valor = round(random.uniform(sensor['min'], sensor['max']), 2)
                
                payload = {
                    'tipo': sensor['tipo'],
                    'valor': valor,
                    'unidade': sensor['unidade'],
                    'timestamp': datetime.now().strftime('%Y-%m-%d %H:%M:%S')
                }
                
                topic = f"viafacil/sensores/{sensor['tipo']}"
                
                client.publish(topic, json.dumps(payload))
                
                print(f"[{datetime.now().strftime('%H:%M:%S')}] [{contador}] Publicado: {sensor['tipo']} = {valor} {sensor['unidade']}")
            
            print("-" * 60)
            time.sleep(5)
            
    except KeyboardInterrupt:
        print("\n\n[INFO] Encerrando simulador MQTT...")
        client.loop_stop()
        client.disconnect()
        print("[INFO] Simulador desconectado com sucesso!")
    except Exception as e:
        print(f"[ERRO] Erro fatal: {e}")

if __name__ == "__main__":
    main()
