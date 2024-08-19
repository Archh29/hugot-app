<?php
$host = "localhost"; // Change to your host
$db_name = "hugot"; // Change to your database name
$username = "root"; // Change to your database username
$password = ""; // Change to your database password

try {
    $conn = new PDO("mysql:host=$host;dbname=$db_name", $username, $password);
    $conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $exception) {
    echo "Connection error: " . $exception->getMessage();
    exit();
}
?>
