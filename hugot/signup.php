<?php
include "connection.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $data = json_decode(file_get_contents('php://input'), true);

    $firstName = $data["firstName"];
    $middleName = $data["middleName"];
    $lastName = $data["lastName"];
    $username = $data["username"];
    $password = password_hash($data["password"], PASSWORD_BCRYPT); // Hash the password
    $email = $data["email"];
    $phone = $data["phone"];
    $address = $data["address"];

    // Check if the username or email already exists
    $checkUser = $conn->prepare("SELECT * FROM users WHERE username = :username OR email = :email");
    $checkUser->bindParam(":username", $username);
    $checkUser->bindParam(":email", $email);
    $checkUser->execute();

    if ($checkUser->rowCount() > 0) {
        echo json_encode(["success" => false, "message" => "Username or email already exists."]);
    } else {
        $sql = "INSERT INTO users (first_name, middle_name, last_name, username, password, email, phone, address) 
                VALUES (:firstName, :middleName, :lastName, :username, :password, :email, :phone, :address)";
        $stmt = $conn->prepare($sql);
        $stmt->bindParam(":firstName", $firstName);
        $stmt->bindParam(":middleName", $middleName);
        $stmt->bindParam(":lastName", $lastName);
        $stmt->bindParam(":username", $username);
        $stmt->bindParam(":password", $password);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":phone", $phone);
        $stmt->bindParam(":address", $address);

        if ($stmt->execute()) {
            echo json_encode(["success" => true, "message" => "User registered successfully."]);
        } else {
            echo json_encode(["success" => false, "message" => "Error registering user."]);
        }
    }
} else {
    echo json_encode(["success" => false, "message" => "Invalid request method."]);
}
?>
