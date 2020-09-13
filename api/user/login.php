<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/../../config/Database.php';
require __DIR__ . '/../../config/JwtHandler.php';

function msg($success, $status, $message, $extra = []) {
    return array_merge([
        'success' => $success,
        'status' => $status,
        'message' => $message
    ], $extra);
}

$db = new Database();
$conn = $db->connect();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    $returnData = msg(0, 404, 'Page Not Found!');
} elseif (
    !isset($data->login) ||
    !isset($data->password) ||
    empty(trim($data->login)) ||
    empty(trim($data->password))
) {
    $fields = ['fields' => ['login', 'password']];
    $returnData = msg(0, 422, 'Please fill in all requested fields!', $fields);
} else {
   $login = trim($data->login);
   $password = trim($data->password);

   if (empty($login))
       $returnData = msg(0, 422, 'Invalid username!');
   elseif(strlen($password) < 8)
       $returnData = msg(0, 422, 'Your password must contain more than 8 characters!');
   else {
       try {
           $fetchUserByID = 'SELECT * FROM `users` WHERE `user_name`= "' . $login . '"';
           $queryStmt = $conn->prepare($fetchUserByID);
           $queryStmt->execute();

           if ($queryStmt->rowCount()) {
               $row = $queryStmt->fetch(PDO::FETCH_ASSOC);
               $checkPassword = password_verify($password, $row['user_pass']);

               if ($checkPassword) {
                   $jwt = new JwtHandler();
                   $token = $jwt->_jwt_encode_data(
                     'http://localhost/php_rest_todoapp/',
                       array("user_id" => $row['user_id'])
                   );

                   $returnData = [
                       'success' => 1,
                       'message' => 'You have successfully logged in',
                       'token' => $token
                   ];
               } else {
                   $returnData = msg(0, 422, 'Invalid password!');
               }
           } else {
               $returnData = msg(0, 422, 'Invalid username!');
           }
       } catch (PDOException $e) {
           $returnData = msg(0, 500, $e->getMessage());
       }
   }
}

echo json_encode($returnData);