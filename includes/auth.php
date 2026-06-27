<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

function requireAdmin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: login.php");
        exit;
    }
}

function adminName() {
    return $_SESSION['admin_nama'] ?? 'Admin';
}
?>