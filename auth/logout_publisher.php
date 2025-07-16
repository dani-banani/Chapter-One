<?php
session_start();
setcookie('publisher_login_token', '', time() - 3600, "/", "", isset($_SERVER['HTTPS']), true);
unset($_SESSION['publisher_id']);
unset($_SESSION['user_role']);
session_destroy();

header('Location: ../../login/publisher_login.html');
exit;