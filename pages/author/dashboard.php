<?php
require_once __DIR__ . '/../../auth/author.php';
?>
<!DOCTYPE html>
<html>
<head>
    <title>Author Dashboard</title>
</head>
<body>
    <h1>Welcome Author ID: <?= htmlspecialchars($AUTHOR_ID) ?></h1>
    <ul>
        <li><a href="../../api/author_api.php">View Author API</a></li>
        <li><a href="../../auth/logout_author.php">Logout</a></a></li>
    </ul>
</body>
</html>