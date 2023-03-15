// Import required libraries
#include "WiFi.h"
#include "ESPAsyncWebServer.h"
#include <Adafruit_Sensor.h>
#include <DHT.h>
#include <Servo.h>
#include <HTTPClient.h>

// Replace with your network credentials
const char* ssid = "MEO-AB25B0"; //Tester
const char* password = "c890678f0b";  //ipt12345
//const char* ssid = "Tester"; //Tester
//const char* password = "ipt12345";  //ipt12345

const int lightsPin = 5; // pin for lights
const int heatPin = 23;  // pin  for heating
const int sensorPin = 33; // pin for light sensor

const int tempOpenRoof = 20;
const int heatOn = 20; 
const int lightOn = 750;
const int readingSensorInterval = 15000;
const int roofOpeningAngle = 60;

//const char*luzes="0";
//const char*aquecimento="0";
//const char*telhado="1";
int luzes = 0;
int aquecimento =0;
int telhado = 0;

int lightVal;   // light reading

Servo myservo; // create servo object to control a servo
int pos = 0;    // variable to store the servo position

#define DHTPIN 27     // Digital pin connected to the DHT sensor

#define DHTTYPE    DHT11     // DHT 11
//#define DHTTYPE    DHT22     // DHT 22 (AM2302)
//#define DHTTYPE    DHT21     // DHT 21 (AM2301)

DHT dht(DHTPIN, DHTTYPE);

String readDHTTemperature() {
  // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)
  // Read temperature as Celsius (the default)
  float temp = dht.readTemperature();
  // Check if any reads failed and exit early (to try again).
  if (isnan(temp)) { Serial.println("Failed to read from DHT sensor!"); return "--"; }
  else { Serial.println(temp); return String(temp); }
}

String readDHTHumidity() {
  // Sensor readings may also be up to 2 seconds 'old' (its a very slow sensor)
  float h = dht.readHumidity();
  if (isnan(h)) { Serial.println("Failed to read from DHT sensor!"); return "--"; }
  else { Serial.println(h); return String(h); }
}

void setup(){

  

  // Serial port for debugging purposes
  pinMode (lightsPin, OUTPUT);
  pinMode (heatPin, OUTPUT);

  myservo.attach(13);  // attaches the servo on pin 13 to the servo object
  
  Serial.begin(115200);

  dht.begin();
  
  // Connect to Wi-Fi
  WiFi.begin(ssid, password);
  while (WiFi.status() != WL_CONNECTED) {
    delay(1000);
    Serial.println("Connecting to WiFi..");
  }

  // Print ESP32 Local IP Address
  Serial.println(WiFi.localIP());

  // HTTPClient http;
  // float temp = dht.readTemperature();
  // float humidade = dht.readHumidity();
  // lightVal = analogRead(sensorPin); // read the current light levels

  // String url = "http://192.168.1.100/db/arduino.php?";
  // url += "temp="+ String(temp);
  // url += "&humidade="+ String(humidade);
  // url += "&sensorLuz="+String(lightVal);
  // url += "&luzes="+String(luzes);
  // url += "&aquecimento="+String(aquecimento);
  // url += "&telhado="+String(telhado);

  // Serial.println(url);

  // http.begin(url);

  // int httpCode = http.GET();

  // if (httpCode > 0) {
  //   String payload = http.getString();
  //   Serial.println(payload);
  // } else {
  //   Serial.println("Falha na requisição");
  // }
  // http.end();

}

void loop()
{
  //Check WiFi connection status
  if(WiFi.status()== WL_CONNECTED){
    WiFiClient client;

  
  float temp = dht.readTemperature();
  if (temp < heatOn )
  {
    digitalWrite (heatPin, HIGH);
    aquecimento =1;
  }
  else
  {
    digitalWrite (heatPin, LOW);
    aquecimento =0;  
  }
  
  float humidade = dht.readHumidity();

  lightVal = analogRead(sensorPin); // read the current light levels

  if(lightVal  <  lightOn ) {
      digitalWrite (lightsPin, HIGH); // turn on light
      luzes = 1;
  }
  //otherwise, it is bright
  else {
    digitalWrite (lightsPin, LOW); // turn off light
    luzes = 0;
  }  

// test lines to ldr sensor response  
// reads the input on analog pin (value between 0 and 4095)
  int analogValue = analogRead(sensorPin);

  Serial.print("Analog Value = ");
  Serial.print(analogValue);   // the raw analog reading

  //delay(readingSensorInterval);

  if (temp < tempOpenRoof )
  {
    myservo.write(0);
    telhado =0;
  }
  else
  {
    myservo.write(roofOpeningAngle);
    telhado =1;
  }

  HTTPClient http;
  //float temp = dht.readTemperature();
 // float humidade = dht.readHumidity();
  lightVal = analogRead(sensorPin); // read the current light levels

  String url = "http://192.168.1.100/db/arduino.php?";
  url += "temp="+ String(temp);
  url += "&humidade="+ String(humidade);
  url += "&sensorLuz="+String(lightVal);
  url += "&luzes="+String(luzes);
  url += "&aquecimento="+String(aquecimento);
  url += "&telhado="+String(telhado);

  Serial.println(url);

  http.begin(url);

  int httpCode = http.GET();

  if (httpCode > 0) {
    String payload = http.getString();
    Serial.println(payload);
  } else {
    Serial.println("Falha na requisição");
  }
  http.end();

  delay(readingSensorInterval);
  }
}
