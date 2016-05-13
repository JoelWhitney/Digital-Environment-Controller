from urllib.request import urlopen

__author__ = 'joelwhitney'
'''
(SIE557_Final_ControlPanelController.py)
 WORKFLOW
     -def main()
         start controller and reset settings (sendSignal() to reset settings)
         start loop
            read serial buffer and send data with sendData()
            create newOutSettings from testConditions()
            if newOutletSettings != outletSettings --> send new settings to cp with sendSignal()
               replace outletSettings with newOutletSettings

    -def getData(sqlStatement) -- takes sqlStatement and selects from db
      return data
    -def testConditions() -- check if any changes need to be made to outletSettings
      return outletSettings (as list - i.e. ['1', '1', '1', '1', '1', '1', '1', '1'])
    -def sendSignal() -- send outSettings to control panel
    -def sendData() -- listen to serial buffer and write to db
'''

import time
import datetime
import serial
import pymysql

# Get data from
def getData(sqlStatement):
    # 1) opens connection to mysql db and sets up insert statement
    # open a connection to the database
    # cnx = pymysql.connect(host='localhost',
    #                       port=8889,
    #                       user='root',
    #                       passwd='root',
    #                       db='SIE557_GrowOps')
    cnx = pymysql.connect(host='108.167.160.69',
                          port=3306,
                          user='abconet1_joelw',
                          passwd='Raptor5099',
                          db='abconet1_GROWMASTER5000')

    select_observation_query = sqlStatement # SQL select statement
    cursor = cnx.cursor() # Sets up cursor object to interact with MYSQL connection
    print("Query is: " + select_observation_query) # generate the query to select data from SensorData table
    print("\n" + "*" * 80)
    cnx.ping() # ping the connection before cursor execution so the connection is re-opened if it went idle in downtime
    cursor.execute(select_observation_query) # use execute function on cursor and select data from db
    return cursor # return data in cursor object

def testConditions(): # test conditions for changes in outletSettings
    outletSettings = ['1', '1', '1', '1', '1', '1', '1', '1']
    sqlSettings = 'SELECT * FROM settings AS m INNER JOIN (SELECT controlOutlet, MAX(insertTime) AS insertTime, operator, setting FROM settings GROUP BY controlOutlet, operator, setting) AS max ON m.controlOutlet = max.controlOutlet AND m.insertTime = max.insertTime ORDER BY m.controlOutlet, m.value ASC'
    sqlMeasurands = 'SELECT * FROM measurands AS m INNER JOIN (SELECT sensorPin,MAX(insertTime) AS insertTime FROM measurands GROUP BY sensorPin) AS max ON m.sensorPin = max.sensorPin AND m.insertTime = max.insertTime ORDER BY m.insertTime ASC'
    sqlOverrides = 'SELECT * FROM manual_overrides AS m INNER JOIN (SELECT controlOutlet,MAX(insertTime) AS insertTime FROM manual_overrides GROUP BY controlOutlet) AS max ON m.controlOutlet = max.controlOutlet AND m.insertTime = max.insertTime AND m.startTime <= DATE_ADD(NOW(), INTERVAL 1 HOUR) AND DATE_ADD(NOW(), INTERVAL 1 HOUR) < m.endTime '
    maxSettings = getData(sqlSettings) # gets data for the max settings
    maxMeasurands = getData(sqlMeasurands) # gets data for the max measurands
    maxOverrides = getData(sqlOverrides) # gets data for the max overrides
    for setting in maxSettings: # iterate over settings
        # get setting values
        s_sID, s_sensorPin, s_controlOutlet, s_operator, s_value, s_setting, s_insertTime, s_type  = setting[0], setting[1], setting[2], setting[3], setting[4], setting[5], setting[6], setting[7]
        now = datetime.datetime.now() # get current datetime
        doubleNow = now.hour + now.minute / 60 + now.second / 3600 # convert now time to a double
        # TEST LIGHTS
        if s_type == 'Lights' and s_operator == '>=' and doubleNow >= s_value: # greater than clause
            outletSettings[s_controlOutlet - 1] = str(s_setting)
        if s_type == 'Lights' and s_operator == '<='and doubleNow <= s_value: # less than clause
            outletSettings[s_controlOutlet - 1] = str(s_setting)
        # NOT LIGHTS
        if s_type != 'Lights':
            maxMeasurands.scroll(0, mode='absolute') # move measurand cursor back to start
            for measurand in maxMeasurands:
                # get measurand values
                m_mID, m_sensorPin, m_value, m_insertTime  = measurand[0], measurand[1], measurand[2], measurand[3]
                if s_operator == '>=' and s_sensorPin == m_sensorPin and m_value >= s_value: # greater than clause
                    outletSettings[s_controlOutlet - 1] = str(s_setting)
                elif s_operator == '<=' and s_sensorPin == m_sensorPin and m_value <= s_value: # less than clause
                    outletSettings[s_controlOutlet - 1] = str(s_setting)
    for override in maxOverrides: # iterate over overrides
        # get overrides values
        mo_moID, mo_controlOutlet, mo_startTime, mo_endTime, mo_action, mo_type, mo_insertTime  = override[0], override[1], override[2], override[3], override[4], override[5], override[6]
        outletSettings[mo_controlOutlet - 1] = str(mo_action)
    return outletSettings # return adjusted outletSettings

def sendSignal(ser, outletSettings): # send signal packet
    packet = ''
    for i in range(len(outletSettings)): # convert outletSettings list to string (packet = '11111lll\n')
        packet += outletSettings[i]
    packet += '\n'
    ser.write(packet.encode('ascii')) # sends packet of signals to serial

def sendData(ser): # send serial data to db
    # opens connection to mysql db and sets up insert statement
    # LOCAL
    # cnx = pymysql.connect(host='localhost',
    #                       port=8889,
    #                       user='root',
    #                       passwd='root',
    #                       db='SIE557_GrowOps')
    # EHOST
    cnx = pymysql.connect(host='108.167.160.69',
                          port=3306,
                          user='abconet1_joelw',
                          passwd='Raptor5099',
                          db='abconet1_GROWMASTER5000')
    # sets up cursor object to interact with MYSQL connection
    cursor = cnx.cursor()
    time.sleep(2)
    with open('serialOutput.txt', 'a') as f: # opens file to write results to and start reading from serial
        responses = ser.readlines() # get all lines from serial buffer
        if len(responses) > 0: # if responses is not empty
            for i in range(len(responses)): # for each line
                insert_observation_query = ''
                # split to type of response and values
                strResponse = responses[i].decode('ascii') # decode response
                strResponse = strResponse.split(":") # split to get type and values
                # this is here becuase I get some stupid out of range error sporadically - data is dropped if something dumb happens
                if len (strResponse) > 1:
                    type, values = strResponse[0], strResponse[1]
                    outResponse = values[:-2] + "," + str(datetime.datetime.now()) # generate out reponse
                    print('\n' + outResponse)
                    # do different stuff if action or measurand (i.e. response from cp versus sensor node)
                    if type == 'Action': # from cp
                        insert_observation_query = ("INSERT INTO actions " # SQL insert statement
                                                    "(controlOutlet, action, insertTime) "
                                                    "VALUES (%s, %s, %s);")
                        f.write(outResponse) # write tuple to file
                        f.flush() # flush to make sure all writes are committed
                        splitResponse = outResponse.split(",") # split to get values
                        # this is here becuase I get some stupid out of range error sporadically - data is dropped if something dumb happens
                        if len(splitResponse) > 2:
                            controlOutlet, value, insertTime = splitResponse[0], splitResponse[1], splitResponse[2]
                            observation_data = (str(controlOutlet), str(value), str(insertTime)) # create observation data list
                            print("SQL is: " + insert_observation_query.replace("%s", "{}").format(observation_data[0],
                                                                                                   observation_data[1],
                                                                                                   observation_data[2]))
                            print("\n" + "*" * 80)
                            cnx.ping()  # ping the connection before cursor execution so the connection is re-opened if it went idle in downtime
                            cursor.execute(insert_observation_query, observation_data)  # use execute function on cursor and insert data from arduino
                            cnx.commit()  # make sure data is committed to the database before looping through again
                    elif type == 'Measurand': # from sensor node
                        insert_observation_query = ("INSERT INTO measurands " # SQL insert statement
                                                    "(sensorPin, value, insertTime) "
                                                    "VALUES (%s, %s, %s);")
                        f.write(outResponse) # write tuple to file
                        f.flush() # flush to make sure all writes are committed
                        splitResponse = outResponse.split(",") # split to get values
                        # this is here becuase I get some stupid out of range error sporadically - data is dropped if something dumb happens
                        if len(splitResponse) > 2:
                            sensorPin, value, insertTime = str(splitResponse[0]), str(splitResponse[1]), str(splitResponse[2])
                            observation_data = (str(sensorPin), str(value), str(insertTime)) # create observation data list
                            print("SQL is: " + insert_observation_query.replace("%s", "{}").format(observation_data[0],
                                                                                                   observation_data[1],
                                                                                                   observation_data[2]))
                            print("\n" + "*" * 80)
                            cnx.ping()  # ping the connection before cursor execution so the connection is re-opened if it went idle in downtime
                            cursor.execute(insert_observation_query, observation_data)  # use execute function on cursor and insert data from arduino
                            cnx.commit()  # make sure data is committed to the database before looping through again
    # close file, cursor, and connection when done writing
    cursor.close()
    cnx.close()

# check internet connection
def internet_on():
    try:
        urlopen("http://google.com")
        return True
    except:
        return False

def main():
    ser = serial.Serial('/dev/ttyUSB0', 9600, timeout= 0) # Pi
    # ser = serial.Serial('COM12', 9600, timeout = 0) # Windows
    outletSettings = ['1', '1', '1', '1', '1', '1', '1', '1']
    sendSignal(ser, outletSettings)
    with open('cpControl.txt', 'w') as fi:
        while True:
            if internet_on() == True:
                sendData(ser) # read serial buffer and write to db
                newOutletSettings = testConditions() # test conditions for changes to controlOutlets
                if outletSettings != newOutletSettings: # if settings are different
                    print('CHANGES DETECTED!!!   ' + str(outletSettings) + ' --> ' + str(newOutletSettings))
                    sendSignal(ser, newOutletSettings) # send new settings to cp
                    outletSettings = newOutletSettings # overwrite old settings with new settings
                time.sleep(3)
    fi.close() # close file

# call main function
main()