c#include <WiFi.h>
#include <PubSubClient.h>

WiFiClient client;
PubSubClient mqtt(client);

const String SSID = "FIESC_IOT_EDU";
const String PASS = "8120gv08";

const int PORT               = 1883;
const String URL               = "test.mosquitto.org";
const String TOPIC            = "DSM2";
const String broker_user      = "";
const String broker_pass      = "";

void setup() {
  // put your setup code here, to run once:
  Serial.begin(115200);
  Serial.println("Conectando ao Wifi");
  WiFi.begin(SSID,PASS);
  while(WiFi.status() != WL_CONNECTED){
    Serial.print(".");
    delay(200);
  }
  Serial.println("\nConectado!");
    Serial.println("Conectando ao broker...");
    mqtt.setServer(URL.c_str(),PORT);
   while(!mqtt.connected()){
    String ID = "S1-";
    ID += String(random(0xffff),HEX);
    mqtt.connect(ID.c_str(),broker_user.c_str(),broker_pass.c_str());
    delay(200);
    Serial.print(".");
}
Serial.println("\n Conectado ao broker com sucesso!");
}

void loop() {
  // put your main code here, to run repeatedly:
}