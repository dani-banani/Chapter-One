<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$response = match ($method) {
    'GET'    => getLibrary($conn, $_GET),
    'POST'   => addToLibrary($conn, json_decode(file_get_contents('php://input'), true)),
    'DELETE' => deleteFromLibrary($conn, $_GET),
    default  => http_response_code(405) && ['error' => 'Unsupported method']
};

echo json_encode($response);
function getLibrary($conn, $filters) {
    $sql = "SELECT * FROM nv_user_library";
    $conditions = [];
    $values = [];
    $types = '';
    foreach (['nv_user_id', 'nv_novel_id'] as $key) {
        if (!empty($filters[$key])) {
            $conditions[] = "$key = ?";
            $values[] = $filters[$key];
            $types .= 'i';
        }
    }
    if ($conditions) {
        $sql .= " WHERE " . implode(" AND ", $conditions);
    }
    $stmt = $conn->prepare($sql);
    if (!$stmt) return ['error' => $conn->error];
    if ($values) $stmt->bind_param($types, ...$values);
    $stmt->execute();
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: ['error' => 'No entries found'];
}

function addToLibrary($conn, $data) {
    $userId = $_SESSION['user_id'] ?? $data['nv_user_id'] ?? null;
    $novelId = $data['nv_novel_id'] ?? null;
    if (!$userId || !$novelId) {
        http_response_code(400);
        return ['error' => 'nv_user_id and nv_novel_id are required'];
    }
    $stmt = $conn->prepare("INSERT IGNORE INTO nv_user_library (nv_user_id, nv_novel_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $userId, $novelId);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}

function deleteFromLibrary($conn, $params) {
    if (empty($params['nv_user_id']) || empty($params['nv_novel_id'])) {
        http_response_code(400);
        return ['error' => 'nv_user_id and nv_novel_id are required'];
    }
    $stmt = $conn->prepare("DELETE FROM nv_user_library WHERE nv_user_id = ? AND nv_novel_id = ?");
    $stmt->bind_param("ii", $params['nv_user_id'], $params['nv_novel_id']);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}
