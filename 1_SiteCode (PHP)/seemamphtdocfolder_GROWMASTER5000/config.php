<?php
//define('DB_SERVER', 'localhost');
//define('DB_PORT', '8889');
//define('DB_USERNAME', 'root');
//define('DB_PASSWORD', 'root');
//define('DB_DATABASE', 'SIE557_GrowOps');

define('DB_SERVER', '108.167.160.69');
define('DB_PORT', '3306');
define('DB_USERNAME', 'abconet1_joelw');
define('DB_PASSWORD', 'Raptor5099');
define('DB_DATABASE', 'abconet1_GROWMASTER5000');

$db = mysqli_connect(DB_SERVER,DB_USERNAME,DB_PASSWORD,DB_DATABASE,DB_PORT) or die('could not connect: '. mysqli_connect_error());
?>
