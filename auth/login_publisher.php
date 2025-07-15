<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['publisher_id']) && isset($_COOKIE['publisher_login_token'])) {
    $token = $_COOKIE['publisher_login_token'];

    $stmt = $conn->prepare("SELECT nv_publisher_id FROM nv_publisher WHERE MD5(CONCAT(nv_publisher_id, nv_publisher_email)) = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($publisher = $result->fetch_assoc()) {
        $_SESSION['publisher_id'] = $publisher['nv_publisher_id'];
        echo json_encode(['success' => true, 'publisher_id' => $publisher['nv_publisher_id'], 'restored' => true]);
        exit;
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method Not Allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);
$identifier = trim($data['identifier'] ?? '');
$password = $data['password'] ?? '';

if ($identifier === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'Identifier and password required']);
    exit;
}

$stmt = $conn->prepare("SELECT nv_publisher_id, nv_publisher_email, nv_publisher_password FROM nv_publisher WHERE (nv_publisher_name = ? OR nv_publisher_email = ?) LIMIT 1");
$stmt->bind_param('ss', $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

$publisher = $result->fetch_assoc();
if (!password_verify($password, $publisher['nv_publisher_password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

$_SESSION['publisher_id'] = $publisher['nv_publisher_id'];
$_SESSION['user_role'] = 'publisher';
$token = md5($publisher['nv_publisher_id'] . $publisher['nv_publisher_email']);
setcookie('publisher_login_token', $token, time() + (86400 * 30), "/", "", isset($_SERVER['HTTPS']), true);

echo json_encode(['success' => true, 'publisher_id' => $publisher['nv_publisher_id']]);
