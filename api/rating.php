<?php
require_once '../config/db.php';
header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$novelId = $_GET['nv_novel_id'] ?? null;

try {
    if ($novelId) {
        $stmt = $conn->prepare("SELECT * FROM view_novel_avg_rating WHERE nv_novel_id = ?");
        $stmt->bind_param("i", $novelId);
    } else {
        $stmt = $conn->prepare("SELECT * FROM view_novel_avg_rating");
    }

    $stmt->execute();
    $result = $stmt->get_result();
    $data = [];

    while ($row = $result->fetch_assoc()) {
        $data[] = $row;
    }

    echo json_encode($data);
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode(['error' => 'Server error: ' . $e->getMessage()]);
}
?>