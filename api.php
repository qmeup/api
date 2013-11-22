<?php
// Include required files
require 'inc/db.php';
require 'inc/myjson.class.php';

$request=$_REQUEST['request'];
$query = "";
switch ($request)
{
case "listbusinesses":
  $lat = $_REQUEST['lat'];
  $long = $_REQUEST['lng'];
  if(!isset($_REQUEST['distance']))
  {
    $distance = "50";
  }
  else
  {
    $distance = $_REQUEST['distance'];
  }
  if(isset($_REQUEST['search']))
  {
	$search = $_REQUEST['search'];
	$query = "SELECT ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS((".$long." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`, latitude, longitude, biz_locations.id as location_id, location_code, yelp_id, address, businesses.icon, businesses.image, description, businesses.name, businesses.category, queues.length, open_from, open_to FROM `biz_locations` inner join `queues` on biz_locations.id = queues.b_id inner join `businesses` on biz_locations.b_id = businesses.id WHERE status = 1 AND name LIKE '%".$search."%' HAVING `distance`<='".$distance."' ORDER BY `distance` ASC";
  }
  else
  {
	$query = "SELECT ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS((".$long." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`, latitude, longitude, biz_locations.id as location_id, location_code, yelp_id, address, businesses.icon, businesses.image, description, businesses.name, businesses.category, queues.length, open_from, open_to FROM `biz_locations` inner join `queues` on biz_locations.id = queues.b_id inner join `businesses` on biz_locations.b_id = businesses.id  WHERE status = 1 HAVING `distance`<='".$distance."' ORDER BY `distance` ASC";
  }
  break;
case "getbusiness":
  $id = $_REQUEST['id'];
  $query = "select biz_locations.id AS location_id, businesses.id as business_id address, phone, icon, image, description, name, category, queues.id AS qid, queues.length, open_from, open_to from biz_locations inner join queues on biz_locations.id = queues.b_id inner join `businesses` on biz_locations.b_id = businesses.id where status = 1 and biz_locations.id = ".$id;
  break;
case "listcategories":
  $query = "SELECT category,COUNT(*) as count FROM biz_locations inner join `businesses` on biz_locations.b_id = businesses.id GROUP BY category ORDER BY category ASC";
  break;
case "getcategory":
  $lat = $_REQUEST['lat'];
  $long = $_REQUEST['lng'];
  $category_name = $_REQUEST['name'];
  if(!isset($_REQUEST['distance']))
  {
    $distance = "50";
  }
  else
  {
    $distance = $_REQUEST['distance'];
  }
  if(isset($_REQUEST['search']))
  {
	$search = $_REQUEST['search'];
	$query = "SELECT ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS((".$long." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`, latitude, longitude, biz_locations.id as location_id, location_code, yelp_id, address, businesses.icon, businesses.image, description, businesses.name, businesses.category, queues.length, open_from, open_to FROM `biz_locations` inner join `queues` on biz_locations.id = queues.b_id inner join `businesses` on biz_locations.b_id = businesses.id  WHERE status = 1 AND category = '".$category_name."' AND name LIKE '%".$search."%' HAVING `distance`<='".$distance."' ORDER BY `distance` ASC";
  }
  else
  {
	$query = "SELECT ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS((".$long." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`, latitude, longitude, biz_locations.id as location_id, location_code, yelp_id, address, businesses.icon, businesses.image, description, businesses.name, businesses.category, queues.length, open_from, open_to FROM `biz_locations` inner join `queues` on biz_locations.id = queues.b_id inner join `businesses` on biz_locations.b_id = businesses.id  WHERE status = 1 AND category = '".$category_name."' HAVING `distance`<='".$distance."' ORDER BY `distance` ASC";
  }
  break;
case "listcoupons":
  $business_id = $_REQUEST['id'];
  $query = "select * from coupons where CURDATE() between valid_from and valid_to and b_id = ".$business_id;
  break;
case "getcoupon":
  $coupon_id = $_REQUEST['id'];
  $query = "select * from coupons where id = ".$coupon_id;
  break;
case "createuser":
  $user_token = uniqid('InQue_', true);
  $user_id = createUser($user_token);
  $query = "select * from users where id = ".$user_id;
  break;
case "joinqueue":
  $user_id = $_REQUEST['id'];
  $location_id = $_REQUEST['bid'];
  $queue_id = $_REQUEST['qid'];
  $qlength = getQueueLength($location_id);
  $position = $qlength + 1;
  if(isset($_REQUEST['notify']))
  {
	$notify = $_REQUEST['notify'];
  }
  else
  {
	$notify = 3;
  }
  mysql_query("INSERT INTO transactions(position,q_id,b_id,u_id,notify) VALUES(".$position.",".$queue_id.",".$location_id.",".$user_id.",".$notify.")");
  incrementQueueLength($location_id);
  $query = "select * from transactions where q_id=".$queue_id." and b_id=".$location_id." and u_id=".$user_id; 
  break;
case "leavequeue":
  $user_id = $_REQUEST['id'];
  $business_id = $_REQUEST['bid'];
  $queue_id = $_REQUEST['qid'];
  $qlength = getQueueLength($business_id);
  $position = $qlength + 1;
  mysql_query("DELETE FROM transactions WHERE u_id = ".$user_id." AND b_id = ".$business_id." AND q_id = ".$queue_id);
  decrementQueueLength($business_id);
  echo json_encode("success");
  break;
case "getwaittime":
  $location_id = $_REQUEST['id'];
  $query = "select avg(diff) as AverageWaitTimeinMinutes from (select TIMESTAMPDIFF(MINUTE, t1.time_stamp, min(t2.time_stamp)) as diff from wait_times t1 inner join wait_times t2 on t2.time_stamp > t1.time_stamp where t2.b_id = ".$location_id." group by t1.time_stamp) a";
  break;
default:
  $lat = $_REQUEST['lat'];
  $long = $_REQUEST['lng'];
  if(!isset($_REQUEST['distance']))
  {
    $distance = "50";
  }
  else
  {
    $distance = $_REQUEST['distance'];
  }
  if(isset($_REQUEST['search']))
  {
	$search = $_REQUEST['search'];
	$query = "SELECT ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS((".$long." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`, latitude, longitude, biz_locations.id as location_id, location_code, yelp_id, address, businesses.icon, businesses.image, description, businesses.name, businesses.category, queues.length, open_from, open_to FROM `biz_locations` inner join `queues` on biz_locations.id = queues.b_id inner join `businesses` on biz_locations.b_id = businesses.id  WHERE status = 1 AND name LIKE '%".$search."%' HAVING `distance`<='".$distance."' ORDER BY `distance` ASC";
  }
  else
  {
	$query = "SELECT ((ACOS(SIN(".$lat." * PI() / 180) * SIN(latitude * PI() / 180) + COS(".$lat." * PI() / 180) * COS(latitude * PI() / 180) * COS((".$long." - longitude) * PI() / 180)) * 180 / PI()) * 60 * 1.1515) AS `distance`, latitude, longitude, biz_locations.id as location_id, location_code, yelp_id, address, businesses.icon, businesses.image, description, businesses.name, businesses.category, queues.length, open_from, open_to FROM `biz_locations` inner join `queues` on biz_locations.id = queues.b_id inner join `businesses` on biz_locations.b_id = businesses.id  WHERE status = 1 HAVING `distance`<='".$distance."' ORDER BY `distance` ASC";
  }
}

// Do the proccess for non-indented
if($query != "")
{
  // Make new instance
  $json = new MyJSON;
  $results = $json->SQLtoJSON($query);	
  
  // Check errors
  if(count($json->errors) > 0) 
  {
	echo json_encode(0);
  }
  else
  {
	echo $results;
  }
}

// Functions
function getQueueLength($bid)
{
	$sql = mysql_fetch_assoc(mysql_query("SELECT * FROM queues WHERE b_id =".$bid));
    $qid = $sql['length'];
	return $qid;
}
function getCouponNumber($bid)
{
	$sql = mysql_fetch_assoc(mysql_query("SELECT count(*) FROM coupons WHERE b_id =".$bid));
    $qid = $sql['length'];
	return $qid;
}
function incrementQueueLength($bid)
{
	mysql_query("UPDATE queues SET length = length + 1 where b_id = ".$bid);
}
function decrementQueueLength($bid)
{
	mysql_query("UPDATE queues SET length = length - 1 where b_id = ".$bid);
}
function createUser($token)
{
	$q = "INSERT INTO users(user_token) VALUES('".$token."')";
	mysql_query($q);
	$sql = mysql_fetch_assoc(mysql_query("SELECT * FROM users WHERE user_token ='".$token."'"));
    $uid = $sql['id'];
	return $uid;
}
?>