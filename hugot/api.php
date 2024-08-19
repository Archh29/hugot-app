<?php
include "connection.php";

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: POST, GET, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

try {
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $action = $data['action'];
        $user_id = $data['user_id'] ?? null; // Handle possible undefined index

        if ($action === 'addPost') {
            $content = $data['content'];

            // Check if user_id exists
            $checkUserSql = "SELECT * FROM users WHERE user_id = :user_id";
            $checkUserStmt = $conn->prepare($checkUserSql);
            $checkUserStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $checkUserStmt->execute();

            if ($checkUserStmt->rowCount() == 0) {
                echo json_encode(["success" => false, "message" => "User ID does not exist."]);
                exit;
            }

            $sql = "INSERT INTO posts (user_id, content) VALUES (:user_id, :content)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':content', $content, PDO::PARAM_STR);

            if ($stmt->execute()) {
                $post_id = $conn->lastInsertId();

                // Fetch user information
                $userSql = "SELECT first_name, last_name FROM users WHERE user_id = :user_id";
                $userStmt = $conn->prepare($userSql);
                $userStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
                $userStmt->execute();
                $user = $userStmt->fetch(PDO::FETCH_ASSOC);

                echo json_encode([
                    "success" => true,
                    "post_id" => $post_id,
                    "user_id" => $user_id,
                    "first_name" => $user['first_name'],
                    "last_name" => $user['last_name']
                ]);
            } else {
                $errorInfo = $stmt->errorInfo();
                echo json_encode(["success" => false, "message" => "Failed to add post. Error: " . implode(", ", $errorInfo)]);
            }
        } elseif ($action === 'addComment') {
            $post_id = $data['post_id'];
            $comment_text = $data['comment_text'];

            // Fetch user information
            $userSql = "SELECT first_name, last_name FROM users WHERE user_id = :user_id";
            $userStmt = $conn->prepare($userSql);
            $userStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $userStmt->execute();
            $user = $userStmt->fetch(PDO::FETCH_ASSOC);

            $sql = "INSERT INTO comments (post_id, user_id, comment_text) VALUES (:post_id, :user_id, :comment_text)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $stmt->bindParam(':comment_text', $comment_text, PDO::PARAM_STR);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "comment_id" => $conn->lastInsertId(),
                    "comment_first_name" => $user['first_name'],
                    "comment_last_name" => $user['last_name']
                ]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to add comment."]);
            }
        } elseif ($action === 'likePost') {
            $post_id = $data['post_id'];

            // Check if the user has already liked the post
            $checkLikeSql = "SELECT * FROM likes WHERE post_id = :post_id AND user_id = :user_id";
            $checkLikeStmt = $conn->prepare($checkLikeSql);
            $checkLikeStmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $checkLikeStmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);
            $checkLikeStmt->execute();

            if ($checkLikeStmt->rowCount() > 0) {
                echo json_encode(["success" => false, "message" => "User has already liked this post."]);
                exit;
            }

            // Add the like
            $sql = "INSERT INTO likes (post_id, user_id) VALUES (:post_id, :user_id)";
            $stmt = $conn->prepare($sql);
            $stmt->bindParam(':post_id', $post_id, PDO::PARAM_INT);
            $stmt->bindParam(':user_id', $user_id, PDO::PARAM_INT);

            if ($stmt->execute()) {
                echo json_encode(["success" => true]);
            } else {
                echo json_encode(["success" => false, "message" => "Failed to add like."]);
            }
        }
    } elseif ($_SERVER['REQUEST_METHOD'] === 'GET') {
        $sql = "SELECT 
                    p.post_id, 
                    p.user_id AS post_user_id, 
                    p.content AS post_content, 
                    p.likes, 
                    p.created_at AS post_created_at, 
                    c.comment_id, 
                    c.comment_text, 
                    c.created_at AS comment_created_at,
                    pu.first_name AS post_first_name,
                    pu.last_name AS post_last_name,
                    cu.first_name AS comment_first_name,
                    cu.last_name AS comment_last_name
                FROM 
                    posts p
                JOIN 
                    users pu ON p.user_id = pu.user_id
                LEFT JOIN 
                    comments c ON p.post_id = c.post_id 
                LEFT JOIN 
                    users cu ON c.user_id = cu.user_id
                ORDER BY 
                    p.created_at DESC, c.created_at ASC";

        $stmt = $conn->prepare($sql);
        $stmt->execute();
        $results = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $posts = [];

        foreach ($results as $row) {
            $post_id = $row['post_id'];
            if (!isset($posts[$post_id])) {
                $posts[$post_id] = [
                    'post_id' => $row['post_id'],
                    'user_id' => $row['post_user_id'],
                    'first_name' => $row['post_first_name'],
                    'last_name' => $row['post_last_name'],
                    'content' => $row['post_content'],
                    'likes' => $row['likes'],
                    'created_at' => $row['post_created_at'], // Added post created_at
                    'comments' => []
                ];
            }
            if ($row['comment_id']) {
                $posts[$post_id]['comments'][] = [
                    'comment_id' => $row['comment_id'],
                    'comment_text' => $row['comment_text'],
                    'created_at' => $row['comment_created_at'], // Added comment created_at
                    'comment_first_name' => $row['comment_first_name'],
                    'comment_last_name' => $row['comment_last_name']
                ];
            }
        }

        echo json_encode(array_values($posts));
    } else {
        echo json_encode(["success" => false, "message" => "Invalid request method."]);
    }
} catch (Exception $e) {
    echo json_encode(["success" => false, "message" => $e->getMessage()]);
}
    