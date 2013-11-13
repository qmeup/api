<?php
$url=parse_url(getenv("mysql://bbfa6b7033a0a8:bc38bf1f@us-cdbr-azure-west-b.cleardb.com/qmeup?reconnect=true"));

$server = $url["host"];
$username = $url["user"];
$password = $url["pass"];
$db = substr($url["path"],1);

mysql_connect($server, $username, $password);
mysql_select_db($db) or die(mysql_error());

?>