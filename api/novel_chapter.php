<?php
require_once 'config.php';

header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        getChapters();
        break;
    case 'POST':
        createChapter();
        break;
    case 'PUT':
        updateChapter();
        break;
    case 'DELETE':
        deleteChapter();
        break;
    default:
        echo json_encode(['error' => 'Invalid request method']);
        break;
}

function getChapters()
{
    global $conn;
    $novel_id = $_GET['nv_novel_id'] ?? null;
    if (!$novel_id) {
        echo json_encode(['error' => 'nv_novel_id is required']);
        return;
    }
    $stmt = $conn->prepare("SELECT * FROM nv_novel_chapter WHERE nv_novel_id = ?");
    $stmt->bind_param("i", $novel_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $chapters = [];
    while ($row = $result->fetch_assoc()) {
        $chapters[] = $row;
    }
    echo json_encode($chapters);
}

function createChapter()
{
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    $required = ['nv_novel_id', 'nv_novel_chapter_title', 'nv_novel_chapter_content', 'nv_novel_chapter_number', 'nv_novel_chapter_published_date', 'nv_novel_chapter_status'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            echo json_encode(['error' => "$field is required"]);
            return;
        }
    }
    $stmt = $conn->prepare("INSERT INTO nv_novel_chapter (nv_novel_id, nv_novel_chapter_title, nv_novel_chapter_content, nv_novel_chapter_number, nv_novel_chapter_published_date, nv_novel_chapter_status) VALUES (?, ?, ?, ?, ?, ?)");
    $stmt->bind_param(
        "ississ",
        $data['nv_novel_id'],
        $data['nv_novel_chapter_title'],
        $data['nv_novel_chapter_content'],
        $data['nv_novel_chapter_number'],
        $data['nv_novel_chapter_published_date'],
        $data['nv_novel_chapter_status']
    );
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Chapter created successfully']);
    } else {
        echo json_encode(['error' => 'Failed to create chapter']);
    }
}

function updateChapter()
{
    global $conn;
    $data = json_decode(file_get_contents('php://input'), true);
    if (empty($data['nv_novel_chapter_id'])) {
        echo json_encode(['error' => 'nv_novel_chapter_id is required']);
        return;
    }
    $stmt = $conn->prepare("UPDATE nv_novel_chapter SET nv_novel_chapter_title = ?, nv_novel_chapter_content = ?, nv_novel_chapter_number = ?, nv_novel_chapter_published_date = ?, nv_novel_chapter_status = ? WHERE nv_novel_chapter_id = ?");
    $stmt->bind_param(
        "ssissi",
        $data['nv_novel_chapter_title'],
        $data['nv_novel_chapter_content'],
        $data['nv_novel_chapter_number'],
        $data['nv_novel_chapter_published_date'],
        $data['nv_novel_chapter_status'],
        $data['nv_novel_chapter_id']
    );
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Chapter updated successfully']);
    } else {
        echo json_encode(['error' => 'Failed to update chapter']);
    }
}

function deleteChapter()
{
    global $conn;
    $chapter_id = $_GET['nv_novel_chapter_id'] ?? null;
    if (!$chapter_id) {
        echo json_encode(['error' => 'nv_novel_chapter_id is required']);
        return;
    }
    $stmt = $conn->prepare("DELETE FROM nv_novel_chapter WHERE nv_novel_chapter_id = ?");
    $stmt->bind_param("i", $chapter_id);
    if ($stmt->execute()) {
        echo json_encode(['message' => 'Chapter deleted successfully']);
    } else {
        echo json_encode(['error' => 'Failed to delete chapter']);
    }
}
?>