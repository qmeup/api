<?php
$server = "us-cdbr-azure-west-b.cleardb.com";
$username = "bbfa6b7033a0a8";
$password = "bc38bf1f";
$db = "qmeup";

mysql_connect($server, $username, $password);
mysql_select_db($db) or die(mysql_error());

?>