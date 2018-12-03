<?php

//USADO SOMENTE NO POSTMAN PARA INSERÇÃO NO DB
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

header('Access-Control-Allow-Methods: POST');
header('Access-Control-Allow-Headers: Access-Control-Allow-Headers, Content-Type, Access-Control-Allow-Methods,
   Authorization, X-Requested-With');

include_once '../../config/database.php';
include_once '../../db/mysql.php';

$data = new Database();
$db = $data->connect();

$conn = new MySQLConn($db);

$data = json_decode(file_get_contents("php://input"));

$conn->cp = $data->cp;
$conn->np = $data->np;
$conn->ed = $data->ed;
$conn->py = $data->py;

$conn->px = $data->px;

if ($conn->create()) {
    echo json_encode(
        array('message' => 'Inserido')
    );
} else {
    echo json_encode(
        array('message' => 'Ocorreu um erro')
    );
}
