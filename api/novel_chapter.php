<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
require_once __DIR__ . '/../vendor/autoload.php';
header('Content-Type: application/json');

function sanitize_html($html)
{
    static $purifier = null;
    if (!$purifier) {
        $config = HTMLPurifier_Config::createDefault();
        $purifier = new HTMLPurifier($config);
    }
    return $purifier->purify($html);
}

$method = $_SERVER['REQUEST_METHOD'];
$response = match ($method) {
    'GET' => getChapters($conn, $_GET),
    'POST' => createChapter($conn, json_decode(file_get_contents('php://input'), true)),
    'PUT' => updateChapter($conn, json_decode(file_get_contents('php://input'), true)),
    'DELETE' => deleteChapter($conn, $_GET),
    default => http_response_code(405) && ['error' => 'Unsupported method']
};
echo json_encode($response);


function getChapters($conn, $filters)
{
    $sql = "SELECT * FROM nv_novel_chapter";
    $values = [];
    $types = [];
    if (!empty($filters)) {
        $where = [];
        foreach ($filters as $key => $value) {
            if (strpos($key, 'nv_') === 0) {
                $where[] = "$key = ?";
                $values[] = $value;
                $types[] = is_numeric($value) ? 'i' : 's';
            }
        }
        if ($where) {
            $sql .= " WHERE " . implode(" AND ", $where);
        }
    }
    $sql .= " ORDER BY nv_novel_id ASC, nv_novel_chapter_number ASC";
    $stmt = $conn->prepare($sql);
    if ($values) {
        $stmt->bind_param(implode('', $types), ...$values);
    }
    $stmt->execute();
    $res = $stmt->get_result();
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : ['error' => $stmt->error];
}

function createChapter($conn, $data)
{
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }
    $status = 'draft';
    $required = ['nv_novel_id', 'nv_novel_chapter_title', 'nv_novel_chapter_content'];
    foreach ($required as $field) {
        if (!isset($data[$field])) {
            http_response_code(response_code: 400);
            return ['error' => "$field is required"];
        }
    }
    $check = $conn->prepare("SELECT nv_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $data['nv_novel_id']);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Unauthorized to add chapters to this novel'];
    }
    $stmt = $conn->prepare("SELECT MAX(nv_novel_chapter_number) as max_number FROM nv_novel_chapter WHERE nv_novel_id = ?");
    $stmt->bind_param("i", $data['nv_novel_id']);
    $stmt->execute();
    $maxResult = $stmt->get_result()->fetch_assoc();
    $newChapterNumber = ($maxResult['max_number'] ?? 0) + 1;
    $insert = $conn->prepare("INSERT INTO nv_novel_chapter (nv_novel_chapter_content, nv_novel_chapter_title, nv_novel_chapter_number, nv_novel_id, nv_novel_chapter_status) VALUES (?, ?, ?, ?, ?)");
    $content = sanitize_html($data['nv_novel_chapter_content']);
    $title = sanitize_html($data['nv_novel_chapter_title']);
    $insert->bind_param("ssiis", $content, $title, $newChapterNumber, $data['nv_novel_id'], $status);
    return $insert->execute() ? ['success' => true, 'chapter_number' => $newChapterNumber] : ['error' => $insert->error];
}


function updateChapter($conn, $data)
{
    if (!isset($data['nv_novel_id'], $data['nv_novel_chapter_number'])) {
        http_response_code(400);
        return ['error' => 'Novel ID and Chapter Number required'];
    }
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }
    $check = $conn->prepare("SELECT nv_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $data['nv_novel_id']);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Unauthorized'];
    }

    $fields = [];
    $values = [];
    $types = '';
    foreach (['nv_novel_chapter_content', 'nv_novel_chapter_title', 'nv_novel_chapter_status'] as $field) {
        if (isset($data[$field])) {
            $fields[] = "$field = ?";
            $types .= 's';
            $values[] = sanitize_html($data[$field]);
        }
    }

    if (empty($fields)) {
        return ['error' => 'No data to update'];
    }
    $types .= 'ii';
    $values[] = $data['nv_novel_id'];
    $values[] = $data['nv_novel_chapter_number'];
    $sql = "UPDATE nv_novel_chapter SET " . implode(", ", $fields) . " WHERE nv_novel_id = ? AND nv_novel_chapter_number = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param($types, ...$values);
    return $stmt->execute() ? ['success' => true] : ['error' => $stmt->error];
}


function deleteChapter($conn, $get)
{
    if (!isset($_SESSION['author_id'], $get['nv_novel_id'], $get['nv_novel_chapter_number'])) {
        http_response_code(400);
        return ['error' => 'Missing required parameters'];
    }
    $novelId = $get['nv_novel_id'];
    $chapterNumber = $get['nv_novel_chapter_number'];
    $check = $conn->prepare("SELECT nv_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $novelId);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Unauthorized'];
    }
    $stmt = $conn->prepare("DELETE FROM nv_novel_chapter WHERE nv_novel_id = ? AND nv_novel_chapter_number = ?");
    $stmt->bind_param("ii", $novelId, $chapterNumber);
    if (!$stmt->execute()) {
        return ['error' => $stmt->error];
    }
    $shift = $conn->prepare("
        UPDATE nv_novel_chapter 
        SET nv_novel_chapter_number = nv_novel_chapter_number - 1 
        WHERE nv_novel_id = ? AND nv_novel_chapter_number > ?
    ");
    $shift->bind_param("ii", $novelId, $chapterNumber);
    $shift->execute();

    return ['success' => true];
}