<?php
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require_once '../config/database.php';
require_once '../src/User.php';
require_once '../src/ApiResponse.php';

$database = new Database();
$db = $database->getConnection();
$user = new User($db);

$request_method = $_SERVER["REQUEST_METHOD"];
$input_data = json_decode(file_get_contents("php://input"));

switch ($request_method) {
    case 'GET':
        $stmt = $user->read();
        $num = $stmt->rowCount();

        if ($num > 0) {
            $users_arr = array();
            $users_arr["records"] = array();

            while ($row = $stmt->fetch(PDO::FETCH_ASSOC)) {
                extract($row);

                $user_item = array(
                    "id" => $id,
                    "name" => $name,
                    "email" => $email
                );

                array_push($users_arr["records"], $user_item);
            }

            ApiResponse::sendResponse(200, "Users found", $users_arr);
        } else {
            ApiResponse::sendResponse(404, "No users found");
        }
        break;

    case 'POST':
        if (!empty($input_data->name) && !empty($input_data->email)) {
            $user->name = $input_data->name;
            $user->email = $input_data->email;

            if ($user->create()) {
                ApiResponse::sendResponse(201, "User created");
            } else {
                ApiResponse::sendResponse(503, "Unable to create user");
            }
        } else {
            ApiResponse::sendResponse(400, "Incomplete data");
        }
        break;

    case 'PUT':
        if (!empty($input_data->id) && !empty($input_data->name) && !empty($input_data->email)) {
            $user->id = $input_data->id;
            $user->name = $input_data->name;
            $user->email = $input_data->email;

            if ($user->update()) {
                ApiResponse::sendResponse(200, "User updated");
            } else {
                ApiResponse::sendResponse(503, "Unable to update user");
            }
        } else {
            ApiResponse::sendResponse(400, "Incomplete data");
        }
        break;

    case 'DELETE':
        if (!empty($input_data->id)) {
            $user->id = $input_data->id;

            if ($user->delete()) {
                ApiResponse::sendResponse(200, "User deleted");
            } else {
                ApiResponse::sendResponse(503, "Unable to delete user");
            }
        } else {
            ApiResponse::sendResponse(400, "Incomplete data");
        }
        break;

    default:
        ApiResponse::sendResponse(405, "Method not allowed");
        break;
}
