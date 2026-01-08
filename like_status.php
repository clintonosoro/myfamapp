<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['user']['id'];
$status_id = $_POST['status_id'];

// Check if the user has already liked the status
$sql_check_like = "SELECT id FROM likes WHERE status_id = ? AND user_id = ?";
$stmt_check_like = $conn->prepare($sql_check_like);
$stmt_check_like->bind_param("ii", $status_id, $user_id);
$stmt_check_like->execute();
$liked_status = $stmt_check_like->get_result()->num_rows > 0;

if ($liked_status) {
    // Remove the like
    $sql_delete_like = "DELETE FROM likes WHERE status_id = ? AND user_id = ?";
    $stmt = $conn->prepare($sql_delete_like);
    $stmt->bind_param("ii", $status_id, $user_id);
    $stmt->execute();
} else {
    // Add the like
    $sql_insert_like = "INSERT INTO likes (status_id, user_id) VALUES (?, ?)";
    $stmt = $conn->prepare($sql_insert_like);
    $stmt->bind_param("ii", $status_id, $user_id);
    $stmt->execute();
}

header("Location: view_status.php"); // Redirect back to the status page
exit();
