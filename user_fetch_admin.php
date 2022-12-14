<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: POST");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
require __DIR__ . '/classes/db.config.php';
require __DIR__ . '/classes/error.handler.php';


$db_connection = new Database();
$conn = $db_connection->dbConnection();
$error_handler = new ErrorHandler();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

if ($_SERVER["REQUEST_METHOD"] != "POST"):
  $returnData = $error_handler->getResponse(0, 404, 'Page Not Found!');

else:

   
   
      $fetch_history = "SELECT * FROM `users` ";
      $fetch_stmt = $conn->prepare($fetch_history);
      
      $fetch_stmt->execute();
      if ($fetch_stmt->rowCount()):
        $data = $fetch_stmt->fetchAll (PDO::FETCH_ASSOC);
        $returnData = $error_handler->getResponse(1, 200, 'User List', array('list' => $data));
      else:
        $returnData = $error_handler->getResponse(0, 422, 'No Data found!');
      endif;
    endif;
  


echo json_encode($returnData);