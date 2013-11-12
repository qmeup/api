<?
function db($variable) {
	$db = array(
// Database Config
			'hostname' => 'http://us-cdbr-azure-west-b.cleardb.com', 
			'username' => 'bbfa6b7033a0a8',
			'password' => 'bc38bf1f',
			'dbname' => 'qmeup',
			'tablename' => 'biz_locations'
);
	return $db[$variable];
}

function fields($variable) {
	$fields = array(
// Field Mappings
			'id' 	=> 'id',
			'latitude' 	=> 'latitude',
			'longitude' 	=> 'longitude',
			'name' 	=> 'name',
			'address' 	=> 'address',
			'category' 	=> 'category',
			'icon' 	=> 'icon'
);
	return $fields[$variable];
}
?>