<?php
$mysqli = new mysqli ('server', 'user', 'password', 'bd');

if ($mysqli->connect_error) exit ('Cannot connect to the database');

$route = 'img/';
$response = [];
$types = ['image/jpeg', 'image/png', 'image/gif'];
$flag = true;

$_POST = array_map(function($data) use ($mysqli){
    return $mysqli->real_escape_string(strip_tags($data));
}, $_POST);

foreach ($_FILES as $file){
    $mimeType = getimagesize($file['tmp_name'])['mime'];

    if ($file['error'] != UPLOAD_ERR_OK || !in_array($mimeType, $types)){
        $flag = false;
        break;
    }
}

if ($flag){
    foreach ($_FILES as $file){
        $type = substr($file['type'], strpos($file['type'], '/') + 1);
        $name = microtime(true) . '.' . $type;
        $tmp_name = $file['tmp_name'];
        move_uploaded_file($tmp_name, $route . $name);
        $response[] = $route . $name;
    }

    $ok = 'yes';
    $values = "('" . implode("'), ('", $response) . "')";
    $mysqli->query("INSERT INTO table (field) VALUES {$values}");
}
else{
    $ok = 'no';
}

echo json_encode(['ok' => $ok, 'dataFiles' => $response]);
?>