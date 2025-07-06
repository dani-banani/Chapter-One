<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$response = [];

switch ($method) {
    case 'GET':
        $response = isset($_GET['id']) ? getNovel($conn, $_GET['id']) : getNovels($conn);
        break;
    case 'POST':
        $response = createNovel($conn, json_decode(file_get_contents('php://input'), true));
        break;
    case 'PUT':
        $response = updateNovel($conn, json_decode(file_get_contents('php://input'), true));
        break;
    case 'DELETE':
        $response = isset($_GET['id']) ? deleteNovel($conn, $_GET['id']) : ['error' => 'Novel ID required'];
        break;
    default:
        http_response_code(405);
        $response = ['error' => 'Unsupported method'];
}

echo json_encode($response);

function getNovels($conn) {
    $result = $conn->query("SELECT * FROM nv_novel");
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : ['error' => $conn->error];
}

function getNovel($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM nv_novel WHERE nv_novel_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    return $stmt->get_result()->fetch_assoc() ?: ['error' => 'Novel not found'];
}

function createNovel($conn, $data) {
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }
    $fields = ['nv_novel_title', 'nv_novel_description'];
    foreach ($fields as $field) {
        if (!isset($data[$field])) {
            http_response_code(400);
            return ['error' => "$field is required"];
        }
    }
    $authorId = $_SESSION['author_id'];
    $columns = ['nv_novel_author_id' => $authorId, 'nv_novel_publish_date' => date('Y-m-d H:i:s')];
    foreach ($data as $key => $val) {
        if (strpos($key, 'nv_novel_') === 0 && $key !== 'nv_novel_id') {
            $columns[$key] = $val;
        }
    }

    $colNames = implode(', ', array_keys($columns));
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $types = str_repeat('s', count($columns));
    $values = array_values($columns);
    $stmt = $conn->prepare("INSERT INTO nv_novel ($colNames) VALUES ($placeholders)");
    $stmt->bind_param($types, ...$values);
    return $stmt->execute() ? ['success' => true, 'id' => $stmt->insert_id] : ['error' => $stmt->error];
}

function updateNovel($conn, $data) {
    if (!isset($data['nv_novel_id'])) {
        http_response_code(400);
        return ['error' => 'nv_novel_id is required'];
    }
    $id = $data['nv_novel_id'];
    unset($data['nv_novel_id']);
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }
    $check = $conn->prepare("SELECT nv_novel_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_novel_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Forbidden'];
    }
    $set = [];
    $types = '';
    $values = [];
    foreach ($data as $key => $val) {
        if (strpos($key, 'nv_novel_') === 0) {
            $set[] = "$key = ?";
            $types .= 's';
            $values[] = $val;
        }
    }
    if (empty($set)) {
        http_response_code(400);
        return ['error' => 'No fields to update'];
    }
    $sql = "UPDATE nv_novel SET " . implode(', ', $set) . " WHERE nv_novel_id = ?";
    $types .= 'i';
    $values[] = $id;
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    return $stmt->execute() ? ['success' => true] : ['error' => $stmt->error];
}

function deleteNovel($conn, $id) {
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }
    $check = $conn->prepare("SELECT nv_novel_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_novel_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Forbidden'];
    }
    $stmt = $conn->prepare("DELETE FROM nv_novel WHERE nv_novel_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute() ? ['success' => true] : ['error' => $stmt->error];
}
