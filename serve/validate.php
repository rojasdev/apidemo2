<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
 
// get database connection
include_once '../config/connect.php';
 
// instantiate product object
include_once '../classes/users.php';
 
$database = new Database();
$db = $database->getConnection();
 
$user = new Users($db);
 
// get posted json data
$data = json_decode(file_get_contents("php://input"));
 
// make sure data is not empty
if(
    !empty($data->username) &&
    !empty($data->password)
){
 
    // set product property values
    $user->username = $data->username;
    $user->password = $data->password;
 
    // create the product
    if($user->validate()){
 
        // set response code - 201 created
        http_response_code(201);
 
        // tell the user
        echo json_encode(array("message" => "User is valid.","validation" => true));
    }
 
    // if unable to validate the user, tell the user
    else{
 
        // set response code - 503 service unavailable
        http_response_code(503);
 
        // tell the user
        echo json_encode(array("message" => "Cannot verify user.", "validation" => false));
    }
}
 
// tell the user data is incomplete
else{
 
    // set response code - 400 bad request
    http_response_code(400);
 
    // tell the user
    echo json_encode(array("message" => "Unable to validate user. Data is incomplete."));
}
?>