<?php
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');
$method = $_SERVER['REQUEST_METHOD'];
switch ($method) {
    case 'GET':
        if (isset($_GET['id'])) {
            getAuthor($conn, $_GET['id']);
        } else {
            getAuthors($conn);
        }
        break;

    case 'POST':
        createAuthor($conn, json_decode(file_get_contents('php://input'), true));
        break;

    case 'PUT':
        updateAuthor($conn, json_decode(file_get_contents('php://input'), true));
        break;

    case 'DELETE':
        if (isset($_GET['id'])) {
            deleteAuthor($conn, $_GET['id']);
        } else {
            echo json_encode(['error' => 'Author ID required']);
        }
        break;

    default:
        echo json_encode(['error' => 'Unsupported method']);
}
function getAuthors($conn) {
    $result = $conn->query("SELECT * FROM nv_author");
    $authors = $result->fetch_all(MYSQLI_ASSOC);
    echo json_encode($authors);
}
function getAuthor($conn, $id) {
    $stmt = $conn->prepare("SELECT * FROM nv_author WHERE nv_author_id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    echo json_encode($result);
}
function createAuthor($conn, $data) {
    if (!is_array($data) || count($data) === 0) {
        echo json_encode(['error' => 'Invalid input']);
        return;
    }
    if (empty($data['nv_author_username']) || empty($data['nv_author_email']) || empty($data['nv_author_password'])) {
        echo json_encode(['error' => 'Username, email, and password are required']);
        return;
    }
    $check = $conn->prepare("SELECT COUNT(*) FROM nv_author WHERE nv_author_username = ?");
    $check->bind_param("s", $data['nv_author_username']);
    $check->execute();
    $check_result = $check->get_result();
    $count_row = $check_result->fetch_row();
    $count = $count_row[0];
    $check->close();
    if ($count > 0) {
        echo json_encode(['error' => 'Username already exists']);
        return;
    }
    $data['nv_author_password'] = password_hash($data['nv_author_password'], PASSWORD_DEFAULT);
    $columns = array_keys($data);
    $placeholders = array_fill(0, count($columns), '?');
    $types = str_repeat('s', count($columns)); // all strings for now if update table then need change here too
    $sql = "INSERT INTO nv_author (" . implode(', ', $columns) . ") VALUES (" . implode(', ', $placeholders) . ")";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => $conn->error]);
        return;
    }
    $stmt->bind_param($types, ...array_values($data));
    if ($stmt->execute()) {
        echo json_encode(['success' => true, 'id' => $stmt->insert_id]);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }
}
function updateAuthor($conn, $data) {
    if (!isset($data['nv_author_id'])) {
        echo json_encode(['error' => 'nv_author_id is required']);
        return;
    }
    $id = $data['nv_author_id'];
    unset($data['nv_author_id']);
    if (count($data) === 0) {
        echo json_encode(['error' => 'No fields to update']);
        return;
    }
    if (isset($data['nv_author_username'])) {
        $check = $conn->prepare("SELECT COUNT(*) FROM nv_author WHERE nv_author_username = ? AND nv_author_id != ?");
        $check->bind_param("si", $data['nv_author_username'], $id);
        $check->execute();
        $check_result = $check->get_result();
        $count_row = $check_result->fetch_row();
        $count = $count_row[0];
        $check->close();

        if ($count > 0) {
            echo json_encode(['error' => 'Username already exists']);
            return;
        }
    }
    if (isset($data['nv_author_password'])) {
        $data['nv_author_password'] = password_hash($data['nv_author_password'], PASSWORD_DEFAULT);
    }
    $columns = array_keys($data);
    $setClause = implode(', ', array_map(fn($col) => "$col = ?", $columns));
    $types = str_repeat('s', count($columns)) . 'i';
    $sql = "UPDATE nv_author SET $setClause WHERE nv_author_id = ?";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        echo json_encode(['error' => $conn->error]);
        return;
    }
    $values = array_merge(array_values($data), [$id]);
    $stmt->bind_param($types, ...$values);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }
}
function deleteAuthor($conn, $id) {
    $stmt = $conn->prepare("DELETE FROM nv_author WHERE nv_author_id = ?");
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['error' => $stmt->error]);
    }
}
?>
