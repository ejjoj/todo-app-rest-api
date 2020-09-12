<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/Database.php';
include_once '../../models/Nav.php';

$database = new Database();
$db = $database->connect();

$cap = new Capabilities($db);

$result = $cap->read();

$num = $result->rowCount();

if ($num > 0) {
    $returnData = [];
    $capData = array();

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        $returnData = $nav->msg(0, 404, 'Page not found');
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

        json_encode($returnData);
    }
} else {
    $returnData = $nav->msg(0, 404, 'No results found.');
    json_encode($returnData);
}