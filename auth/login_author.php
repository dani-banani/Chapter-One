<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    exit;
}
$isJson = str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json');
$data = $isJson ? json_decode(file_get_contents('php://input'), true) : $_POST;
$identifier = trim($data['identifier'] ?? '');
$password = $data['password'] ?? '';
if ($identifier === '' || $password === '') {
    http_response_code(400);
    echo json_encode(['error' => 'identifier & password required']);
    exit;
}
$stmt = $conn->prepare("SELECT nv_author_id, nv_author_password FROM nv_author WHERE (nv_author_username = ? OR nv_author_email = ?) LIMIT 1");
$stmt->bind_param('ss', $identifier, $identifier);
$stmt->execute();
$result = $stmt->get_result();
if ($result->num_rows === 0) {
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
echo json_encode(['success' => true, 'author_id' => $author['nv_author_id']]);
?>