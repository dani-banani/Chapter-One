<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$response = [];

switch ($method) {
    case 'GET':
        $response = isset($_GET['id']) ? getUser($conn, $_GET['id']) : getUsers($conn);
        break;
    case 'POST':
        $response = createUser($conn, json_decode(file_get_contents('php://input'), true));
        break;
    case 'PUT':
        $response = updateUser($conn, json_decode(file_get_contents('php://input'), true));
        break;
    case 'DELETE':
        $response = isset($_GET['id']) ? deleteUser($conn, $_GET['id']) : ['error' => 'User ID required'];
        break;
    default:
        http_response_code(405);
        $response = ['error' => 'Unsupported method'];
}

echo json_encode($response);

function getUsers($conn) {
    $res = $conn->query("SELECT nv_user_id, nv_user_email, nv_user_sub_tier, nv_user_created_date, nv_user_role FROM nv_user");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : ['error' => $conn->error];
}

function getUser($conn, $id) {
    $stmt = $conn->prepare("SELECT nv_user_id, nv_user_email, nv_user_sub_tier, nv_user_created_date, nv_user_role FROM nv_user WHERE nv_user_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: ['error' => 'User not found'];
}

function createUser($conn, $data) {
    if (!isset($data['email'], $data['password'])) {
        http_response_code(400);
        return ['error' => 'Email and password are required'];
    }

    $email = trim($data['email']);
    $role = $data['role'] ?? 'reader';
    $tier = (int)($data['sub_tier'] ?? 0);

    $stmt = $conn->prepare("SELECT 1 FROM nv_user WHERE nv_user_email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        return ['error' => 'Email already registered'];
    }

    $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
    $stmt = $conn->prepare("INSERT INTO nv_user (nv_user_email, nv_user_password, nv_user_sub_tier, nv_user_role) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("ssis", $email, $hashed, $tier, $role);

    return $stmt->execute()
        ? ['success' => true, 'id' => $stmt->insert_id]
        : ['error' => $stmt->error];
}

function updateUser($conn, $data) {
    if (!isset($data['id'], $data['email'])) {
        http_response_code(400);
        return ['error' => 'ID and email are required'];
    }

    $id = $data['id'];
    $email = trim($data['email']);
    $role = $data['role'] ?? null;
    $tier = isset($data['sub_tier']) ? (int)$data['sub_tier'] : null;

    $stmt = $conn->prepare("SELECT 1 FROM nv_user WHERE nv_user_email = ? AND nv_user_id != ?");
    $stmt->bind_param("si", $email, $id);
    $stmt->execute();
    if ($stmt->get_result()->num_rows > 0) {
        http_response_code(409);
        return ['error' => 'Email already in use by another user'];
    }

    if (!empty($data['password'])) {
        $hashed = password_hash($data['password'], PASSWORD_DEFAULT);
        $stmt = $conn->prepare("UPDATE nv_user SET nv_user_email = ?, nv_user_password = ?, nv_user_sub_tier = ?, nv_user_role = ? WHERE nv_user_id = ?");
        $stmt->bind_param("ssisi", $email, $hashed, $tier, $role, $id);
    } else {
        $stmt = $conn->prepare("UPDATE nv_user SET nv_user_email = ?, nv_user_sub_tier = ?, nv_user_role = ? WHERE nv_user_id = ?");
        $stmt->bind_param("sisi", $email, $tier, $role, $id);
    }

    return $stmt->execute() ? ['success' => true] : ['error' => $stmt->error];
}

function deleteUser($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM nv_user WHERE nv_user_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute() ? ['success' => true] : ['error' => $stmt->error];
}
