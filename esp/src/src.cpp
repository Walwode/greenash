// ESP8266 and interrups: https://techtutorialsx.com/2016/12/11/esp8266-external-interrupts/
// ESP8266 platform 2.5.0 required, else IRAM error (https://github.com/platformio/platform-espressif8266.git#32f0b31)

#include "config.h"
#include <ArduinoJson.h>
#include <DNSServer.h>
#include <ESP8266HTTPClient.h>
#include <ESP8266WebServer.h>
#include <ESP8266WiFi.h>
// #include <HTTPClient.h>
// #include <WebServer.h>
// #include <WiFi.h>
#include <SSD1306Wire.h>
#include <Ticker.h>
#include <WiFiManager.h>

// defaults
SSD1306Wire  display(0x3c, D3, D5); // 128x64 pixel
const byte interruptPin = 13; // D7
Ticker ticker;

// internet values
char* data_name;
int data_diameter = 80; // mm
int data_faintInterval = 30;
int data_pushInterval = 1000;
boolean data_hasOled = false;

// interrupt volatiles
volatile byte interruptCounter = 0;
volatile double currentInterrupt, lastInterrupt;

// globales
#define SPEED_AVG_COUNT 5
double circumference;
double cumulatedDistance;
double cumulatedInterruptCounter;
double currentMillis, lastMillis;
double distance;
double speed[SPEED_AVG_COUNT];
int lastSpeedIndex;
String url;

void handleInterrupt();
void tick();
void configModeCallback (WiFiManager *myWiFiManager);
void setupWiFi();
void getConfiguration();
void setupSpeed();
void setupDisplay();
void calculateSpeed();
double getAverageSpeed();
void updateDisplay();
void sendWiFi();

void setup() {
  Serial.begin(115200);
  while (!Serial);

  setupWiFi();
  getConfiguration();
  setupSpeed();
  setupDisplay();
}

void loop() {
  calculateSpeed();
  if (data_hasOled) updateDisplay();
  sendWiFi();
  delay(data_pushInterval);
}

void handleInterrupt() {
  currentInterrupt = millis();
  if ((currentInterrupt - lastInterrupt) > data_faintInterval) {
    interruptCounter++;
    lastInterrupt = currentInterrupt;
  }
}

void tick() {
  int state = digitalRead(2);
  digitalWrite(2, !state);
}

void configModeCallback (WiFiManager *myWiFiManager) {
  Serial.println("[WiFi] Entered config mode");
  Serial.println(WiFi.softAPIP());
  Serial.println(myWiFiManager->getConfigPortalSSID()); // auto generated SSID
  ticker.attach(0.2, tick);
}

void setupWiFi() {
  pinMode(2, OUTPUT);
  ticker.attach(0.6, tick);
  WiFiManager wifiManager;
  wifiManager.setAPCallback(configModeCallback);
  if (!wifiManager.autoConnect()) {
    Serial.println("[WiFi] Failed to connect and hit timeout");
    ESP.reset(); // reset and try again, or maybe put it to deep sleep
    delay(1000);
  }
  Serial.println("[WiFi] Connected to WiFi");
  ticker.detach();
  digitalWrite(2, LOW);
}

void getConfiguration() {
  ticker.attach(2.0, tick);
  // atol(root["entryNo"].as<char*>());
  while (data_diameter == 0) {
    if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http; // Object of class HTTPClient
      url = API_URL;
      url += "?action=get";
      url += "&chipId=" + String(ESP.getChipId());
      http.begin(url);
      Serial.println("[API] Get configuration: " + url);
      int httpCode = http.GET();
      if (httpCode > 0) {

        const size_t capacity = JSON_ARRAY_SIZE(1) + JSON_OBJECT_SIZE(2) + JSON_OBJECT_SIZE(8) + 190;
        DynamicJsonDocument doc(capacity);

        deserializeJson(doc, http.getString());

        // const char* version = doc["version"]; // "1.0"
        JsonObject data_0 = doc["data"][0];
        // const char* data_chipId = data_0["chipId"]; // "1"
        // data_name = data_0["name"]; // "Johanniter Unfallhilfe e.V. Regionalverband Dresden"
        // const char* data_active = data_0["active"]; // "1"
        data_diameter = data_0["diameter"].as<int>(); // "80"
        data_faintInterval = data_0["faintInterval"].as<int>(); // "30"
        data_pushInterval = data_0["pushInterval"].as<int>(); // "1000"
        data_hasOled = data_0["hasOled"]; // "0"
        // const char* data_colorCode = data_0["colorCode"]; // "#12130F"

        Serial.println("[API] Configuration received");
      } else {
        Serial.println("[API] Config. http error: " + http.errorToString(httpCode));
      }
      http.end(); // Close connection
    } else ESP.reset();
    if (data_diameter == 0) delay(1000);
  }
  ticker.detach();
  digitalWrite(2, LOW);
}

void setupSpeed() {
  pinMode(interruptPin, INPUT_PULLUP);
  attachInterrupt(digitalPinToInterrupt(interruptPin), handleInterrupt, RISING);
  circumference = PI * data_diameter; // U = 2*pi*r
  
  Serial.println("Created interrupts");
  Serial.print("Circumference: ");
  Serial.println(circumference);
}

void setupDisplay() {
  display.init();
  display.flipScreenVertically();
  display.setFont(ArialMT_Plain_10);
  display.setTextAlignment(TEXT_ALIGN_LEFT);
}

void calculateSpeed() {
  detachInterrupt(digitalPinToInterrupt(interruptPin));
  currentMillis = millis();
  int rotations = interruptCounter;
  interruptCounter = 0;
  attachInterrupt(digitalPinToInterrupt(interruptPin), handleInterrupt, RISING);

  int millisDifference = currentMillis - lastMillis;

  distance = rotations * circumference / 1000; // [m]
  speed[lastSpeedIndex] = distance * 60 * 60 / millisDifference; // [km/h]
  lastSpeedIndex++;
  if (lastSpeedIndex > SPEED_AVG_COUNT) lastSpeedIndex = 0;

  cumulatedDistance += distance;
  cumulatedInterruptCounter += rotations;

  lastMillis = currentMillis;
}

double getAverageSpeed() {
  double averageSpeed = 0;
  for (int i = 0; i < SPEED_AVG_COUNT; i++) {
    averageSpeed += speed[i];
  }
  return averageSpeed / SPEED_AVG_COUNT;  
}

void updateDisplay() {
  // sprintf(tMin, "%02d", targetDuration / 60);

  display.clear();
  display.drawString(0, 0, "Interrupts: " + String(cumulatedInterruptCounter));
  display.drawString(0, 15, "Speed: " + String(getAverageSpeed()) + "km/h");
  display.drawString(0, 25, "Distance: " + String(cumulatedDistance) + "m");
  // display.drawString(0, 40, "D " + String(currentInterrupt) + "-" + String(lastInterrupt));
  display.drawString(0, 40, String(millis()));
  display.display();
}

void sendWiFi() {
  if (WiFi.status() == WL_CONNECTED) {
      HTTPClient http; // Object of class HTTPClient
      url = API_URL;
      url += "?action=log";
      url += "&chipId=" + String(ESP.getChipId());
      url += "&distance=" + String(distance);
      url += "&speed=" + String(getAverageSpeed());
      url += "&cumulatedDistance=" + String(cumulatedDistance);
      http.begin(url);
      int httpCode = http.GET();
      if (httpCode > 0) {
        Serial.println("[API] Uploaded data: " + url);
      } else Serial.println("[API] Upload http error: " + http.errorToString(httpCode));
  } else ESP.reset();
}

