# 🚀 Guia do Worker MQTT Persistente

## O que é o Worker?

O **Worker MQTT** é um processo PHP que roda continuamente em segundo plano, mantendo uma conexão persistente com o broker MQTT. Diferente da abordagem anterior (que conectava por apenas 2-5 segundos), o worker fica **sempre conectado**, recebendo e processando mensagens em tempo real.

## 📊 Comparação: Com vs Sem Worker

### ❌ Sem Worker (Abordagem Antiga)
```
Requisição → Conecta MQTT → Escuta 2s → Desconecta → Retorna dados
     ↓
Requisição → Conecta MQTT → Escuta 2s → Desconecta → Retorna dados
     ↓
(Repete a cada 3 segundos)
```

**Problemas:**
- ⏱️ Conexão muito curta (2 segundos)
- 🔄 Reconecta constantemente (overhead)
- 📉 Pode perder mensagens entre conexões
- ⚡ Lento para estabelecer conexão TLS

### ✅ Com Worker (Abordagem Nova)
```
Worker inicia → Conecta MQTT → Mantém conexão ativa infinitamente
                                         ↓
                            Recebe mensagens em tempo real
                                         ↓
                            Salva no banco imediatamente
                                         ↓
                    Interface web consulta banco atualizado
```

**Vantagens:**
- ⚡ Tempo real instantâneo
- 🔌 Conexão sempre ativa
- 📊 Não perde mensagens
- 💪 Menor overhead de CPU/rede

## 🎯 Como Usar

### 1. Iniciar o Worker

#### Windows (PowerShell):
```powershell
cd c:\xampp\htdocs\sa_certa\SA_ViaFacil
php public/mqtt_worker.php
```

#### Linux/Mac:
```bash
cd /xampp/htdocs/sa_certa/SA_ViaFacil
php public/mqtt_worker.php
```

### 2. Saída Esperada

```
=== Worker MQTT ViaFácil ===
Pressione CTRL+C para encerrar

[2025-11-27 20:30:15] Conectando ao broker MQTT...
[2025-11-27 20:30:16] ✓ Conectado ao broker!
[2025-11-27 20:30:16] Inscrevendo em 8 tópicos...

[2025-11-27 20:30:16] ✓ Worker ativo! Aguardando mensagens...
----------------------------------------------------------------------
[2025-11-27 20:30:18] 📊 S1 temperatura → 25.3 °C (Sensor #2)
[2025-11-27 20:30:19] 📊 S1 umidade → 84.5 % (Sensor #1)
[2025-11-27 20:30:20] 📊 S1 iluminacao → 1 estado (Sensor #3)
[2025-11-27 20:30:45] 💓 Keepalive (Total: 12 mensagens)
```

### 3. Verificar Status na Interface

Acesse: `http://localhost/sa_certa/SA_ViaFacil/public/sensores.php`

Você verá:
- **🟢 MQTT Online** = Worker funcionando
- **⚠️ Modo Offline** = Worker não está rodando

## 🛠️ Recursos do Worker

### Reconexão Automática
Se a conexão cair, o worker tenta reconectar automaticamente:
```
[2025-11-27 20:35:00] ❌ Erro: Connection lost
[2025-11-27 20:35:00] 🔄 Reconectando em 5 segundos... (Tentativa 1/5)
[2025-11-27 20:35:05] Conectando ao broker MQTT...
[2025-11-27 20:35:06] ✓ Conectado ao broker!
```

### Keepalive
A cada 30 segundos, mostra que está ativo:
```
[2025-11-27 20:40:00] 💓 Keepalive (Total: 156 mensagens)
```

### Log em Tempo Real
Cada mensagem recebida é logada:
```
[2025-11-27 20:30:18] 📊 S1 temperatura → 25.3 °C (Sensor #2)
[2025-11-27 20:30:19] 📊 Projeto S2 Distancia1 → objeto_proximo cm (Sensor #4)
[2025-11-27 20:30:20] 📊 projeto trem velocidade → 50.0 km/h (Sensor #8)
```

## 🔧 Configurações do Worker

### Timeout de Reconexão
```php
$max_tentativas = 5;  // Máximo de tentativas antes de desistir
sleep(5);             // Aguarda 5 segundos entre tentativas
```

### Intervalo de Keepalive
```php
if (time() - $ultimo_ping > 30) {  // A cada 30 segundos
    echo "💓 Keepalive\n";
}
```

### Intervalo de Processamento
```php
usleep(50000);  // 50ms entre cada ciclo (reduz CPU)
```

## 🚦 Modos de Operação

### Modo 1: Worker Ativo (Recomendado)
```
1. Inicie o worker no terminal
2. Deixe rodando em segundo plano
3. Acesse a interface web
4. Veja dados em tempo real
```

**Quando usar:** Produção, demonstrações, monitoramento contínuo

### Modo 2: Sem Worker (Fallback)
```
1. Não inicie o worker
2. Acesse a interface web
3. Sistema tenta conectar por 5 segundos a cada requisição
4. Dados podem demorar mais para aparecer
```

**Quando usar:** Desenvolvimento, testes rápidos

### Modo 3: Dados do Banco (Offline)
```
1. Worker não está rodando
2. Dispositivos IoT offline
3. Interface mostra últimos dados salvos
```

**Quando usar:** Sem conexão com dispositivos

## 📝 Comandos Úteis

### Iniciar Worker
```bash
php public/mqtt_worker.php
```

### Iniciar em Background (Linux/Mac)
```bash
nohup php public/mqtt_worker.php > mqtt_worker.log 2>&1 &
```

### Ver Log do Worker
```bash
tail -f mqtt_worker.log
```

### Parar Worker
```bash
# Pressione CTRL+C no terminal
# Ou encontre o processo e mate:
ps aux | grep mqtt_worker
kill <PID>
```

### Iniciar como Serviço (Systemd - Linux)
Crie `/etc/systemd/system/viafacil-mqtt.service`:
```ini
[Unit]
Description=ViaFacil MQTT Worker
After=network.target mysql.service

[Service]
Type=simple
User=www-data
WorkingDirectory=/var/www/SA_ViaFacil
ExecStart=/usr/bin/php public/mqtt_worker.php
Restart=always
RestartSec=10

[Install]
WantedBy=multi-user.target
```

```bash
sudo systemctl daemon-reload
sudo systemctl enable viafacil-mqtt
sudo systemctl start viafacil-mqtt
sudo systemctl status viafacil-mqtt
```

## 🐛 Troubleshooting

### Worker não conecta
```
❌ Erro: Falha ao conectar ao broker
```

**Soluções:**
1. Verifique credenciais em `mqtt_config.php`
2. Teste conexão: `php public/test_mqtt.php`
3. Verifique firewall na porta 8883
4. Confirme que `cacert.pem` existe

### Worker conecta mas não recebe mensagens
```
✓ Worker ativo! Aguardando mensagens...
(Nada aparece)
```

**Soluções:**
1. Verifique se dispositivos IoT estão ligados
2. Confirme que estão conectados ao WiFi
3. Verifique tópicos em `mqtt_config.php`
4. Use HiveMQ Cloud Console para testar

### Worker desconecta constantemente
```
🔄 Reconectando em 5 segundos...
```

**Soluções:**
1. Verifique conexão de internet
2. Broker pode estar sobrecarregado
3. Firewall pode estar bloqueando
4. Aumente timeout do keepalive

### Banco de dados não atualiza
**Soluções:**
1. Verifique logs do worker
2. Confirme que sensores existem no banco
3. Verifique permissões MySQL
4. Teste query manualmente

## 💡 Dicas

### Para Desenvolvimento
- Use **Modo 2** (sem worker) para testes rápidos
- O worker é melhor para longas sessões

### Para Produção
- **Sempre use o worker**
- Configure como serviço do sistema
- Monitore logs regularmente
- Configure restart automático

### Para Demonstrações
- Inicie o worker 5 minutos antes
- Deixe acumular dados no banco
- Interface mostrará histórico + tempo real

## 🎓 Exemplo Completo

### Cenário: Demonstração em Aula

1. **Preparação (5 min antes):**
```bash
# Terminal 1
cd c:\xampp\htdocs\sa_certa\SA_ViaFacil
php public/mqtt_worker.php
```

2. **Ligar Dispositivos:**
- ESP32 S1 (temperatura, umidade, luz)
- ESP32 S2 (distâncias)
- ESP32 S3 (presença, ultrassom)
- Trem (velocidade)

3. **Aguardar Mensagens:**
```
[20:30:18] 📊 S1 temperatura → 25.3 °C
[20:30:19] 📊 S1 umidade → 84.5 %
[20:30:20] 📊 S1 iluminacao → 1 estado
...
```

4. **Abrir Interface:**
```
http://localhost/sa_certa/SA_ViaFacil/public/sensores.php
```

5. **Verificar:**
- ✅ Indicador 🟢 MQTT Online
- ✅ Valores atualizando em tempo real
- ✅ Timestamps recentes

6. **Demonstrar Interatividade:**
- Cubra sensor de luz → veja mudar
- Aproxime mão do ultrassom → veja distância
- Acelere trem → veja velocidade

## 📞 Resumo Rápido

**Problema anterior:** Conexão muito rápida (2 segundos)  
**Solução:** Worker persistente (sempre conectado)  
**Como usar:** `php public/mqtt_worker.php`  
**Resultado:** Dados em tempo real instantâneo! ⚡

---

**Próximo passo:** Deixe o worker rodando e veja a mágica acontecer! 🎉
