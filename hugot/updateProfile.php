<?php
header('Content-Type: application/json');
include 'db_connection.php'; // Include your database connection file

$data = json_decode(file_get_contents("php://input"), true);

$user_id = $data['user_id'];
$first_name = $data['first_name'];
$last_name = $data['last_name'];
$email = $data['email'];
$bio = $data['bio'];
$profile_picture = $data['profile_picture'];

$query = "UPDATE profiles SET first_name = ?, last_name = ?, email = ?, bio = ?, profile_picture = ? WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("sssssi", $first_name, $last_name, $email, $bio, $profile_picture, $user_id);
$result = $stmt->execute();

if ($result) {
    echo json_encode(["success" => true]);
} else {
    echo json_encode(["success" => false, "message" => "Failed to update profile"]);
}

$stmt->close();
$conn->close();
?>
