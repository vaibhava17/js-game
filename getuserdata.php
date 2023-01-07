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

$userid = isset($_GET['id']) ? (int) $_GET['id'] : null;
$returnData = [];

if ($_SERVER["REQUEST_METHOD"] != "GET"):
  $returnData = $error_handler->getResponse(0, 404, 'Page Not Found!');

else:
  try {

    if ($userid):
      $fetch_history = "SELECT * FROM `withdrawal` WHERE userid=:userid";
    $fetch_user="SELECT *FROM users WHERE mobile=:userid";
      $fetch_stmt = $conn->prepare($fetch_history);
      $fetch_stmt->bindValue(':userid', $userid, PDO::PARAM_INT);
      
      $fetch_stmt->execute();
      $fetch_stmt_user = $conn->prepare($fetch_user);
      $fetch_stmt_user->bindValue(':userid', $userid, PDO::PARAM_INT);
      
      $fetch_stmt_user->execute();
      if ($fetch_stmt->rowCount() && $fetch_stmt_user->rowCount()):
        $history = $fetch_stmt->fetchAll (PDO::FETCH_ASSOC);
        $user_data = $fetch_stmt_user->fetchAll (PDO::FETCH_ASSOC);
        $returnData = $error_handler->getResponse(1, 200, 'User Data', array('list' => $history,"user"=>$user_data));
      else:
        $returnData = $error_handler->getResponse(0, 422, 'No Data found!');
      endif;
    endif;
  } catch (PDOException $e) {
    $returnData = $error_handler->getResponse(0, 500, $e->getMessage());
  }
endif;

echo json_encode($returnData);