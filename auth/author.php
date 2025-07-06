<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    header('Location: /author_login.html');
    exit;
}
$authorId = $_SESSION['author_id'];
?>