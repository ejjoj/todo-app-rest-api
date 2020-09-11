<?php
//Headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");

include_once '../../config/Database.php';
include_once '../../models/User.php';

// Instantiate DB & connect

$database = new Database();

$db = $database->connect();

// Instatiate Users object

$user = new User($db);

// User query

$result = $user->read();

//Get row count

$num = $result->rowCount();

//Check if there are any users

if ($num > 0) {
    // User array
    $users_arr = array();
    $users_arr['data'] = array();

    while($row = $result->fetch(PDO::FETCH_ASSOC)) {
        extract($row);

        $user_item = array(
          'user_id' => $user_id,
          'user_name' => $user_name,
            'user_pass' => $user_pass,
            'user_list_id' => $user_list_id,
            'joined_at' => $joined_at
        );

        //Push to 'data'
        array_push($users_arr['data'], $user_item);
    }

    //Turn to JSON & output

    echo json_encode($users_arr);
} else {
    // No users
    echo json_encode(
      array('message' => 'No users')
    );
}