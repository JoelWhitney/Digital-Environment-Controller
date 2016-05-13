/*
  SIE557 - Final Project - Control Panel program
This simple sketch listens to the serial port and is getting messages from
the Raspberry Pi XBee radio. When a message comes in it, it parses the data from buffer
and changes the state of the relayStateArray to turn on/off for the appropiate outlets.
It sends a message back when a state is changed (ex. "Action:2,1").

 The circuit:
 * The 8 outlets are connected to a relay module
 * that is connected to the arduino (pins 2-9)
 * XBee: Listen to Pi XBee and send to Pi Xbee
*/

int pinArray[] = {2,3,4,5,6,7,8,9}; // define the pins that will connect to the relays
int relayStateArray[] = {1,1,1,1,1,1,1,1}; // track the state of a relay (on or off). Start with all off
int i = 0; // used for the "for" statements (loops that cycle through all 8 relays).
String inputString = ""; // a string to hold incoming data
boolean stringComplete = false; // whether the string is complete
int lastState = 1; // used for tracking changes
char newStateChar;
int newState = 1; // used for tracking changes

void setup() {
  Serial.begin(9600); // opens serial port, sets data rate to 9600 bps
  for(i=0;i<8;i++){  // set the pinMode to OUTPUT for the 8 pins that connect to the relays
    pinMode(pinArray[i],OUTPUT);
    digitalWrite(pinArray[i], relayStateArray[i]);
  }
  // reserve 10 bytes for the inputString:
  inputString.reserve(10);
}

void loop() {
  // read data only when you receive data:
  if (stringComplete) {
    for(i=0;i<8;i++){
      lastState = relayStateArray[i];
      newStateChar = inputString.charAt(i);
      newState = newStateChar - '0';
      relayStateArray[i] = newState;  // just changes the value in the state array
      digitalWrite(pinArray[i],relayStateArray[i]); // this turns the relay on or off based on that value
      delay(100);
      if (lastState != relayStateArray[i]) {
        Serial.println("Action:" + String(pinArray[i]-1) + "," + String(newState));
      }  
    }
    // clear the string:
    inputString = "";
    stringComplete = false;
  }
}

/*
  SerialEvent occurs whenever a new data comes in the
 hardware serial RX.  This routine is run between each
 time loop() runs, so using delay inside loop can delay
 response.  Multiple bytes of data may be available.
 */
void serialEvent() {
  while (Serial.available()) {
    // get the new byte:
    char inChar = (char)Serial.read();
    // add it to the inputString:
    inputString += inChar;
    // if the incoming character is a newline, set a flag
    // so the main loop can do something about it:
    if (inChar == '\n') {
      stringComplete = true;
    }
  }
}

