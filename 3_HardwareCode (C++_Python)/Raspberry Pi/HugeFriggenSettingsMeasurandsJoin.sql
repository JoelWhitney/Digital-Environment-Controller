USE SIE557_GrowOps;

SELECT maxMeas.sensorPin AS sensorPin, maxMeas.value AS measValue, oControlOutlet AS controlOutlet, ooperator AS onOperator, oValue AS onValue, operator AS offOperator, combSettings.value AS offValue, type AS type FROM (
  SELECT meas.mID, meas.sensorPin, meas.value, meas.insertTime
  FROM measurands AS meas INNER JOIN (
                                    SELECT sensorPin,MAX(insertTime) AS insertTime
                                    FROM measurands
                                    GROUP BY sensorPin) AS measMaxTime
      ON meas.sensorPin = measMaxTime.sensorPin AND meas.insertTime = measMaxTime.insertTime) AS maxMeas LEFT JOIN (

  SELECT * FROM (

    SELECT onS.sID AS osID, onS.sensorPin AS oSensorPin, onS.controlOutlet AS oControlOutlet, onS.operator AS ooperator, onS.value AS oValue, onS.setting AS oSetting, onS.insertTime AS oInsertTime, onS.type AS oType
    FROM settings AS onS INNER JOIN (
                                    SELECT sensorPin, MAX(insertTime) AS insertTime, operator, setting
                                    FROM settings
                                    WHERE setting = '0'
                                    GROUP BY sensorPin, setting) AS onSettMaxTime
        ON onS.sensorPin = onSettMaxTime.sensorPin AND onS.insertTime = onSettMaxTime.insertTime
        ORDER BY onS.sensorPin, onS.value ASC) AS onSettings INNER JOIN (

      SELECT offS.sID, offS.sensorPin, offS.controlOutlet, offS.operator, offS.value, offS.setting, offS.insertTime, offS.type
      FROM settings AS offS INNER JOIN (
                                      SELECT sensorPin, MIN(insertTime) AS insertTime, operator, setting
                                      FROM settings
                                      WHERE setting = '1'
                                      GROUP BY sensorPin, setting) AS offSettMaxTime
          ON offS.sensorPin = offSettMaxTime.sensorPin AND offS.insertTime = offSettMaxTime.insertTime
          ORDER BY offS.sensorPin, offS.value ASC) AS offSettings
  ON oSensorPin = offSettings.sensorPin ) AS combSettings

ON  combSettings.sensorPin = maxMeas.sensorPin
ORDER BY oControlOutlet;

