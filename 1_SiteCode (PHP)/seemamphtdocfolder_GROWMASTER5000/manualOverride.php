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
                    <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><strong>Manual override added...</strong></div>
                    <div style = "margin:30px;">
                        <?php
                        include("config.php");
                        if($_SERVER["REQUEST_METHOD"] == "POST") {
                            $controlOutlet = mysqli_real_escape_string($db, $_POST['controlOutlet']);
                            $startTime = mysqli_real_escape_string($db, $_POST['startTime']);
                            $endTime = mysqli_real_escape_string($db, $_POST['endTime']);
                            $setting = mysqli_real_escape_string($db, $_POST['setting']);
                            if ($setting == '0') {
                                $settingStr = 'On';
                            } else {
                                $settingStr = 'Off';
                            }
                            $type = mysqli_real_escape_string($db, $_POST['type']);
                            $dt = new DateTime('America/New_York');
                            $insertTime = date_format($dt, 'Y-m-d H:i:s');

                            $query = "INSERT INTO manual_overrides (controlOutlet,startTime,endTime,action,type,insertTime) VALUES ('$controlOutlet','$startTime','$endTime','$setting','$type','$insertTime')";

                            $result = mysqli_query($db, $query) or die('Error querying database.' . mysqli_error());

                            mysqli_close($db);
                            echo "<div style='font-weight: bold; font-size: 15px; text-align: center '>The following manual override was added...</div><table style='font-size: 12px' align = 'center'>";
                            echo "<tr><th>Control Outlet</th><th>Start Time</th><th>End Time</th><th>Setting</th><th>Type</th><th>Insert Time</th></tr>";
                            echo "<tr><td style='font-size: 12px'>" . $controlOutlet . "</td><td style='font-size: 12px'>" . $startTime . "</td><td style='font-size: 12px'>" . $endTime . "</td><td style='font-size: 12px'>" . $settingStr . "</td><td style='font-size: 12px'>" . $type . "</td><td style='font-size: 12px'>" . $insertTime . "</td></tr>";
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