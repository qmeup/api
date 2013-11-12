<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
<title>Geocoding your database</title>
<style>
body {color: #333;background: #ddd; font-family: 'Trebuchet MS', sans-serif; font-size: 16px; font-style: normal; font-weight: bold; text-transform: normal; letter-spacing: -1px; line-height: 1.2em;}
#container {width: 750px; margin: 20px; background: #eee; border: 1px solid #aaa}
#header {margin: 0px 20px; color: #111}
#success {width: 720px; margin: 15px; border: 1px solid #aaa; background: #fff; height: 320px; overflow-y: scroll; background: #bcdabc}
#success span {display: block; padding: 4px; border-bottom: 1px solid #aaa}
#failures{width: 720px; margin: 15px; border: 1px solid #aaa; background: #fff; height: 120px; overflow-y: scroll; background: #f7d9da}
#failures span {display: block; padding: 4px; border-bottom: 1px solid #888}
</style>
</head>
<body>
<div id="container">
<div id="header">
<h1>POI Auto Map - Batch Geocoding</h1>
</div>
<div id="main">
<?php
  require_once("db.config.php");
  define("MAPS_HOST", "maps.google.com");
  try {
      $dbh = new PDO("mysql:host=" . db('hostname') . ";dbname=" . db('dbname'), db('username'), db('password'));
      $sql = "SELECT * FROM " . db('tablename') . " WHERE " . fields('latitude') . " = '' OR " . fields('latitude') . " = '0'";
      $failures = "<div id='failures'>";
	  echo '<div id="success">';
	  foreach ($dbh->query($sql) as $row) {
          $geocode_pending = true;
          while ($geocode_pending) {
              $delay = 100;
              $base_url = "http://" . MAPS_HOST . "/maps/api/geocode/xml?address=";
              $address = $row[fields('address')];
			  $recordID = $row[fields('id')];
              $request_url = $base_url . urlencode($address) . "&sensor=true";
              $xml = simplexml_load_file($request_url) or die("url not loading");
              $status = $xml->status;
              if (strcmp($status, "OK") == 0) {
                  $geocode_pending = false;
                  $lat = $xml->result->geometry->location->lat;
                  $lng = $xml->result->geometry->location->lng;
                  $update_SQL = "UPDATE " . db('tablename') . " SET " . fields('latitude') . "='$lat', " . fields('longitude') . "='$lng' WHERE " . fields('id') . "='$recordID'";
                  //echo $update_SQL;
				  $count = $dbh->exec($update_SQL);
				  echo '<span><strong>'. $address . '</strong> - Geocode Successful ('. $lat . ',' . $lng . ')</span>';
              } elseif (strcmp($status, "OVER_QUERY_LIMIT") == 0) {
                  // sent geocodes too fast
                  $delay += 100000;
              } else {
                  // failure to geocode
                  $geocode_pending = false;
                  $failures .= "<span>" . $address . " failed to geocode. ";
                  $failures .= "Received status " . $status . "</span>";
              }
              usleep($delay);
          }
      }
      $dbh = null;
  }
  catch (PDOException $e) {
      echo $e->getMessage();
  }
echo "</div>";
  $failures .= "</div>";
  echo $failures;
  
?>
</div>
</body>
</html>
