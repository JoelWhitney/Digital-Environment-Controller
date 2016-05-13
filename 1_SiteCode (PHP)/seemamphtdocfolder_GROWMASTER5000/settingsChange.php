<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>GROWMASTER5000</title>
    <link href="BlogPostAssets/styles/blogPostStyle.css" rel="stylesheet" type="text/css">
    <link rel="stylesheet" href="index.css">
    <link rel="icon" type="image/png" href="images/leaficon.png" />
</head>
<body style="background-image:url(images/Greenhouse_at_night.jpg); background-size: cover; background; overflow:hidden; background-position:center">
<div id="mainwrapper">
    <div id="content">
        <section id="mainContent">
            <!--************************************************************************
            Main Blog content starts here
            ****************************************************************************-->
            <h1><!-- Blog title -->GROWMASTER5000</h1>
            <h3><!-- Tagline -->Digital Environment Controller</h3>
            <div class="vcenter"; align="center">
                <div style = "width:450px; border: solid 1px #333333; background-color: rgba(205, 205, 205, 0.71); " align = "left" >
                    <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><strong>Settings changed...</strong></div>
                    <div style = "margin:30px;">
                        <?php
                        include("config.php");
                        if($_SERVER["REQUEST_METHOD"] == "POST") {
                            $sensorPin = mysqli_real_escape_string($db, $_POST['sensorPin']);
                            $controlOutlet = mysqli_real_escape_string($db, $_POST['controlOutlet']);
                            $operator = mysqli_real_escape_string($db, $_POST['operator']);
                            $value = mysqli_real_escape_string($db, $_POST['value']);
                            $setting = mysqli_real_escape_string($db, $_POST['setting']);
                            $type = mysqli_real_escape_string($db, $_POST['type']);
                            $dt = new DateTime('America/New_York');
                            $insertTime = date_format($dt, 'Y-m-d H:i:s');

                            $query = "INSERT INTO settings (sensorPin,controlOutlet,operator,value,setting,insertTime,type) VALUES ('$sensorPin','$controlOutlet','$operator','$value','$setting','$insertTime','$type')";

                            $result = mysqli_query($db, $query) or die('Error querying database.' . mysqli_error());

                            mysqli_close($db);
                            echo "<div style='font-weight: bold; font-size: 15px; text-align: center '>The following settings were changed...</div><table style='font-size: 12px' align = 'center'>";
                            echo "<tr><th>Sensor Pin</th><th>Control Outlet</th><th>Operator</th><th>Value</th><th>Setting</th><th>Type</th><th>Insert Time</th></tr>";
                            echo "<tr><td style='font-size: 12px'>" . $sensorPin . "</td><td style='font-size: 12px'>" . $controlOutlet . "</td><td style='font-size: 12px'>" . $operator . "</td><td style='font-size: 12px'>" . $value . "</td><td style='font-size: 12px'>" . $setting . "</td><td style='font-size: 12px'>" . $type . "</td><td style='font-size: 12px'>" . $insertTime . "</td></tr>";
                            echo "</table>";
                            echo "<br /><a href=\"welcome.php\">Return to the main page</a>";
                        }
                        ?>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>
</body>
</html>
