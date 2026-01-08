<?php
// comment_status.php

session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
  header("Location: login.php");
  exit();
}

$status_id = intval($_POST['status_id']);
$user_id   = $_SESSION['user']['id'];
$comment   = trim($_POST['comment_text']);

// 1) Confirm the status exists
$chk = $conn->prepare("SELECT 1 FROM status WHERE id = ?");
$chk->bind_param("i", $status_id);
$chk->execute();
if ($chk->get_result()->num_rows === 0) {
  die("Error: status #{$status_id} not found. Cannot post comment.");
}

if ($comment !== '') {
  $ins = $conn->prepare("
    INSERT INTO comments (status_id, user_id, comment_text, created_at)
    VALUES (?, ?, ?, NOW())
  ");
  if (!$ins) {
    die("Prepare failed: " . $conn->error);
  }
  $ins->bind_param("iis", $status_id, $user_id, $comment);
  if (!$ins->execute()) {
    die("Execute failed: " . $ins->error);
  }
}

header("Location: view_comments.php?status_id={$status_id}");
exit();
?>