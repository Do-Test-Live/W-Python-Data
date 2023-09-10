<?php
require_once 'include/dbController.php';
// Create an instance of DBController
$dbController = new DBController();
date_default_timezone_set("Asia/Hong_Kong");

$date = date('Y-m-d');

// Fetch data from the database
$query = "SELECT * FROM  page as p,racevalue as r WHERE p.id = r.page_id AND p.date = '$date'";
//$query = "SELECT * FROM racevalue as r, page as p WHERE p.id = r.page_id AND p.date = '$date' ORDER BY p.page_no ASC";
$rows = $dbController->runQuery($query);


$response = ['status' => 'success', 'data' => $rows];
echo json_encode($response);
