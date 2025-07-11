<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

if (!isset($_SESSION['author_id']) && isset($_COOKIE['author_login_token'])) {
    $token = $_COOKIE['author_login_token'];

    $stmt = $conn->prepare("SELECT nv_author_id FROM nv_author WHERE MD5(CONCAT(nv_author_id, nv_author_email)) = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($author = $result->fetch_assoc()) {
        $_SESSION['author_id'] = $author['nv_author_id'];
        echo json_encode(['success' => true, 'author_id' => $author['nv_author_id'], 'restored' => true]);
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

$stmt = $conn->prepare("SELECT nv_author_id, nv_author_email, nv_author_password FROM nv_author WHERE (nv_author_username = ? OR nv_author_email = ?) LIMIT 1");
$stmt->bind_param('ss', $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows === 0) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

$author = $result->fetch_assoc();
if (!password_verify($password, $author['nv_author_password'])) {
    http_response_code(401);
    echo json_encode(['error' => 'Invalid credentials']);
    exit;
}

$_SESSION['author_id'] = $author['nv_author_id'];
$token = md5($author['nv_author_id'] . $author['nv_author_email']);
setcookie('author_login_token', $token, time() + (86400 * 30), "/", "", isset($_SERVER['HTTPS']), true);

echo json_encode(['success' => true, 'author_id' => $author['nv_author_id']]);
