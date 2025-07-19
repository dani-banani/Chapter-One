<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$response = match ($method) {
    'GET' => getLibrary($conn, $_GET),
    'POST' => addToLibrary($conn, json_decode(file_get_contents('php://input'), true)),
    'PUT' => updateLibrary($conn, json_decode(file_get_contents('php://input'), true)),
    'DELETE' => deleteFromLibrary($conn, $_GET),
    default => http_response_code(405) && ['error' => 'Unsupported method']
};

echo json_encode($response);

function getLibrary($conn, $filters)
{
    $sql = "SELECT * FROM nv_user_library";
    $where = [];
    $values = [];
    $types = '';
    foreach ($filters as $key => $value) {
        if (in_array($key, ['nv_user_id', 'nv_novel_id'])) {
            $where[] = "$key = ?";
            $values[] = $value;
            $types .= 'i';
        }
    }
    if ($where) {
        $sql .= " WHERE " . implode(" AND ", $where);
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt)
        return ['error' => $conn->error];
    if ($values)
        $stmt->bind_param($types, ...$values);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: ['error' => 'No entries found'];
}

function addToLibrary($conn, $data)
{
    $required = ['nv_novel_id'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            return ['error' => "$field is required"];
        }
    }

    $novelId  = intval($data['nv_novel_id']);
    $chapter  = isset($data['nv_current_chapter']) ? intval($data['nv_current_chapter']) : null;
    $userId   = $_SESSION['user_id'] ?? $data['nv_user_id'] ?? null;

    if (!$userId) {
        http_response_code(401);
        return ['error' => 'User ID is required'];
    }
    $checkStmt = $conn->prepare("SELECT 1 FROM nv_user_library WHERE nv_user_id = ? AND nv_novel_id = ?");
    $checkStmt->bind_param("ii", $userId, $novelId);
    $checkStmt->execute();
    if ($checkStmt->get_result()->num_rows > 0) {
        return ['error' => 'Entry already exists'];
    }

    $stmt = $conn->prepare("INSERT INTO nv_user_library (nv_user_id, nv_novel_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $novelId);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}

function updateLibrary($conn, $data) {
    if (empty($data['nv_user_library_id'])) {
        http_response_code(400);
        return ['error' => 'nv_user_id and nv_novel_id are required'];
    }
    $fields = [];
    $types = '';
    $values = [];
    if (isset($data['nv_current_chapter'])) {
        $fields[] = "nv_current_chapter = ?";
        $values[] = $data['nv_current_chapter'];
        $types .= 'i';
    }

    if (empty($fields)) return ['error' => 'No fields to update'];

    $types .= 'i';
    $values[] = $data['nv_user_library_id'];

    $stmt = $conn->prepare("UPDATE nv_user_library SET " . implode(', ', $fields) . " WHERE nv_user_library_id = ?");
    $stmt->bind_param($types, ...$values);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}

function deleteFromLibrary($conn, $params) {
    if (empty($params['nv_user_library_id'])) {
        http_response_code(400);
        return ['error' => 'nv_user_id and nv_novel_id are required'];
    }
    $stmt = $conn->prepare("DELETE FROM nv_user_library WHERE nv_user_id = ? AND nv_novel_id = ?");
    $stmt->bind_param("ii", $params['nv_user_id'], $params['nv_novel_id']);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}