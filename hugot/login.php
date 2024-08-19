<?php
include "connection.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    // Ensure that the username and password are provided
    if (isset($data["username"]) && isset($data["password"])) {
        $username = $data["username"];
        $password = $data["password"];

        $sql = "SELECT * FROM users WHERE username = :username";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":username", $username);
        $stmt->execute();

        if ($stmt->rowCount() > 0) {
            $user = $stmt->fetch(PDO::FETCH_ASSOC);

            if (password_verify($password, $user["password"])) {
                // Include user_id in the response
                echo json_encode(["success" => true, "message" => "Login successful.", "user_id" => $user["user_id"], "userRole" => "user"]);
            } else {
                echo json_encode(["success" => false, "message" => "Invalid password."]);
            }
        } else {
            echo json_encode(["success" => false, "message" => "User not found."]);
        }
    } else {
        echo json_encode(["success" => false, "message" => "Username and password are required."]);
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
