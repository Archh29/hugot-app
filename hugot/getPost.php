<?php
header('Content-Type: application/json');

// Database connection details
$host = 'localhost';
$dbname = 'hugotapp';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Fetch posts
    $sql = "SELECT * FROM posts";
    $stmt = $pdo->query($sql);
    $posts = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Fetch comments for each post
    foreach ($posts as &$post) {
        $post_id = $post['post_id'];
        $sql = "SELECT * FROM comments WHERE post_id = ?";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$post_id]);
        $post['comments'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    echo json_encode($posts);

} catch (PDOException $e) {
    echo json_encode(["success" => false, "message" => "Database error: " . $e->getMessage()]);
}
?>
