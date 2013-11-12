<?php

$username = "almasco_admin";
$password = "~Oh3n3Op4re";
$hostname = "localhost";	
$database = "almasco_inque";

$con = mysql_connect($hostname, $username, $password) or die(mysql_error());
mysql_select_db($database) or die(mysql_error());

?>