<?php
header('Content-Type: image/jpeg');
include_once 'resize.php';
$resize = new Resize();
$mysqli = new mysqli ('server', 'user', 'password', 'db');

if ($mysqli->connect_error) exit ('Cannot connect to the database: ' . $mysqli->connect_error);

$route = 'img/';
$response = [];
$errors = 0;
$ok = 'no';
$max_file_uploads = ini_get('max_file_uploads');
$post_max_size = (int)ini_get('post_max_size');
$upload_max_filesize = (int)ini_get('upload_max_filesize');
$sizeUploaded = 0;

foreach ($_FILES as $file) $sizeUploaded += $file['size'];
$sizeUploaded /= 1048576;

$_POST = array_map(function($data) use ($mysqli){
	return $mysqli->real_escape_string(strip_tags($data));
}, $_POST); 

if (count($_FILES) <= $max_file_uploads && $sizeUploaded <= $post_max_size){
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
		$mysqli->query("INSERT INTO files (file) VALUES {$values}") or exit ('Could not execute query: ' . $mysqli->error);
	}
}

echo json_encode(['ok' => $ok, 'dataFiles' => $response]);
?>
