<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

function sanitize_html($html) {
    static $purifier = null;
    if (!$purifier) {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
    }
    return $purifier->purify($html);
}

$method = $_SERVER['REQUEST_METHOD'];
$response = match ($method) {
    'GET'    => getNovel($conn),
    'POST'   => createNovel($conn, json_decode(file_get_contents('php://input'), true)),
    'PUT'    => updateNovel($conn, json_decode(file_get_contents('php://input'), true)),
    'DELETE' => isset($_GET['nv_novel_id']) ? deleteNovel($conn, $_GET['nv_novel_id']) : ['error' => 'nv_novel_id is required'],
    default  => http_response_code(405) && ['error' => 'Unsupported method']
};

echo json_encode($response);
function getNovel($conn) {
    $query = "SELECT DISTINCT n.* FROM nv_novel n";
    $joinGenre = false;
    $conditions = [];
    $params = [];
    $types = '';
    foreach ($_GET as $key => $value) {
        if ($key === 'genre_id') {
            $joinGenre = true;
            $conditions[] = "m.nv_genre_id = ?";
            $params[] = intval($value);
            $types .= 'i';
        } elseif (strpos($key, 'nv_novel_') === 0) {
            $column = "n.$key";
            $conditions[] = "$column = ?";
            $params[] = is_numeric($value) ? intval($value) : $value;
            $types .= is_numeric($value) ? 'i' : 's';
        }
    }
    if ($joinGenre) {
        $query .= " JOIN nv_novel_genre_mapping m ON n.nv_novel_id = m.nv_novel_id";
    }
    if (!empty($conditions)) {
        $query .= ' WHERE ' . implode(' AND ', $conditions);
    }
    $query .= " ORDER BY n.nv_novel_id DESC";
    $stmt = $conn->prepare($query);
    if (!$stmt) return ['error' => $conn->error];
    if (!empty($params)) {
        $stmt->bind_param($types, ...$params);
    }
    $stmt->execute();
    $result = $stmt->get_result();
    if (!$result) {
        return ['error' => $stmt->error];
    }
    return $result->fetch_all(MYSQLI_ASSOC);
}

function createNovel($conn, $data) {
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }
    $required = ['nv_novel_title', 'nv_novel_description'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            return ['error' => "$field is required"];
        }
    }
    $authorId = $_SESSION['author_id'];
    $now = date('Y-m-d H:i:s');
    $columns = [
        'nv_author_id' => $authorId,
        'nv_novel_publish_date' => $now,
        'nv_novel_title' => sanitize_html($data['nv_novel_title']),
        'nv_novel_description' => sanitize_html($data['nv_novel_description']),
    ];
    foreach ($data as $key => $val) {
        if (strpos($key, 'nv_novel_') === 0 && !isset($columns[$key]) && $key !== 'nv_novel_id') {
            $columns[$key] = sanitize_html($val);
        }
    }
    $colNames = implode(', ', array_keys($columns));
    $placeholders = implode(', ', array_fill(0, count($columns), '?'));
    $types = str_repeat('s', count($columns));
    $values = array_values($columns);
    $stmt = $conn->prepare("INSERT INTO nv_novel ($colNames) VALUES ($placeholders)");
    if (!$stmt) return ['error' => $conn->error];
    $stmt->bind_param($types, ...$values);
    return $stmt->execute()
        ? ['success' => true, 'id' => $stmt->insert_id]
        : ['error' => $stmt->error];
}

function updateNovel($conn, $data) {
    if (!isset($data['nv_novel_id'])) {
        http_response_code(400);
        return ['error' => 'nv_novel_id is required'];
    }
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }
    $id = $data['nv_novel_id'];
    unset($data['nv_novel_id']);
    $check = $conn->prepare("SELECT nv_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Forbidden'];
    }
    $updates = [];
    $types = '';
    $values = [];
    foreach ($data as $key => $val) {
        if (strpos($key, 'nv_novel_') === 0) {
            $updates[] = "$key = ?";
            $types .= 's';
            $values[] = sanitize_html($val);
        }
    }
    if (empty($updates)) {
        http_response_code(400);
        return ['error' => 'No fields to update'];
    }
    $types .= 'i';
    $values[] = $id;
    $sql = "UPDATE nv_novel SET " . implode(', ', $updates) . " WHERE nv_novel_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) return ['error' => $conn->error];
    $stmt->bind_param($types, ...$values);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}

function deleteNovel($conn, $id) {
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }
    $check = $conn->prepare("SELECT nv_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $id);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Forbidden'];
    }
    $stmt = $conn->prepare("DELETE FROM nv_novel WHERE nv_novel_id = ?");
    $stmt->bind_param("i", $id);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}
