<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your database connection file

$user_id = $_GET['user_id']; // Get user ID from query parameters

$query = "SELECT * FROM profiles WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$profile = $result->fetch_assoc();

echo json_encode($profile);

$stmt->close();
$conn->close();
?>
