<?php
include("config.php");
session_start();
// establishing the MySQLi connection
if($_SERVER["REQUEST_METHOD"] == "POST") {
    // username and password sent from form
    $user=mysqli_real_escape_string($db,$_POST['login']);
    $pass=mysqli_real_escape_string($db,$_POST['pass']);
    $sql="SELECT * FROM users WHERE username = '$user' and password = '$pass' ";
    $result = mysqli_query($db,$sql);
    //$row = mysqli_fetch_array($result);
    //$active = $row['active'];

    $count = mysqli_num_rows($result);

    // If result matched $myusername and $mypassword, table row must be 1 row
    if($count > 0) {
        $_SESSION['login_user'] = $user;

        header("location: welcome.php");
    }else {
        $error = "Your Login Name '$user' or Password '$pass' is invalid";
    }
}
?>

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
        <div style = "width:350px; border: solid 1px #333333; background-color: rgba(205, 205, 205, 0.71); " align = "left" >
            <div style = "background-color:#333333; color:#FFFFFF; padding:3px;"><strong>Login</strong></div>
            <div style = "margin:30px;">
                <form action = "" method = "post">
                    <span class="labelClass-login">User Name: </span><input type = "text" name = "login" class = "box" required="required"/><br/><br/>
                    <span class="labelClass-login">Password:</span><input type = "password" name = "pass" class = "box" required="required"/><br/><br />
                    <input type = "submit" name = "submit" value = " Submit "/><br />
                </form>
                <div style = "font-size:11px; color:#cc0000; margin-top:10px"><?php echo $error; ?></div>
            </div>
        </div>
      </div>
      </section>
    </div>
</div>
</body>
</html>
