<?php
include("session.php");
include("config.php");

// establishing the MySQLi connection
$sql="SELECT maxMeas.sensorPin AS sensorPin, maxMeas.value AS measValue, oControlOutlet AS controlOutlet, ooperator AS onOperator, oValue AS onValue, operator AS offOperator, combSettings.value AS offValue, type AS type FROM (
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
                                      SELECT sensorPin, MAX(insertTime) AS insertTime, operator, setting
                                      FROM settings
                                      WHERE setting = '1'
                                      GROUP BY sensorPin, setting) AS offSettMaxTime
          ON offS.sensorPin = offSettMaxTime.sensorPin AND offS.insertTime = offSettMaxTime.insertTime
          ORDER BY offS.sensorPin, offS.value ASC) AS offSettings
  ON oSensorPin = offSettings.sensorPin ) AS combSettings
ON  combSettings.sensorPin = maxMeas.sensorPin
ORDER BY oControlOutlet";
$measurandSettings = mysqli_query($db,$sql) or die(mysqli_error());

$dt = new DateTime('America/New_York');
$hour = $dt->format('H');
$min = $dt->format('i') / 60;
$sec = $dt->format('s') / 3600;
$doubleNow = $hour + $min + $sec;
$doubleNow = number_format($doubleNow, 2, '.', '');
$count = mysqli_num_rows($measurandSettings);
mysqli_close($db);
?>

<!doctype html>
<!--Quite a few clients strip your Doctype out, and some even apply their own. Many clients do honor your doctype and it can make things much easier if you can validate constantly against a Doctype.-->
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<meta http-equiv="refresh" content="60"> <!-- Refresh every minute -->
    <link rel="icon" type="image/png" href="images/leaficon.png" />
<title>GROWMASTER5000</title>

<!-- Please use an inliner tool to convert all CSS to inline as inpage or external CSS is removed by email clients -->
<!-- important in CSS is used to prevent the styles of currently inline CSS from overriding the ones mentioned in media queries when corresponding screen sizes are encountered -->

<style type="text/css">
h1 {
	color: whitesmoke;
	text-align: center;
	font-size: 20px;
}
h2 {
	color: white;
	text-align: left;
	font-size: 24px;
}
h3 {
	color: rgba(218, 218, 218, 0.95);
	text-align: center;
	font-size: 18px;
}
a {
	text-align: left;
	color: #999999 ;
	font-family: "Times New Roman";
	font-size: 18px;
}
b {
    text-align: left;
    color: #000000 ;
    font-size: 14px;
}
body {
	margin: 0;
}
body, table, td, p, a, li, blockquote {
	-webkit-text-size-adjust: none!important;
	font-family: sans-serif;
	font-style: normal;
	font-weight: 400;
}
button {
	width: 90%;
}

@media screen and (max-width:600px) {
/*styling for objects with screen size less than 600px; */
body, table, td, p, a, li, blockquote {
	-webkit-text-size-adjust: none!important;
	font-family: sans-serif;
}
table {
	/* All tables are 100% width */
	width: 100%;
}
.footer {
	/* Footer has 2 columns each of 48% width */
	height: auto !important;
	max-width: 48% !important;
	width: 48% !important;
}
table.responsiveImage {
	/* Container for images in catalog */
	height: auto !important;
	max-width: 30% !important;
	width: 30% !important;
}
table.responsiveContent {
	/* Content that accompanies the content in the catalog */
	height: auto !important;
	max-width: 66% !important;
	width: 66% !important;
}
.top {
	/* Each Columnar table in the header */
	height: auto !important;
	max-width: 48% !important;
	width: 48% !important;
}
.catalog {
	margin-left: 0%!important;
}
}

@media screen and (max-width:480px) {
/*styling for objects with screen size less than 480px; */
body, table, td, p, a, li, blockquote {
	-webkit-text-size-adjust: none!important;
	font-family: sans-serif;
}
table {
	/* All tables are 100% width */
	width: 100% !important;
	border-style: none !important;
}
.footer {
	/* Each footer column in this case should occupy 96% width  and 4% is allowed for email client padding*/
	height: auto !important;
	max-width: 96% !important;
	width: 96% !important;
}
.table.responsiveImage {
	/* Container for each image now specifying full width */
	height: auto !important;
	max-width: 96% !important;
	width: 96% !important;
}
.table.responsiveContent {
	/* Content in catalog  occupying full width of cell */
	height: auto !important;
	max-width: 96% !important;
	width: 96% !important;
}
.top {
	/* Header columns occupying full width */
	height: auto !important;
	max-width: 100% !important;
	width: 100% !important;
}
.catalog {
	margin-left: 0%!important;
}
button {
	width: 90%!important;
}
}
</style>
</head>
<body style="background-image:url(images/Greenhouse_at_night.jpg); background-size: cover; background; overflow:auto; background-position:center">

<a class="hello">Welcome,   '<em><?php echo $user_check;?>'!</em></a>
<a href="logout.php">Logout of session</a>
<div><button style="width: 100px" onclick="reload()">Refresh Page</button></div>
<script>
    function reload() {
        location.reload();
    }
</script>
<h1>GROWMASTER5000</h1>
<h3>Digital Environment Controller</h3>
      <table width="90%"  align="center" cellpadding="5" cellspacing="5">
          <!-- Main Wrapper Table with initial width set to 60opx -->
          <tbody>
            <tr>
              <td><table cellpadding="0" cellspacing="0" align="center" width="100%" class="catalog">
                  <!-- Table for catalog -->
                  <tr>
                    <td >
                    <!-- ************************************************* START OF MAJOR CELL 1 ****************************************************** -->
                    <!-- ************************************************* START OF MAJOR CELL 1 ****************************************************** -->
                    <!--// SETTINGS TABLE-->
                     <table width="50%" height="280" align="left" cellpadding="0" border="4"  cellspacing="0" class ="responsive-table" style="margin: 0px 0px 0px 0px;">
                         <caption style="color: #999999; font-weight: bold">Current Settings & Measurands</caption>
                        <!-- Table container for each image and description in catalog -->
                        <tbody>
                          <tr>
                             <td><table width="100%" height="280" border="2" align="left" cellpadding="0"  cellspacing="0" class="table.responsiveImage" style="background-color: rgba(205, 205, 205, 0.71)">
                                <!-- Table container for image -->
                                <tbody>
                                    <tr>
                                        <?php
                                        mysqli_data_seek($measurandSettings, 0);
                                        $row = mysqli_fetch_row($measurandSettings);
                                        $sensorPin = $row[0];
                                        $measValue = $row[1];
                                        $controlOutlet = $row[2];
                                        $onOperator = $row[3];
                                        $onValue = $row[4];
                                        $offOperator = $row[5];
                                        $offValue = $row[6];
                                        $type = $row[7];
                                        if ($type == 'Lights') {
                                            $measValue =$doubleNow;
                                        }
                                        ?>
                                        <td align="center" style="padding:0px 0px 0px 0px; height: 50%;"><table width="100%" height="80%" border="0">
                                        <caption><div style="color: #000000; font-size: 14px; font-weight: bold"><?php echo $type; ?></div><div style="font-size: 12px"> <?php echo ' (cOutlet: '.$controlOutlet.')'; ?></div></caption>
                                          <tbody>
                                            <tr>
                                              <td><div style="color: #BD2E30; font-weight:bold; font-size:14px; text-align:center"><?php echo $measValue; ?></div><div style="color: #BD2E30; font-size:12px; text-align:center"><?php echo ' (sPin: '.$sensorPin.')'; ?></div></td>
                                            </tr>
                                            <tr>
                                              <td><table width="100%" height="100%" border="0">
                                                <tbody>
                                                  <tr>
                                                    <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $onOperator.' '.$onValue; ?></div><div style="font-size: 12px">(ON Value)</div></td>
                                                    <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $offOperator.' '.$offValue; ?></div><div style="font-size: 12px">(OFF Value)</div></td>
                                                  </tr>
                                                </tbody>
                                              </table></td>
                                            </tr>
                                          </tbody>
                                        </table></td>
                                        <?php
                                        mysqli_data_seek($measurandSettings, 1);
                                        $row = mysqli_fetch_row($measurandSettings);
                                        $sensorPin = $row[0];
                                        $measValue = $row[1];
                                        $controlOutlet = $row[2];
                                        $onOperator = $row[3];
                                        $onValue = $row[4];
                                        $offOperator = $row[5];
                                        $offValue = $row[6];
                                        $type = $row[7];
                                        if ($type == 'Lights') {
                                            $measValue =$doubleNow;
                                        }
                                        ?>
                                        <td align="center" style="padding:0px 0px 0px 0px; height: 50%;"><table width="100%" height="80%" border="0">
                                                <caption><div style="color: #000000; font-size: 14px; font-weight: bold"><?php echo $type; ?></div><div style="font-size: 12px"> <?php echo ' (cOutlet: '.$controlOutlet.')'; ?></div></caption>
                                                <tbody>
                                                <tr>
                                                    <td><div style="color: #BD2E30; font-weight:bold; font-size:14px; text-align:center"><?php echo $measValue; ?></div><div style="color: #BD2E30; font-size:12px; text-align:center"><?php echo ' (sPin: '.$sensorPin.')'; ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td><table width="100%" height="100%" border="0">
                                                            <tbody>
                                                            <tr>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $onOperator.' '.$onValue; ?></div><div style="font-size: 12px">(ON Value)</div></td>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $offOperator.' '.$offValue; ?></div><div style="font-size: 12px">(OFF Value)</div></td>
                                                            </tr>
                                                            </tbody>
                                                        </table></td>
                                                </tr>
                                                </tbody>
                                            </table></td>
                                        <?php
                                        mysqli_data_seek($measurandSettings, 2);
                                        $row = mysqli_fetch_row($measurandSettings);
                                        $sensorPin = $row[0];
                                        $measValue = $row[1];
                                        $controlOutlet = $row[2];
                                        $onOperator = $row[3];
                                        $onValue = $row[4];
                                        $offOperator = $row[5];
                                        $offValue = $row[6];
                                        $type = $row[7];
                                        if ($type == 'Lights') {
                                            $measValue =$doubleNow;
                                        }
                                        ?>
                                        <td align="center" style="padding:0px 0px 0px 0px; height: 50%;"><table width="100%" height="80%" border="0">
                                                <caption><div style="color: #000000; font-size: 14px; font-weight: bold"><?php echo $type; ?></div><div style="font-size: 12px"> <?php echo ' (cOutlet: '.$controlOutlet.')'; ?></div></caption>
                                                <tbody>
                                                <tr>
                                                    <td><div style="color: #BD2E30; font-weight:bold; font-size:14px; text-align:center"><?php echo $measValue; ?></div><div style="color: #BD2E30; font-size:12px; text-align:center"><?php echo ' (sPin: '.$sensorPin.')'; ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td><table width="100%" height="100%" border="0">
                                                            <tbody>
                                                            <tr>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $onOperator.' '.$onValue; ?></div><div style="font-size: 12px">(ON Value)</div></td>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $offOperator.' '.$offValue; ?></div><div style="font-size: 12px">(OFF Value)</div></td>
                                                            </tr>
                                                            </tbody>
                                                        </table></td>
                                                </tr>
                                                </tbody>
                                            </table></td>
                                        <?php
                                        mysqli_data_seek($measurandSettings, 3);
                                        $row = mysqli_fetch_row($measurandSettings);
                                        $sensorPin = $row[0];
                                        $measValue = $row[1];
                                        $controlOutlet = $row[2];
                                        $onOperator = $row[3];
                                        $onValue = $row[4];
                                        $offOperator = $row[5];
                                        $offValue = $row[6];
                                        $type = $row[7];
                                        if ($type == 'Lights') {
                                            $measValue =$doubleNow;
                                        }
                                        ?>
                                        <td align="center" style="padding:0px 0px 0px 0px; height: 50%;"><table width="100%" height="80%" border="0">
                                                <caption><div style="color: #000000; font-size: 14px; font-weight: bold"><?php echo $type; ?></div><div style="font-size: 12px"> <?php echo ' (cOutlet: '.$controlOutlet.')'; ?></div></caption>
                                                <tbody>
                                                <tr>
                                                    <td><div style="color: #BD2E30; font-weight:bold; font-size:14px; text-align:center"><?php echo $measValue; ?></div><div style="color: #BD2E30; font-size:12px; text-align:center"><?php echo ' (sPin: '.$sensorPin.')'; ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td><table width="100%" height="100%" border="0">
                                                            <tbody>
                                                            <tr>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $onOperator.' '.$onValue; ?></div><div style="font-size: 12px">(ON Value)</div></td>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $offOperator.' '.$offValue; ?></div><div style="font-size: 12px">(OFF Value)</div></td>
                                                            </tr>
                                                            </tbody>
                                                        </table></td>
                                                </tr>
                                                </tbody>
                                            </table></td>
                                    </tr>
                                    <!--//start of second row in 1st cell-->
                                    <tr>
                                        <?php
                                        mysqli_data_seek($measurandSettings, 4);
                                        $row = mysqli_fetch_row($measurandSettings);
                                        $sensorPin = $row[0];
                                        $measValue = $row[1];
                                        $controlOutlet = $row[2];
                                        $onOperator = $row[3];
                                        $onValue = $row[4];
                                        $offOperator = $row[5];
                                        $offValue = $row[6];
                                        $type = $row[7];
                                        if ($type == 'Lights') {
                                            $measValue =$doubleNow;
                                        }
                                        ?>
                                        <td align="center" style="padding:0px 0px 0px 0px; height: 50%;"><table width="100%" height="80%" border="0">
                                                <caption><div style="color: #000000; font-size: 14px; font-weight: bold"><?php echo $type; ?></div><div style="font-size: 12px"> <?php echo ' (cOutlet: '.$controlOutlet.')'; ?></div></caption>
                                                <tbody>
                                                <tr>
                                                    <td><div style="color: #BD2E30; font-weight:bold; font-size:14px; text-align:center"><?php echo $measValue; ?></div><div style="color: #BD2E30; font-size:12px; text-align:center"><?php echo ' (sPin: '.$sensorPin.')'; ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td><table width="100%" height="100%" border="0">
                                                            <tbody>
                                                            <tr>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $onOperator.' '.$onValue; ?></div><div style="font-size: 12px">(ON Value)</div></td>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $offOperator.' '.$offValue; ?></div><div style="font-size: 12px">(OFF Value)</div></td>
                                                            </tr>
                                                            </tbody>
                                                        </table></td>
                                                </tr>
                                                </tbody>
                                            </table></td>
                                        <?php
                                        mysqli_data_seek($measurandSettings, 5);
                                        $row = mysqli_fetch_row($measurandSettings);
                                        $sensorPin = $row[0];
                                        $measValue = $row[1];
                                        $controlOutlet = $row[2];
                                        $onOperator = $row[3];
                                        $onValue = $row[4];
                                        $offOperator = $row[5];
                                        $offValue = $row[6];
                                        $type = $row[7];
                                        if ($type == 'Lights') {
                                            $measValue =$doubleNow;
                                        }
                                        ?>
                                        <td align="center" style="padding:0px 0px 0px 0px; height: 50%;"><table width="100%" height="80%" border="0">
                                                <caption><div style="color: #000000; font-size: 14px; font-weight: bold"><?php echo $type; ?></div><div style="font-size: 12px"> <?php echo ' (cOutlet: '.$controlOutlet.')'; ?></div></caption>
                                                <tbody>
                                                <tr>
                                                    <td><div style="color: #BD2E30; font-weight:bold; font-size:14px; text-align:center"><?php echo $measValue; ?></div><div style="color: #BD2E30; font-size:12px; text-align:center"><?php echo ' (sPin: '.$sensorPin.')'; ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td><table width="100%" height="100%" border="0">
                                                            <tbody>
                                                            <tr>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $onOperator.' '.$onValue; ?></div><div style="font-size: 12px">(ON Value)</div></td>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $offOperator.' '.$offValue; ?></div><div style="font-size: 12px">(OFF Value)</div></td>
                                                            </tr>
                                                            </tbody>
                                                        </table></td>
                                                </tr>
                                                </tbody>
                                            </table></td>
                                        <?php
                                        mysqli_data_seek($measurandSettings, 6);
                                        $row = mysqli_fetch_row($measurandSettings);
                                        $sensorPin = $row[0];
                                        $measValue = $row[1];
                                        $controlOutlet = $row[2];
                                        $onOperator = $row[3];
                                        $onValue = $row[4];
                                        $offOperator = $row[5];
                                        $offValue = $row[6];
                                        $type = $row[7];
                                        if ($type == 'Lights') {
                                            $measValue =$doubleNow;
                                        }
                                        ?>
                                        <td align="center" style="padding:0px 0px 0px 0px; height: 50%;"><table width="100%" height="80%" border="0">
                                                <caption><div style="color: #000000; font-size: 14px; font-weight: bold"><?php echo $type; ?></div><div style="font-size: 12px"> <?php echo ' (cOutlet: '.$controlOutlet.')'; ?></div></caption>
                                                <tbody>
                                                <tr>
                                                    <td><div style="color: #BD2E30; font-weight:bold; font-size:14px; text-align:center"><?php echo $measValue; ?></div><div style="color: #BD2E30; font-size:12px; text-align:center"><?php echo ' (sPin: '.$sensorPin.')'; ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td><table width="100%" height="100%" border="0">
                                                            <tbody>
                                                            <tr>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $onOperator.' '.$onValue; ?></div><div style="font-size: 12px">(ON Value)</div></td>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $offOperator.' '.$offValue; ?></div><div style="font-size: 12px">(OFF Value)</div></td>
                                                            </tr>
                                                            </tbody>
                                                        </table></td>
                                                </tr>
                                                </tbody>
                                            </table></td>
                                        <?php
                                        mysqli_data_seek($measurandSettings, 7);
                                        $row = mysqli_fetch_row($measurandSettings);
                                        $sensorPin = $row[0];
                                        $measValue = $row[1];
                                        $controlOutlet = $row[2];
                                        $onOperator = $row[3];
                                        $onValue = $row[4];
                                        $offOperator = $row[5];
                                        $offValue = $row[6];
                                        $type = $row[7];
                                        if ($type == 'Lights') {
                                            $measValue =$doubleNow;
                                        }
                                        ?>
                                        <td align="center" style="padding:0px 0px 0px 0px; height: 50%;"><table width="100%" height="80%" border="0">
                                                <caption><div style="color: #000000; font-size: 14px; font-weight: bold"><?php echo $type; ?></div><div style="font-size: 12px"> <?php echo ' (cOutlet: '.$controlOutlet.')'; ?></div></caption>
                                                <tbody>
                                                <tr>
                                                    <td><div style="color: #BD2E30; font-weight:bold; font-size:14px; text-align:center"><?php echo $measValue; ?></div><div style="color: #BD2E30; font-size:12px; text-align:center"><?php echo ' (sPin: '.$sensorPin.')'; ?></div></td>
                                                </tr>
                                                <tr>
                                                    <td><table width="100%" height="100%" border="0">
                                                            <tbody>
                                                            <tr>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $onOperator.' '.$onValue; ?></div><div style="font-size: 12px">(ON Value)</div></td>
                                                                <td style="text-align:center"><div style="font-size: 14px; font-weight:bold"><?php echo $offOperator.' '.$offValue; ?></div><div style="font-size: 12px">(OFF Value)</div></td>
                                                            </tr>
                                                            </tbody>
                                                        </table></td>
                                                </tr>
                                                </tbody>
                                            </table></td>
								</tbody>
                              </table></td>
                          </tr>
                        </tbody>
                      </table>
                      <!-- ************************************************* START OF MAJOR CELL 2 ****************************************************** -->
                      <!-- ************************************************* START OF MAJOR CELL 2 ****************************************************** -->
                      <table class ="responsive-table" width="50%" border="4" cellspacing="0" cellpadding="0" align="left" style="margin: 0px 0px 0px 0px;">
                          <caption style="color: #999999; font-weight: bold">Add Settings & Manual Overrides</caption>
                        <!-- Table container for each image and description in catalog -->
                        <tbody>
                          <tr>
                            <td><table width="100%" height="280" border="2" align="left" cellpadding="0"  cellspacing="0" class="table.responsiveImage" style="background-color: rgba(205, 205, 205, 0.71)">
                                    <col width="50%">
                                    <col width="50%">
                                <!-- Table container for image -->
                                <tbody>
                                  <tr>
                                    <td align="left" valign="top" style="padding:10px 10px 10px 10px;">
                                        <div style="font-weight: bold; font-size: 15px; text-align: center ">Add New Setting</div>
                                        <form method="post" action="settingsChange.php">
                                            <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Sensor Pin (i.e. 3-15): </span><input type="text" name="sensorPin" size="5" required="required"/><br />
                                            <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Control Outlet (i.e. 1-8): </span><input type="text" name="controlOutlet" size="5" required="required"/><br />
                                            <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Operator: </span><select name="operator">
                                                <option value=">=">>=</option>
                                                <option value="<="><=</option></select><br/>
                                            <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Value: </span><input type="text" name="value" size="5" required="required"/><br />
                                            <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Setting: </span><select name="setting">
                                                <option value="0">On</option>
                                                <option value="1">Off</option></select><br/>
                                            <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Type: </span><select name="type">
                                                <option value="Lights">Lights</option>
                                                <option value="CO2">CO2</option>
                                                <option value="Temperature">Temperature</option>
                                                <option value="pH">pH</option>
                                                <option value="Humidity">Humidity</option>
                                                <option value="Moisture">Moisture</option>
                                                <option value="Moisture">Other</option></select><br/><br/>
                                            <input align="center" type="submit" value="Submit new setting" name="submitSetting" /> <br /> <br/>
                                        </form>
                                    </td>
                                    <td align="left" valign="top" style="padding:10px 10px 10px 10px;">
                                        <div style="font-weight: bold; font-size: 15px; text-align: center ">Add New Manual Override</div>
                                        <div>
                                            <form method="post" action="manualOverride.php">
                                                <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Control Outlet (i.e. 1-8): </span><input type="text" name="controlOutlet" size="5" required="required"/><br />
                                                <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Start time: </span><input type="datetime-local" name="startTime" size="10" required="required"/><br />
                                                <span class="labelClass-login" style="font-weight: bold; font-size: 12px">End time: </span><input type="datetime-local" name="endTime" size="10" required="required"/><br />
                                                <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Setting: </span><select name="setting">
                                                    <option value="0">On</option>
                                                    <option value="1">Off</option></select><br/>
                                                <span class="labelClass-login" style="font-weight: bold; font-size: 12px">Type: </span><select name="type">
                                                    <option value="Lights">Lights</option>
                                                    <option value="CO2">CO2</option>
                                                    <option value="Temperature">Temperature</option>
                                                    <option value="pH">pH</option>
                                                    <option value="Humidity">Humidity</option>
                                                    <option value="Moisture">Moisture</option>
                                                    <option value="Moisture">Other</option></select><br/><br/>
                                                <input align="center" type="submit" value="Submit manual override" name="submitOverride" /> <br /><br />
                                            </form>
                                            <!--// OVERRIDE TABLE-->
                                            <?php
                                            require('config.php');
                                            $now = new DateTime('America/New_York');
                                            $nowStr = date_format($now, 'Y-m-d H:i:s');
                                            $overrideSql="SELECT * FROM manual_overrides AS m INNER JOIN (SELECT controlOutlet,MAX(insertTime) AS insertTime FROM manual_overrides GROUP BY controlOutlet) AS max ON m.controlOutlet = max.controlOutlet AND m.insertTime = max.insertTime AND m.startTime <= '$nowStr' AND '$nowStr' < m.endTime ";
                                            $manualOverrides = mysqli_query($db,$overrideSql) or die(mysqli_error());
                                            echo "<div style='font-weight: bold; font-size: 15px; text-align: center '>Active Manual Overrides</div><table style='font-size: 12px' align = 'left'>";
                                            $count = mysqli_num_rows($manualOverrides);
                                            if ($count > 0) {
                                                echo "<tr><th>Control Outlet</th><th>Start Time</th><th>End Time</th><th>Setting</th><th>Type</th></tr>";
                                                while ($row = mysqli_fetch_array($manualOverrides)) {
                                                    $controlOutlet = $row['controlOutlet'];
                                                    $startTime = $row['startTime'];
                                                    $endTime = $row['endTime'];
                                                    $setting = $row['action'];
                                                    if ($setting == '0') {
                                                        $setting = 'On';
                                                    } else {
                                                        $setting = 'Off';
                                                    }
                                                    $type = $row['type'];
                                                    echo "<tr><td style='font-size: 12px'>" . $controlOutlet . "</td><td style='font-size: 12px'>" . $startTime . "</td><td style='font-size: 12px'>" . $endTime . "</td><td style='font-size: 12px'>" . $setting . "</td><td style='font-size: 12px'>" . $type . "</td></tr>";
                                                }
                                            } else {
                                                echo "<div style='color: #BD2E30'>There are no active overrides...</div>";
                                            }
                                            echo "</table>";
                                            mysqli_close($db);
                                            ?>
                                        </div>
                                      </td>
                                  </tr>
                                </tbody>
                              </table></td>
                          </tr>
                        </tbody>
                      </table></td>
                  </tr>
                  <tr>
                      <td>
                      <!-- ************************************************* START OF MAJOR CELL 3 ****************************************************** -->
                      <!-- ************************************************* START OF MAJOR CELL 3 ****************************************************** -->
                      <table class ="responsive-table" width="50%" border="4" cellspacing="0" cellpadding="0" align="left" style="margin: 0px 0px 0px 0px;">
                          <caption style="color: #999999; font-weight: bold">Streaming Video Feed</caption>
                          <!-- Table container for each image and description in catalog -->
                          <tbody>
                          <tr>
                              <td><table width="100%" height="280" border="2" align="left" cellpadding="0"  cellspacing="0" class="table.responsiveImage" style="background-color: rgba(205, 205, 205, 0.71)">
                                      <!-- Table container for image -->
                                      <tbody>
                                      <tr>
                                          <td colspan="2" valign="top" align="center">
                                              <div style="padding:10px 10px 10px 10px;" align="center">
                                                  <img style="display:block; max-height: 220px" align="center" src="http://joel-whitney.ddns.net:60000/?action=stream">
                                                  <input align="left" type="button" value="Open in new window"
                                                         onclick="window.open('http://joel-whitney.ddns.net:60000/?action=stream')">
                                              </div>
                                          </td>
                                      </tr>
                                      </tbody>
                                  </table></td>
                          </tr>
                          </tbody>
                      </table></td>
                  </tr>
                </table></td>
            </tr>
          </tbody>
        </table></td>
    </tr>
  </tbody>
</table>
</body>
</html>
