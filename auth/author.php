<?php
session_start();
require_once __DIR__ . '/../paths.php';
require_once __DIR__ . '/../database_connection.php';

if (!isset($_SESSION['author_id']) && isset($_COOKIE['author_login_token'])) {
    $token = $_COOKIE['author_login_token'];

    $stmt = $conn->prepare("SELECT nv_author_id FROM nv_author WHERE MD5(CONCAT(nv_author_id, nv_author_email)) = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($author = $result->fetch_assoc()) {
        $_SESSION['author_id'] = $author['nv_author_id'];
        $_SESSION['author_username'] = $author['nv_author_username'];
    }
}
if (!isset($_SESSION['author_id'])) {
    header('Location: ' . LOGIN_PAGE);
    exit;
}

$authorId = $_SESSION['author_id'];
$authorUsername = $_SESSION['author_username'] ?? "Author"; 
?>
