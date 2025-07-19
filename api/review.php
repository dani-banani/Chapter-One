<?php
session_start();
require_once __DIR__ . '/../database_connection.php';
header('Content-Type: application/json');

$method = $_SERVER['REQUEST_METHOD'];
$response = match ($method) {
    'GET' => getReviews($conn, $_GET),
    'POST' => createReview($conn, json_decode(file_get_contents('php://input'), true)),
    'PUT' => updateReview($conn, json_decode(file_get_contents('php://input'), true)),
    'DELETE' => deleteReview($conn, $_GET),
    default => http_response_code(405) && ['error' => 'Unsupported method']
};

echo json_encode($response);

function getReviews($conn, $filters)
{
    $sql = "SELECT * FROM nv_review";
    $where = [];
    $values = [];
    $types = '';

    foreach ($filters as $key => $value) {
        if (in_array($key, ['nv_review_id', 'nv_novel_id', 'nv_user_id'])) {
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
    return $stmt->get_result()->fetch_all(MYSQLI_ASSOC) ?: ['error' => 'No results found'];
}

function createReview($conn, $data)
{
    $required = ['nv_novel_id', 'nv_review_rating', 'nv_review_comment'];
    foreach ($required as $field) {
        if (empty($data[$field])) {
            http_response_code(400);
            return ['error' => "$field is required"];
        }
    }

    $novelId = $data['nv_novel_id'];
    $rating = floatval($data['nv_review_rating']);
    if ($rating < 0 || $rating > 5) {
        http_response_code(400);
        return ['error' => 'nv_review_rating must be between 0 and 5'];
    }
    $comment = trim($data['nv_review_comment']);
    $likes = intval($data['nv_review_likes'] ?? 0);
    $userId = $_SESSION['user_id'] ?? $data['nv_user_id'] ?? null;

    if (!$userId) {
        http_response_code(401);
        return ['error' => 'User ID required'];
    }

    $stmt = $conn->prepare("INSERT INTO nv_review (nv_novel_id, nv_review_rating, nv_review_comment, nv_review_likes, nv_user_id, nv_review_editted_at) VALUES (?, ?, ?, ?, ?, NOW())");
    $stmt->bind_param("idsii", $novelId, $rating, $comment, $likes, $userId);
    return $stmt->execute()
        ? ['success' => true, 'id' => $stmt->insert_id]
        : ['error' => $stmt->error];
}

function updateReview($conn, $data)
{
    if (empty($data['nv_review_id'])) {
        http_response_code(400);
        return ['error' => 'nv_review_id is required'];
    }

    $fields = [];
    $types = '';
    $values = [];
    foreach (['nv_review_rating', 'nv_review_comment', 'nv_review_likes'] as $field) {
        if (isset($data[$field])) {
            if ($field === 'nv_review_rating') {
                $rating = floatval($data[$field]);
                if ($rating < 0 || $rating > 5) {
                    http_response_code(400);
                    return ['error' => 'nv_review_rating must be between 0 and 5'];
                }
                $values[] = $rating;
                $types .= 'd';
            } else {
                $values[] = $data[$field];
                $types .= is_numeric($data[$field]) ? 'i' : 's';
            }
            $fields[] = "$field = ?";
        }
    }

    if (empty($fields))
        return ['error' => 'No fields to update'];

    $fields[] = "nv_review_editted_at = NOW()";
    $types .= 'i';
    $values[] = $data['nv_review_id'];

    $stmt = $conn->prepare("UPDATE nv_review SET " . implode(', ', $fields) . " WHERE nv_review_id = ?");
    $stmt->bind_param($types, ...$values);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}

function deleteReview($conn, $params)
{
    if (empty($params['nv_review_id'])) {
        http_response_code(400);
        return ['error' => 'nv_review_id is required'];
    }

    $stmt = $conn->prepare("DELETE FROM nv_review WHERE nv_review_id = ?");
    $stmt->bind_param("i", $params['nv_review_id']);
    return $stmt->execute()
        ? ['success' => true]
        : ['error' => $stmt->error];
}
