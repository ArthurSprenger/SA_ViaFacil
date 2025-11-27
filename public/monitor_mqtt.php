<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Monitor MQTT - ViaFácil</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
            background: #f5f5f5;
        }
        h1 {
            color: #003366;
            text-align: center;
        }
        .sensor-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 30px;
        }
        .sensor-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }
        .sensor-title {
            font-size: 14px;
            color: #666;
            margin-bottom: 10px;
        }
        .sensor-value {
            font-size: 32px;
            font-weight: bold;
            color: #003366;
        }
        .sensor-time {
            font-size: 12px;
            color: #999;
            margin-top: 10px;
        }
        .status {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 12px;
            font-size: 12px;
            margin-top: 10px;
        }
        .status.online {
            background: #43b649;
            color: white;
        }
        .status.offline {
            background: #e53935;
            color: white;
        }
    </style>
</head>
<body>
    <h1>Monitor de Sensores MQTT</h1>
    <div class="sensor-grid">
        <div class="sensor-card">
            <div class="sensor-title">Temperatura S1</div>
            <div class="sensor-value" id="temp-s1">--</div>
            <div class="sensor-time" id="time-s1">Aguardando dados...</div>
            <span class="status offline" id="status-s1">Offline</span>
        </div>
        <div class="sensor-card">
            <div class="sensor-title">Umidade S1</div>
            <div class="sensor-value" id="umid-s1">--</div>
            <div class="sensor-time" id="time-umid">Aguardando dados...</div>
            <span class="status offline" id="status-umid">Offline</span>
        </div>
        <div class="sensor-card">
            <div class="sensor-title">Iluminação S1</div>
            <div class="sensor-value" id="ilum-s1">--</div>
            <div class="sensor-time" id="time-ilum">Aguardando dados...</div>
            <span class="status offline" id="status-ilum">Offline</span>
        </div>
        <div class="sensor-card">
            <div class="sensor-title">Velocidade Trem</div>
            <div class="sensor-value" id="vel-trem">--</div>
            <div class="sensor-time" id="time-trem">Aguardando dados...</div>
            <span class="status offline" id="status-trem">Offline</span>
        </div>
    </div>

    <script>
        function updateSensor(endpoint, valueId, timeId, statusId, unit = '') {
            fetch(endpoint)
                .then(r => r.text())
                .then(data => {
                    const trimmed = data.trim();
                    if (trimmed !== "" && trimmed !== "0" && !trimmed.includes('erro')) {
                        document.getElementById(valueId).textContent = trimmed + unit;
                        document.getElementById(timeId).textContent = 'Atualizado: ' + new Date().toLocaleTimeString('pt-BR');
                        const status = document.getElementById(statusId);
                        status.textContent = 'Online';
                        status.className = 'status online';
                    }
                })
                .catch(err => console.error(err));
        }

        // Atualizar a cada 3 segundos
        setInterval(() => {
            updateSensor('get_mqtt_message.php?topic=S1 temperatura', 'temp-s1', 'time-s1', 'status-s1', '°C');
            updateSensor('get_mqtt_message.php?topic=S1 umidade', 'umid-s1', 'time-umid', 'status-umid', '%');
            updateSensor('get_mqtt_message.php?topic=S1 iluminacao', 'ilum-s1', 'time-ilum', 'status-ilum', ' lux');
            updateSensor('get_mqtt_message.php?topic=projeto trem velocidade', 'vel-trem', 'time-trem', 'status-trem', ' km/h');
        }, 3000);
    </script>
</body>
</html>
