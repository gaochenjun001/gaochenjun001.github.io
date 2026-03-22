<?php
require_once __DIR__ . '/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    if (is_logged_in()) {
        logout();
    }
    header('Location: index.php');
    exit;
}

$token = $_POST['csrf_token'] ?? '';
if (is_logged_in() && verify_csrf($token)) {
    logout();
}

header('Location: index.php');
exit;
