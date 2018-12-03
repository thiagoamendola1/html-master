<?php

//SERIA USADO PARA ACESSAR OS PONTOS DE ONIBUS
header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

include_once '../../config/database.php';
include_once '../../db/mysql.php';

$data = new Database();
$db = $data->connect();

$conn = new MySQLConn($db);

$result = $conn->read();

$num = $result->rowCount();

if ($num > 0) {
    $conn_arr = array();
    $conn_arr['pontos'] = array();
    while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);
        $conn_data = array('nome' => $nome);
        array_push($conn_arr['pontos'], $conn_data);
    }
    echo json_encode($conn_arr);
} else {
    echo json_encode(
        array('message:' => 'Requisição vazia')
    );
}
