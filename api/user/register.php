<?php
header('Access-Control-Allow-Origin: *');
header("Access-Control-Allow-Credentials: true");
header('Access-Control-Allow-Methods: GET, PUT, POST, DELETE, OPTIONS');
header('Access-Control-Max-Age: 1000');
header('Access-Control-Allow-Headers: Origin, Content-Type, X-Auth-Token , Authorization');

function msg($success, $status, $message, $extra = []) {
    return array_merge([
       'success' => $success,
       'status' => $status,
       'message' => $message
    ], $extra);
}

include_once '../../config/Database.php';
include_once '../../models/User.php';

$database = new Database();

$db = $database->connect();

$user = new User($db);

$tableName = $user->getTableName();

$data = json_decode(file_get_contents('php://input'));
$returnData = [];

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $returnData = msg(0, 404, 'Page not found!');
} elseif (
    !isset($data->name) ||
    !isset($data->email) ||
    !isset($data->password) ||
    !isset($data->joined_at) ||
    empty(trim($data->name)) ||
    empty(trim($data->email)) ||
    empty(trim($data->password) ||
    empty(trim($data->joined_at))
    )
) {
    $fields = ['fields' => ['name', 'email', 'password']];
    $returnData = msg(0, 422 ,'Please fill in all Required Data', $fields);
} else {
    $user->user_name = trim($data->name);
    $user->user_email = trim($data->email);
    $user->user_pass = trim($data->password);
    $user->joined_at = trim($data->joined_at);


    if (!filter_var($user->user_email, FILTER_VALIDATE_EMAIL)) {
        $returnData = msg(0, 422, 'Invalid Email Address');
    } elseif (strlen($user->user_pass) < 8) {
        $returnData = msg(0, 422, 'Your password must be at least 8 characters long');
    } elseif (strlen($user->user_name) < 3) {
        $returnData = msg(0, 422, 'Your name must be at least 3 characters long!');
    }elseif (!$user->validateDate($user->joined_at)) {
        $returnData = msg(0, 422, 'You must fetch join date!');
    } else {
        try {
            $check_email = 'SELECT `user_email` FROM `' . $tableName . '` WHERE `user_email`="' . $user->user_email . '"';
            $check_email_stmt = $db->prepare($check_email);
            $check_email_stmt->execute();

            if ($check_email_stmt->rowCount()) {
                $returnData = msg(0, 422, 'This e-mail is already in use!');
            } else {
                $user->user_name = htmlspecialchars(strip_tags($user->user_name));
                $user->user_email = htmlspecialchars(strip_tags($user->user_email));
                $user->user_pass = password_hash($user->user_pass, PASSWORD_DEFAULT);
                $user->joined_at = $user->validateDate($user->joined_at) ? $user->joined_at : die();

                $insert_query = 'INSERT INTO `' . $tableName . '` (`user_name`, `user_email`, `user_pass`, `joined_at`) VALUES ("' . $user->user_name . '","' . $user->user_email . '", "'. $user->user_pass . '" , "' . $user->joined_at . '")';
                $insert_stmt = $db->prepare($insert_query);

                $insert_stmt->execute();

                $returnData = msg(1, 201, 'You have successfully registered');
            }
        } catch (PDOException $e) {
            $returnData = msg(0, 500, $e->getMessage());
        }
    }
}

echo json_encode($returnData);