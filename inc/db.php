<?php

$username = "bbfa6b7033a0a8";
$password = "bc38bf1f";
$hostname = "http://us-cdbr-azure-west-b.cleardb.com'";	
$database = "qmeup";

$con = @mysql_connect($hostname, $username, $password) or die(mysql_error());
mysql_select_db($database) or die(mysql_error());

?>