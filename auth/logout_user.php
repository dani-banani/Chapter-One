<?php
require_once __DIR__ . '/../paths.php';
session_start();
session_unset();
session_destroy();

setcookie('user_login_token', '', time() - 3600, '/');

http_response_code(200);
header('Location: ' . LOGIN_PAGE);
exit;
