<?php
session_start();
require_once __DIR__ . '/../database_connection.php';

if (!isset($_SESSION['publisher_id']) && isset($_COOKIE['publisher_login_token'])) {
    $token = $_COOKIE['publisher_login_token'];
    $stmt = $conn->prepare("SELECT nv_publisher_id FROM nv_publisher WHERE MD5(CONCAT(nv_publisher_id, nv_publisher_email)) = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($publisher = $result->fetch_assoc()) {
        $_SESSION['publisher_id'] = $publisher['nv_publisher_id'];
    }
}

if (!isset($_SESSION['publisher_id'])) {
    if (strpos($_SERVER['REQUEST_URI'], '/api/') !== false) {
        http_response_code(401);
        echo json_encode(['error' => 'Login required']);
        exit;
    } else {
        header('Location: ../../login/publisher_login.html');
        exit;
    }
}

$publisherId = $_SESSION['publisher_id'];
