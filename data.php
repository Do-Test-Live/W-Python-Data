<?php
// Connect to the database
$dbHost = 'localhost';
$dbUser = 'root';
$dbPass = '';
$dbName = 'race';

$conn = new mysqli($dbHost, $dbUser, $dbPass, $dbName);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch data from the database
$sql = "SELECT * FROM racevalue";
$result = $conn->query($sql);
$rows = [];

if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $rows[] = $row;
    }
}

// Handle CRUD operations
$action = isset($_POST['action']) ? $_POST['action'] : '';

if ($action === 'fetch') {
    $response = ['status' => 'success', 'data' => $rows];
    echo json_encode($response);
} elseif ($action === 'edit') {
    $id = $_POST['id'];
    $newWin = $_POST['new_win'];
    $newPlace = $_POST['new_place'];

    $sql = "UPDATE racevalue SET new_win='$newWin', new_place='$newPlace' WHERE id=$id";
    $result = $conn->query($sql);

    if ($result) {
        $response = ['status' => 'success', 'message' => 'Record updated successfully.'];
    } else {
        $response = ['status' => 'error', 'message' => 'Failed to update record.'];
    }

    echo json_encode($response);
}
?>
