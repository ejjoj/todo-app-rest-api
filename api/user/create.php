<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type");

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();

$db = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents('php://input'));

if (
    isset($data->user_name) &&
    !empty($data->user_name) &&
    strlen($data->user_name) <= 50 &&
    isset($data->user_pass) &&
    !empty($data->user_pass) &&
    strlen($data->user_pass) <= 75 &&
    isset($data->joined_at) &&
    !empty($data->joined_at) &&
    $user->validateDate($data->joined_at)
) {
    $user->user_name = $data->user_name;
    $user->user_pass = $data->user_pass;
    $user->joined_at = $data->joined_at;

    if ($user->create()) {
        http_response_code(200);

        echo json_encode(
          array('message' => 'User has been added')
        );
    } else {
        http_response_code(503);

        echo json_encode(
          array('message' => 'Unable to add user')
        );
    }
} else {
    http_response_code(400);

    echo json_encode(
        array('message' => 'Unable to add user. Data is incomplete')
    );
}