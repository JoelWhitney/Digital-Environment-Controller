__author__ = 'joelwhitney'
# SIE 557 - Final - Read database for displaying


# Get data from
def getData(sqlStatement):
    # 1) opens connection to mysql db and sets up insert statement
    # open a connection to the database
    cnx = pymysql.connect(host='localhost',
                          port=8889,
                          user='joelw',
                          passwd='Raptor5099',
                          db='SIE557')
    # SQL select statement
    select_observation_query = sqlStatement
    # Sets up cursor object to interact with MYSQL connection
    cursor = cnx.cursor()
    # generate the query to insert the value into the SensorData table
    print("Query is: " + select_observation_query)
    print("\n" + "*" * 80)
    # ping the connection before cursor execution so the connection is re-opened if it went idle in downtime
    cnx.ping()
    # use execute function on cursor and insert data from arduino
    data = cursor.execute(select_observation_query)
    # make sure data is committed to the database before looping through again
    cnx.commit()
    # close file, cursor, and connection when done writing
    cursor.close()
    cnx.close()
    # return data
    return data

def displayData():
    while True:
        sqlSettings = 'SELECT * FROM settings AS m INNER JOIN (SELECT sensorPin,MAX(insertTime) AS insertTime, operator FROM settings GROUP BY sensorPin, operator) AS max ON m.sensorPin = max.sensorPin AND m.insertTime = max.insertTime'
        sqlMeasurands = 'SELECT * FROM measurands AS m INNER JOIN (SELECT sensorPin,MAX(insertTime) AS insertTime FROM measurands GROUP BY sensorPin) AS max ON m.sensorPin = max.sensorPin AND m.insertTime = max.insertTime'
        sqlOverrides = 'SELECT * FROM manual_overrides AS m INNER JOIN (SELECT controlOutlet,MAX(insertTime) AS insertTime FROM manual_overrides GROUP BY controlOutlet) AS max ON m.controlOutlet = max.controlOutlet AND m.insertTime = max.insertTime'
        maxSettings = getData(sqlSettings)
        maxMeasurands = getData(sqlMeasurands)
        maxOverrides = getData(sqlOverrides)


displayData()