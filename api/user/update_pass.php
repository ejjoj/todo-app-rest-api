<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();

$db = $database->connect();

$user = new User($db);

$data = json_decode(file_get_contents('php://input'));

// set ID property of user to be edited

$user->user_id = (!empty($data->user_id) ? $data->user_id : die());
$user->user_pass = (!empty($data->user_pass) ? $data->user_pass : die());

if ($user->update_pass()) {
    http_response_code(200);

    echo json_encode(
      array('message' => 'User password has been updated')
    );
} else {
    http_response_code(503);

    echo json_encode(
      array('message' => 'Unable to update user password')
    );
}