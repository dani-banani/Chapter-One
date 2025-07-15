<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

$response = match ($method) {
    'POST' => registerPublisher($conn, json_decode(file_get_contents('php://input'), true)),
    'GET' => getCurrentPublisher(),
    default => http_response_code(405) && ['error' => 'Unsupported method']
};

echo json_encode($response);

function registerPublisher($conn, $data) {
    $required = ['nv_publisher_name', 'nv_publisher_email', 'nv_publisher_password'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            return ['error' => "$field is required"];
        }
    }

    $email = $data['nv_publisher_email'];
    $name = $data['nv_publisher_name'];
    $password = password_hash($data['nv_publisher_password'], PASSWORD_DEFAULT);
    $check = $conn->prepare("SELECT nv_publisher_id FROM nv_publisher WHERE nv_publisher_email = ?");
    $check->bind_param("s", $email);
    $check->execute();
    if ($check->get_result()->num_rows > 0) {
        http_response_code(409);
        return ['error' => 'Email already registered'];
    }

    $stmt = $conn->prepare("INSERT INTO nv_publisher (nv_publisher_name, nv_publisher_email, nv_publisher_password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $name, $email, $password);

    if ($stmt->execute()) {
        $_SESSION['publisher_id'] = $stmt->insert_id;
        return ['success' => true, 'nv_publisher_id' => $stmt->insert_id];
    }

    http_response_code(500);
    return ['error' => 'Registration failed'];
}

function getCurrentPublisher() {
    if (!isset($_SESSION['publisher_id'])) {
        http_response_code(401);
        return ['error' => 'Not logged in as publisher'];
    }
    return ['nv_publisher_id' => $_SESSION['publisher_id']];
}
