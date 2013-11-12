<?
function db($variable) {
	$db = array(
// Database Config
			'hostname' => 'localhost', 
			'username' => 'almasco_admin',
			'password' => '~Oh3n3Op4re',
			'dbname' => 'almasco_inque',
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