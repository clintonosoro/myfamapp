<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$uid = $user['id'];

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['add_task'])) {
        $task = $_POST['task'];
        $is_shared = isset($_POST['is_shared']) ? 1 : 0;
        $stmt = $conn->prepare("INSERT INTO todo_tasks (user_id, task, is_shared) VALUES (?, ?, ?)");
        $stmt->bind_param("isi", $uid, $task, $is_shared);
        $stmt->execute();
    }

    if (isset($_POST['mark_done'])) {
        $tid = $_POST['task_id'];
        $conn->query("UPDATE todo_tasks SET is_done = 1 WHERE id = $tid AND (user_id = $uid OR is_shared = 1)");
    }

    if (isset($_POST['delete_task'])) {
        $tid = $_POST['task_id'];
        $conn->query("DELETE FROM todo_tasks WHERE id = $tid AND (user_id = $uid OR is_shared = 1)");
    }
}

// Fetch tasks
$tasks = $conn->query("
    SELECT * FROM todo_tasks 
    WHERE user_id = $uid OR is_shared = 1 
    ORDER BY is_done ASC, created_at DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>To-Do List - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="container mt-5">
    <h2>📝 Family To-Do List</h2>

    <form method="POST" class="mt-4 mb-5">
        <div class="mb-3">
            <label>New Task</label>
            <input type="text" name="task" class="form-control" required>
        </div>
        <div class="form-check mb-3">
            <input class="form-check-input" type="checkbox" name="is_shared" id="sharedCheck">
            <label class="form-check-label" for="sharedCheck">Share with family</label>
        </div>
        <button type="submit" name="add_task" class="btn btn-primary">Add Task</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>

    <h4>📋 Your Tasks</h4>
    <table class="table table-bordered">
        <thead>
            <tr>
                <th>Task</th>
                <th>Shared</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($task = $tasks->fetch_assoc()): ?>
                <tr class="<?= $task['is_done'] ? 'table-success' : '' ?>">
                    <td><?= htmlspecialchars($task['task']) ?></td>
                    <td><?= $task['is_shared'] ? '✅' : '❌' ?></td>
                    <td><?= $task['is_done'] ? 'Done' : 'Pending' ?></td>
                    <td>
                        <?php if (!$task['is_done']): ?>
                            <form method="POST" style="display:inline;">
                                <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                                <button type="submit" name="mark_done" class="btn btn-sm btn-success">Mark Done</button>
                            </form>
                        <?php endif; ?>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="task_id" value="<?= $task['id'] ?>">
                            <button type="submit" name="delete_task" class="btn btn-sm btn-danger">Delete</button>
                        </form>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
