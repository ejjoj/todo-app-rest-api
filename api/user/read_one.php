<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Access-Control-Allow-Credentials: true");
header('Content-Type: application/json');

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();

$db = $database->connect();

$user = new User($db);

if (isset($_GET['user_id']) && !empty($_GET['user_id'])) {
    $user->user_id = $_GET['user_id'];

    $result = $user->read_one();
    $num = $result->rowCount();

    if ($num > 0) {
        $user_arr = array();
        $user_arr['data'] = array();

        while ($row = $result->fetch(PDO::FETCH_ASSOC)) {
            extract($row);

            $user_item = array(
                'user_id' => $user_id,
                'user_name' => $user_name,
                'user_pass' => $user_pass,
                'user_list_id' => $user_list_id,
                'joined_at' => $joined_at
            );

            array_push($user_arr['data'], $user_item);
        }

        http_response_code(200);

        echo json_encode($user_arr);
    } else {
        http_response_code(404);
        echo json_encode(
          array('message' => 'No such users with given ID')
        );
    }
}