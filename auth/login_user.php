<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        http_response_code(405);
        echo json_encode(['error' => 'Method Not Allowed']);
        exit;
    }

    $isJson = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
    $data = $isJson ? json_decode(file_get_contents('php://input'), true) : $_POST;

    $identifier = trim($data['identifier'] ?? '');
    $password = $data['password'] ?? '';

    if ($identifier === '' || $password === '') {
        http_response_code(400);
        echo json_encode(['error' => 'Identifier and password required']);
        exit;
    }

    $stmt = $conn->prepare("SELECT nv_user_id, nv_user_email, nv_user_password FROM nv_user WHERE nv_user_email = ? LIMIT 1");
    $stmt->bind_param('s', $identifier);
    $stmt->execute();
    $result = $stmt->get_result();

    if (!$result || $result->num_rows === 0) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    $user = $result->fetch_assoc();
    if (!password_verify($password, $user['nv_user_password'])) {
        http_response_code(401);
        echo json_encode(['error' => 'Invalid credentials']);
        exit;
    }

    $_SESSION['user_id'] = $user['nv_user_id'];

    $token = md5($user['nv_user_id'] . $user['nv_user_email']);
    setcookie('user_login_token', $token, time() + (30 * 24 * 60 * 60), '/');

    echo json_encode(['success' => true, 'user_id' => $user['nv_user_id']]);

} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
