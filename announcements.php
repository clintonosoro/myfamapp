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
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $title = trim($_POST['title']);
    $message = trim($_POST['message']);

    if (!empty($title) && !empty($message)) {
        $stmt = $conn->prepare("INSERT INTO announcements (user_id, title, message) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $uid, $title, $message);
        $stmt->execute();
    }
}

// Fetch announcements
$announcements = $conn->query("
    SELECT announcements.*, users.full_name 
    FROM announcements 
    JOIN users ON announcements.user_id = users.id 
    ORDER BY date_posted DESC
");
?>

<!DOCTYPE html>
<html>
<head>
    <title>📢 Family Announcements - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background-image: url('fam.jpg');
            background-size: cover;
            background-attachment: fixed;
            background-position: center;
            font-family: 'Segoe UI', sans-serif;
            color: #fff;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        .container {
            margin-top: 60px;
            max-width: 700px;
            background-color: rgba(52, 73, 94, 0.9);
            padding: 30px;
            border-radius: 15px;
        }

        h2, h4 {
            color: #f1c40f;
        }

        .form-control, .btn {
            border-radius: 20px;
        }

        .btn-primary {
            background-color: #3498db;
            border-color: #3498db;
        }

        .btn-primary:hover {
            background-color: #2980b9;
        }

        .card {
            background-color: rgba(255, 255, 255, 0.1);
            border: none;
            border-radius: 15px;
        }

        .card-title {
            color: #f39c12;
        }

        .card-text, .text-muted {
            color: #ecf0f1;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📢 Family Announcements</h2>

        <!-- Post Announcement Form -->
        <form method="POST" class="mt-4 mb-5">
            <div class="mb-3">
                <label>Title</label>
                <input type="text" name="title" class="form-control" required>
            </div>
            <div class="mb-3">
                <label>Message</label>
                <textarea name="message" rows="3" class="form-control" required></textarea>
            </div>
            <button type="submit" class="btn btn-primary">Post Announcement</button>
            <a href="dashboard.php" class="btn btn-secondary">Back</a>
        </form>

        <!-- Display Announcements -->
        <h4>🧾 All Announcements</h4>
        <?php while ($a = $announcements->fetch_assoc()): ?>
            <div class="card mb-3 shadow-sm">
                <div class="card-body">
                    <h5 class="card-title"><?= htmlspecialchars($a['title']) ?></h5>
                    <p class="card-text"><?= nl2br(htmlspecialchars($a['message'])) ?></p>
                    <small class="text-muted">
                        Posted by <?= htmlspecialchars($a['full_name']) ?> on <?= date("M d, Y H:i", strtotime($a['date_posted'])) ?>
                    </small>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
