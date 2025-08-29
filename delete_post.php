<?php
session_start();
require 'db.php';

// Security Check: Is user logged in and are they an admin?
if (!isset($_SESSION['is_admin']) || $_SESSION['is_admin'] != 1) {
    // If not an admin, redirect them away.
    header('Location: feed.php');
    exit;
}

// Is a post ID provided?
if (isset($_GET['id'])) {
    $post_id = filter_var($_GET['id'], FILTER_VALIDATE_INT);
    if ($post_id) {
        $stmt = $pdo->prepare("DELETE FROM posts WHERE id = ?");
        $stmt->execute([$post_id]);
    }
}

// Redirect back to the feed after deleting.
header('Location: feed.php');
exit;
?>