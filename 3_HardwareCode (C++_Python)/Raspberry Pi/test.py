import datetime

__author__ = 'joelwhitney'
# SIE 557 - Final - Insert into DB
# -read serial of Sensor node and write to measurands
# -read serial of Control node and write toactions

# this file:
# 1) opens connection to mysql db and sets up insert statement
# 2) set up pi serial connection
# 3) opens file to write results to and start reading from serial

# imports
import serial

# 2) set up pi serial connection
ser = serial.Serial('COM12', 9600, timeout = 2) # Windows
ser.flushInput()
# sends packet of signals to serial
# TEST IF YOU CAN ITERATE ACROSS PACKET. WILL NEED THIS WHEN I HAVE TO BUILD PACKET
packet = '11111111\n'
ser.write(packet.encode('ascii'))

# create variable for each serial readline result
responses = ser.readlines()
for i in range(len(responses)):
    # split to type of response and values
    sinResponse = str(responses[i])
    sinResponse = sinResponse.split(":")
    type = sinResponse[0]
    values = sinResponse[1]
    outResponse = values[:-5] + "," + str(datetime.datetime.now())
    # print response
    print(outResponse)
