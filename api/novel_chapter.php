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
    'GET'    => isset($_GET['novel_id']) ? getChaptersByNovel($conn, $_GET['novel_id']) : getChapters($conn),
    'POST'   => createChapter($conn, json_decode(file_get_contents('php://input'), true)),
    'PUT'    => updateChapter($conn, json_decode(file_get_contents('php://input'), true)),
    'DELETE' => isset($_GET['id']) ? deleteChapter($conn, $_GET['id']) : ['error' => 'Chapter ID required'],
    default  => http_response_code(405) && ['error' => 'Unsupported method']
};

echo json_encode($response);

function getChapters($conn) {
    $res = $conn->query("SELECT * FROM nv_novel_chapter");
    return $res ? $res->fetch_all(MYSQLI_ASSOC) : ['error' => $conn->error];
}

function getChaptersByNovel($conn, $novelId) {
    $stmt = $conn->prepare("SELECT * FROM nv_novel_chapter WHERE nv_novel_id = ? ORDER BY nv_novel_chapter_number ASC");
    $stmt->bind_param("i", $novelId);
    $stmt->execute();
    $result = $stmt->get_result();
    return $result ? $result->fetch_all(MYSQLI_ASSOC) : ['error' => 'Chapters not found'];
}

function createChapter($conn, $data) {
    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }

    $required = ['nv_novel_id', 'nv_novel_chapter_title', 'nv_novel_chapter_content', 'nv_novel_chapter_description', 'nv_novel_chapter_number'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            return ['error' => "$field is required"];
        }
    }

    $check = $conn->prepare("SELECT nv_novel_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $data['nv_novel_id']);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_novel_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Unauthorized to add chapters to this novel'];
    }

    $stmt = $conn->prepare("INSERT INTO nv_novel_chapter (nv_novel_chapter_content, nv_novel_chapter_title, nv_novel_chapter_description, nv_novel_chapter_number, nv_novel_id) VALUES (?, ?, ?, ?, ?)");
    $content = sanitize_html($data['nv_novel_chapter_content']);
    $title = sanitize_html($data['nv_novel_chapter_title']);
    $desc = sanitize_html($data['nv_novel_chapter_description']);
    $number = $data['nv_novel_chapter_number'];
    $novelId = $data['nv_novel_id'];
    $stmt->bind_param("sssii", $content, $title, $desc, $number, $novelId);

    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}

function updateChapter($conn, $data) {
    if (!isset($data['nv_novel_id'], $data['nv_novel_chapter_number'])) {
        http_response_code(400);
        return ['error' => 'Novel ID and Chapter Number required'];
    }

    if (!isset($_SESSION['author_id'])) {
        http_response_code(401);
        return ['error' => 'Login required'];
    }

    $check = $conn->prepare("SELECT nv_novel_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $data['nv_novel_id']);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_novel_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Unauthorized'];
    }

    $fields = [];
    $values = [];
    $types = '';

    foreach (['nv_novel_chapter_content', 'nv_novel_chapter_title', 'nv_novel_chapter_description'] as $field) {
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

    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}

function deleteChapter($conn, $chapterNumber) {
    if (!isset($_SESSION['author_id'], $_GET['novel_id'])) {
        http_response_code(400);
        return ['error' => 'Author session and novel ID required'];
    }

    $novelId = $_GET['novel_id'];

    $check = $conn->prepare("SELECT nv_novel_author_id FROM nv_novel WHERE nv_novel_id = ?");
    $check->bind_param("i", $novelId);
    $check->execute();
    $res = $check->get_result()->fetch_assoc();
    if (!$res || $res['nv_novel_author_id'] != $_SESSION['author_id']) {
        http_response_code(403);
        return ['error' => 'Unauthorized'];
    }

    $stmt = $conn->prepare("DELETE FROM nv_novel_chapter WHERE nv_novel_id = ? AND nv_novel_chapter_number = ?");
    $stmt->bind_param("ii", $novelId, $chapterNumber);

    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}
