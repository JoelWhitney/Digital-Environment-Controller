import pymysql

sqlSettings = 'SELECT * FROM settings AS m INNER JOIN (SELECT controlOutlet, MAX(insertTime) AS insertTime, operator, setting FROM settings GROUP BY controlOutlet, operator, setting) AS max ON m.controlOutlet = max.controlOutlet AND m.insertTime = max.insertTime ORDER BY m.controlOutlet, m.value ASC'

def getData(sqlStatement):
    # 1) opens connection to mysql db and sets up insert statement
    # open a connection to the database
    cnx = pymysql.connect(host='localhost',
                          port=8889,
                          user='joelw',
                          passwd='Raptor5099',
                          db='SIE557_GrowOps')
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
    cursor.execute(select_observation_query)
    # return data
    return cursor


measurands = getData(sqlSettings)

for row in measurands:
    print(row)