<?php
session_start();
if (!isset($_SESSION['author_id'])) {
    header('Location: Chapter-One-main/Chapter-One-main/pages/login.html');
    exit;
}
$authorId = $_SESSION['author_id'];
?>