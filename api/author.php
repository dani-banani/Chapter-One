<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$response = [];

switch ($method) {
    case 'GET':
        $response = isset($_GET['id']) ? getAuthor($conn, $_GET['id']) : getAuthors($conn);
        break;
    case 'POST':
        $response = createAuthor($conn, json_decode(file_get_contents('php://input'), true));
        break;
    case 'PUT':
        $response = updateAuthor($conn, json_decode(file_get_contents('php://input'), true));
        break;
    case 'DELETE':
        $response = isset($_GET['id']) ? deleteAuthor($conn, $_GET['id']) : ['error' => 'Author ID required'];
        break;
    default:
        http_response_code(405);
        $response = ['error' => 'Unsupported method'];
}

echo json_encode($response);

function getAuthors($conn) {
    $result = $conn->query("SELECT * FROM nv_author");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : ['error' => $conn->error];
}

function getAuthor($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM nv_author WHERE nv_author_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: ['error' => 'Author not found'];
}

function createAuthor($conn, $data) {
    if (!isset($data['email'], $data['username'], $data['password'])) {
        http_response_code(400);
        return ['error' => 'Email, username, and password are required'];
    }
    $email = $data['email'];
    $username = $data['username'];
    $stmt = $conn->prepare("SELECT 1 FROM nv_author WHERE nv_author_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        return ['error' => 'Email is already taken'];
    }
    $stmt = $conn->prepare("SELECT 1 FROM nv_author WHERE nv_author_username = ?");
    $stmt->bind_param("s", $username);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        return ['error' => 'Username is already taken'];
    }
    $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO nv_author (nv_author_email, nv_author_username, nv_author_password) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $email, $username, $hashed);
    return $stmt->execute() 
        ? ['success' => true, 'id' => $stmt->insert_id]
        : ['error' => $stmt->error];
}

function updateAuthor($conn, $data) {
    if (!isset($data['id'], $data['email'], $data['username'])) {
        http_response_code(400);
        return ['error' => 'ID, email, and username are required'];
    }
    $id = $data['id'];
    $email = $data['email'];
    $username = $data['username'];
    $stmt = $conn->prepare("SELECT 1 FROM nv_author WHERE nv_author_email = ? AND nv_author_id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        return ['error' => 'Email is already taken'];
    }
    $stmt = $conn->prepare("SELECT 1 FROM nv_author WHERE nv_author_username = ? AND nv_author_id != ?");
    $stmt->bind_param("si", $username, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        return ['error' => 'Username is already taken'];
    }
    if (!empty($data['password'])) {
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE nv_author SET nv_author_email = ?, nv_author_username = ?, nv_author_password = ? WHERE nv_author_id = ?");
        $stmt->bind_param("sssi", $email, $username, $hashed, $id);
    } else {
        $stmt = $conn->prepare("UPDATE nv_author SET nv_author_email = ?, nv_author_username = ? WHERE nv_author_id = ?");
        $stmt->bind_param("ssi", $email, $username, $id);
    }

    return $stmt->execute() ? ['success' => true] : ['error' => $stmt->error];
}

function deleteAuthor($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM nv_author WHERE nv_author_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute() ? ['success' => true] : ['error' => $stmt->error];
}
