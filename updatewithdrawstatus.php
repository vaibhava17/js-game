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

$withdrawid = isset($_GET['withdrawid']) ? $_GET['withdrawid'] : null;
$withdrawstatus = isset($_GET['withdrawstatus']) ? $_GET['withdrawstatus'] : null;

if ($_SERVER["REQUEST_METHOD"] != "POST"):
  $returnData = $error_handler->getResponse(0, 404, 'Page Not Found!');
else:
  if (empty($data) && (empty($withdrawid) )):
    $returnData = $error_handler->getResponse(0, 422, 'Invalid Data! Please try again.');
  else:
    try {
      // $game_data = "SELECT withdrawstatus FROM withdrawal WHERE withdrawid = :value ";
      // $get_stmt = $conn->prepare($game_data);
      // $get_stmt->bindValue(':value', $withdrawid, PDO::PARAM_STR);
      // $get_stmt->execute();
      // if ($get_stmt->rowCount() > 0):
      //   $game = $get_stmt->fetch(PDO::FETCH_ASSOC);
        $sql = "UPDATE withdrawal SET withdrawstatus=:withdrawstatus  WHERE withdrawid=:value ";
        $stmt = $conn->prepare($sql);
        $stmt->bindValue(':value', $withdrawid, PDO::PARAM_STR);
        $stmt->bindValue(':withdrawstatus', $withdrawstatus, PDO::PARAM_STR);
      
        $stmt->execute();
        $returnData = $error_handler->getResponse(1, 200, 'Withdraw History Updated Successfully!');
      // else:
      //   $returnData = $error_handler->getResponse(0, 404, 'No Data Found!');
      // endif;
    } catch (PDOException $e) {
      $returnData = $error_handler->getResponse(0, 422, $e->getMessage());
    }
  endif;
endif;

echo json_encode($returnData);