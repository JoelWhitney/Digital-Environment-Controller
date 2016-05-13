/*
  SIE557 - Final Project - Sensor node program
This simple sketch..

 The circuit:
 * The...
*/

// sensor pins
const int switchPin = 2;
const int tempSensorPin = A0;
const int lightSensorPin = A1;


void setup() {
  Serial.begin(9600);
  pinMode(switchPin, INPUT);
}

void loop() {
  int switchState = digitalRead(switchPin);
  Serial.println("Measurand:" + String(switchPin) + "," + String(switchState));
  delay(1);
  
  int lightValue = analogRead(lightSensorPin);
  Serial.println("Measurand:" + String(lightSensorPin) + "," + String(lightValue));
  delay(1);
  
  int tempValue = analogRead(tempSensorPin);
  float tempVoltage = (tempValue / 1024.0) * 5.0;
  float temperatureC = (tempVoltage - 0.5) * 100.0;
  Serial.println("Measurand:" + String(tempSensorPin) + "," + String(temperatureC));
  delay(10000);
}

