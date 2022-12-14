<?php
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Headers: access");
header("Access-Control-Allow-Methods: GET");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

require __DIR__ . '/classes/db.config.php';
require __DIR__ . '/classes/error.handler.php';
require __DIR__ . '/middlewares/admin.middleware.php';

$allHeaders = getallheaders();
$db_connection = new Database();
$conn = $db_connection->dbConnection();
$auth = new AdminAuth($conn, $allHeaders);
$error_handler = new ErrorHandler();

$data = json_decode(file_get_contents("php://input"));
$returnData = [];

// url params
// $limit = isset($_GET['limit']) ? $_GET['limit'] : 10;
// $page = isset($_GET['page']) ? $_GET['page'] : 1;
// $offset = ($page - 1) * $limit;
$search = isset($_GET['search']) ? $_GET['search'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';
$sort = isset($_GET['sort']) ? $_GET['sort'] : 'DESC';
$sort_by = isset($_GET['sort_by']) ? $_GET['sort_by'] : 'withdrawid';

if ($_SERVER["REQUEST_METHOD"] != "GET"):
  $returnData = $error_handler->getResponse(0, 404, 'Page Not Found!');
elseif (!$auth->isValid()):
  $returnData = $error_handler->getResponse(0, 401, 'Unauthorized!');
else:
  try {
    $fetch_history = "SELECT * FROM `withdrawal` WHERE (`withdrawstatus` LIKE '%$status%'  AND `userid` LIKE '%$search%') ORDER BY $sort_by $sort ";
    // LIMIT $limit OFFSET $offset  
    $fetch_stmt = $conn->prepare($fetch_history);
    $fetch_stmt->execute();
    if ($fetch_stmt->rowCount()):
      $data = $fetch_stmt->fetchAll(PDO::FETCH_ASSOC);
      $returnData = $error_handler->getResponse(1, 200, 'User Withdraw History', array('list' => $data, "query" => $fetch_history));
    else:
      $returnData = $error_handler->getResponse(0, 422, 'No Data found!');
    endif;
  } catch (PDOException $e) {
    $returnData = $error_handler->getResponse(0, 500, $e->getMessage());
  }
endif;

echo json_encode($returnData);