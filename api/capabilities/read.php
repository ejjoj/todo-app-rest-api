<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/Database.php';
include_once '../../models/Capabilities.php';

$database = new Database();
$db = $database->connect();

$cap = new Capabilities($db);

$result = $cap->read();

$num = $result->rowCount();

if ($num > 0) {
    $returnData = [];
    $capData = array();

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        $returnData = $cap->msg(0, 404, 'Page not found');
        echo json_encode($returnData);
    } else {
        $capItem = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $capItem = array(
                'id' => $id,
                'name' => $name,
                'desc' => $desc
            );
            array_push($capData, $capItem);
        }

        $returnData = $cap->msg(1, 200, $capData);
        echo json_encode($returnData);
    }
} else {
    $returnData = $cap->msg(0, 404, 'No results found.');
    echo json_encode($returnData);
}