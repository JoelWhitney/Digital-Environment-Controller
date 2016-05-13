import random

import serial

__author__ = 'joelwhitney'
# SIE 557 - Final Take - Query for events and send emails
# this file:
# 1) def function for sending messages (on exit of while true send notification message -- this will good to know if something weird happens)
# 2) def main function (re-run every ~1 sec)
  # read serial of Sensor node and write to measurands
    # a) open connection to mysql db
    # b) set up serial for arduino
    # c) open file to and start while loop
    # d) read serial from arduino from sensors
    # e) execute insert of tuples to db

# imports
import time
import datetime
import pymysql
import smtplib

def sendMessage(msg):
    # gmail credentials
    username = 'whitney.joel.b@gmail.com'
    password = 'Raptor5099'
    fromaddr = 'Joel Whitney'
    toaddrs  = '2072499538@txt.att.net'

    # The actual mail send. 'msg' can't have symbols, just plain text
    server = smtplib.SMTP('smtp.gmail.com:587')
    server.starttls()
    server.login(username,password)
    server.sendmail(fromaddr, toaddrs, str(msg))
    server.quit()

def main():
    # a) opens connection to mysql db and sets up insert statement
    # open a connection to the database
    cnx = pymysql.connect(host='108.167.160.69',
                          port=3306,
                          user='abconet1_joelw',
                          passwd='Raptor5099',
                          db='abconet1_GrowOperation')
    # SQL insert statement
    insert_observation_query = ("INSERT INTO measurands "
                                "(mID, type, value, insertTime) "
                                "VALUES (%s, %s, %s, %s);")
    # Sets up cursor object to interact with MYSQL connection
    cursor = cnx.cursor()

    # # b) set up pi serial connection
    # ser = serial.Serial('/dev/ttyUSB0', 9600, timeout=None) # Arduino
    # # clean out serial connection
    # ser.flushInput()
    time.sleep(2)
    # print header
    print("type, value, insertTime")

    # c) opens file to write results to and start reading from serial
    with open('arduinoOutput.txt', 'w') as f:
        while True:
            # set up fake data and response
            fakeType1 = 'temperature'
            fakeValue1 = str(random.uniform(75.0, 85.0))
            response1 = str(fakeType1) + ', ' + str(fakeValue1)
            # # d) read serial from sensors and set up response tuple
            # # create variable for each serial readline result
            # response = ser.readline().decode("ascii")
            # add current datetime for insertTime values
            response1 = response1[:-2] + "," + str(datetime.datetime.now())
            # print response
            print(response1)
            # write tuple to file
            tuple1 = str(response1)
            f.write(tuple1)
            # flush to make sure all writes are committed
            f.flush()
            # split response string using "," as delimiter
            split = tuple1.split(",")
            # assign the observation counter to id and value to value from the split string array
            mID1 = 'NULL'
            type1 = split[0]
            value1 = split[1]
            insertTime1 = split[2]
            # generate the query to insert the value into the SensorData table
            observation_data = (str(mID1), str(type1), str(value1), str(insertTime1))
            print("Query is: " + insert_observation_query.replace("%s", "{}").format(observation_data[0],
                                                                                     observation_data[1],
                                                                                     observation_data[2],
                                                                                     observation_data[3]))
            print("\n" + "*" * 80)
            # ping the connection before cursor execution so the connection is re-opened if it went idle in downtime
            cnx.ping()
            # use execute function on cursor and insert data from arduino
            cursor.execute(insert_observation_query, observation_data)
            # make sure data is committed to the database before looping through again
            cnx.commit()

            # set up fake data and response
            fakeType2 = 'humidity'
            fakeValue2 = str(random.uniform(20.0, 40.0))
            response2 = str(fakeType2) + ', ' + str(fakeValue2)
            # # d) read serial from sensors and set up response tuple
            # # create variable for each serial readline result
            # response = ser.readline().decode("ascii")
            # add current datetime for insertTime values
            response2 = response2[:-2] + "," + str(datetime.datetime.now())
            # print response
            print(response2)
            # write tuple to file
            tuple2 = str(response2)
            f.write(tuple2)
            # flush to make sure all writes are committed
            f.flush()
            # split response string using "," as delimiter
            split = tuple2.split(",")
            # assign the observation counter to id and value to value from the split string array
            mID2 = 'NULL'
            type2 = split[0]
            value2 = split[1]
            insertTime2 = split[2]
            # generate the query to insert the value into the SensorData table
            observation_data = (str(mID2), str(type2), str(value2), str(insertTime2))
            print("Query is: " + insert_observation_query.replace("%s", "{}").format(observation_data[0],
                                                                                     observation_data[1],
                                                                                     observation_data[2],
                                                                                     observation_data[3]))
            print("\n" + "*" * 80)
            # ping the connection before cursor execution so the connection is re-opened if it went idle in downtime
            cnx.ping()
            # use execute function on cursor and insert data from arduino
            cursor.execute(insert_observation_query, observation_data)
            # make sure data is committed to the database before looping through again
            cnx.commit()

            # set up fake data and response
            fakeType3 = 'moisture'
            fakeValue3 = str(random.uniform(10.0, 20.0))
            response3 = str(fakeType3) + ', ' + str(fakeValue3)
            # # d) read serial from sensors and set up response tuple
            # # create variable for each serial readline result
            # response = ser.readline().decode("ascii")
            # add current datetime for insertTime values
            response3 = response3[:-2] + "," + str(datetime.datetime.now())
            # print response
            print(response3)
            # write tuple to file
            tuple3 = str(response3)
            f.write(tuple3)
            # flush to make sure all writes are committed
            f.flush()
            # split response string using "," as delimiter
            split = tuple3.split(",")
            # assign the observation counter to id and value to value from the split string array
            mID3 = 'NULL'
            type3 = split[0]
            value3 = split[1]
            insertTime3 = split[2]
            # generate the query to insert the value into the SensorData table
            observation_data = (str(mID3), str(type3), str(value3), str(insertTime3))
            print("Query is: " + insert_observation_query.replace("%s", "{}").format(observation_data[0],
                                                                                     observation_data[1],
                                                                                     observation_data[2],
                                                                                     observation_data[3]))
            print("\n" + "*" * 80)
            # ping the connection before cursor execution so the connection is re-opened if it went idle in downtime
            cnx.ping()
            # use execute function on cursor and insert data from arduino
            cursor.execute(insert_observation_query, observation_data)
            # make sure data is committed to the database before looping through again
            cnx.commit()

            time.sleep(5)
    # close file, cursor, and connection when done writing
    f.close()
    cursor.close()
    cnx.close()

main()