<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];

switch ($method) {
    case 'GET':
        if (isset($_GET['list'])) {
            $result = $conn->query("SELECT * FROM nv_novel_genre_db ORDER BY nv_genre_name ASC");
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));

        } elseif (isset($_GET['novel_id'])) {
            $novelId = intval($_GET['novel_id']);
            $stmt = $conn->prepare("
                SELECT g.nv_genre_id, g.nv_genre_name
                FROM nv_novel_genre_mapping m
                join nv_novel_genre_db g ON m.nv_genre_id = g.nv_genre_id
                WHERE m.nv_novel_id = ?
            ");
            $stmt->bind_param("i", $novelId);
            $stmt->execute();
            $res = $stmt->get_result();
            echo json_encode($res->fetch_all(MYSQLI_ASSOC));

        } elseif (isset($_GET['all'])) {
            $result = $conn->query("SELECT * FROM nv_novel_genre_mapping");
            echo json_encode($result->fetch_all(MYSQLI_ASSOC));

        } elseif (isset($_GET['genre_id'])) {
            $genreId = intval($_GET['genre_id']);
            $stmt = $conn->prepare("
                SELECT n.* from nv_novel n join nv_novel_genre_mapping m on n.nv_novel_id = m.nv_novel_id WHERE nv_genre_id = ?");
            $stmt->bind_param("i", $genreId);
            $stmt->execute();
            $res = $stmt->get_result();
            echo json_encode($res->fetch_all(MYSQLI_ASSOC));

        } else {
            echo json_encode(['error' => 'Missing parameters']);
        }
        break;

    case 'POST':
        $data = json_decode(file_get_contents('php://input'), true);
        if (!isset($data['nv_novel_id'], $data['nv_genre_id'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Missing novel_id or genre_id']);
            exit;
        }

        $stmt = $conn->prepare("INSERT IGNORE INTO nv_novel_genre_mapping (nv_novel_id, nv_genre_id) VALUES (?, ?)");
        $stmt->bind_param("ii", $data['nv_novel_id'], $data['nv_genre_id']);
        $ok = $stmt->execute();
        echo json_encode($ok ? ['success' => true] : ['error' => $stmt->error]);
        break;

    case 'DELETE':
        if (isset($_GET['novel_id']) && isset($_GET['genre_id'])) {
            $stmt = $conn->prepare("DELETE FROM nv_novel_genre_mapping WHERE nv_novel_id = ? AND nv_genre_id = ?");
            $stmt->bind_param("ii", $_GET['novel_id'], $_GET['genre_id']);
            $ok = $stmt->execute();
            echo json_encode($ok ? ['success' => true] : ['error' => $stmt->error]);
        } elseif (isset($_GET['novel_id'])) {
            $stmt = $conn->prepare("DELETE FROM nv_novel_genre_mapping WHERE nv_novel_id = ?");
            $stmt->bind_param("i", $_GET['novel_id']);
            $ok = $stmt->execute();
            echo json_encode($ok ? ['success' => true] : ['error' => $stmt->error]);
        } else {
            http_response_code(400);
            echo json_encode(['error' => 'Missing novel_id']);
        }
        break;

    default:
        http_response_code(405);
        echo json_encode(['error' => 'Method not allowed']);
}
