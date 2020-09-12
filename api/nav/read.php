<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/Database.php';
include_once '../../models/Nav.php';

$database = new Database();
$db = $database->connect();

$nav = new Nav($db);

$result = $nav->read();

$num = $result->rowCount();

if ($num > 0) {
    $returnData = [];
    $tableName = $nav->getTableName();
    $navData = array();

    if ($_SERVER['REQUEST_METHOD'] != 'GET') {
        $returnData = $nav->msg(0, 404, 'Page not found');
        echo json_encode($returnData);
    } else {
        $nav_item = array();
        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $nav_item = array(
                'id' => $id,
                'name' => $name,
                'path' => $path,
                'exact' => $exact,
                'component' => $component
            );
            array_push($navData, $nav_item);
        }

        $returnData = $nav->msg(1, 200, $navData);

        echo json_encode($returnData);
    }
} else {
    $returnData = $nav->msg(0, 404, 'No results found.');
    json_encode($returnData);
}