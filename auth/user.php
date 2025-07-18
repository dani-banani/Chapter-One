<?php
session_start();
require_once __DIR__ . '/../database_connection.php';

if (!isset($_SESSION['user_id']) && isset($_COOKIE['user_login_token'])) {
    $token = $_COOKIE['user_login_token'];

    $stmt = $conn->prepare("SELECT nv_user_id FROM nv_user WHERE MD5(CONCAT(nv_user_id, nv_user_email)) = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($user = $result->fetch_assoc()) {
        $_SESSION['user_id'] = $user['nv_user_id'];
    }
}

if (!isset($_SESSION['user_id'])) {
    header('Location: ../Chapter-One-main/Chapter-One-main/pages/login.php');
    exit;
}

$userId = $_SESSION['user_id'];
