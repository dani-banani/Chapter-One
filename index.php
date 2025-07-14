<?php
require_once __DIR__ . '/paths.php';

session_start();
$userRole = isset($_SESSION['user_role']) ? $_SESSION['user_role'] : null;

if($userRole == null){
    header('Location: ' . LOGIN_PAGE);
    exit;
}

switch($userRole){
    case 'author':
        header('Location: ' . AUTHOR_DASHBOARD_PAGE);
        exit;
    default:
        header('Location: ' . LOGIN_PAGE);
        exit;
}

?>
