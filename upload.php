<?php
header('Content-Type: image/jpeg');
include_once 'resize.php';
$resize = new Resize();
$mysqli = new mysqli ('server', 'user', 'password', 'bd');

if ($mysqli->connect_error) exit ('Cannot connect to the database');

$route = 'img/';
$response = [];
$errors = 0;
$ok = 'no';
$max_file_uploads = ini_get('max_file_uploads');
$upload_max_filesize = substr(ini_get('upload_max_filesize'), 0, strpos(ini_get('upload_max_filesize'), 'M'));

$_POST = array_map(function($data) use ($mysqli){
	return $mysqli->real_escape_string(strip_tags($data));
}, $_POST);

if (count($_FILES) <= $max_file_uploads){
	foreach ($_FILES as $file){
		if ($aux = $resize->upload($file['tmp_name'], $route, $file['name'], $file['error'], $upload_max_filesize)){
			$resize->newSize($route . $aux['name'], $route, $aux['name']);
			$response[] = $route . $aux['name'];
		}
		else{
			$errors++;
		}
	}

	if ($errors < count($_FILES)){ 
		$ok = 'yes';
		$values = "('" . implode("'), ('", $response) . "')";
		$mysqli->query("INSERT INTO files (file) VALUES {$values}");
	}
}

echo json_encode(['ok' => $ok, 'dataFiles' => $response]);
?>
