<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$uid = $user['id'];

// Handle new status post
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $content = trim($_POST['content']);
    $image_path = null;

    // Handle optional image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $image_tmp = $_FILES['image']['tmp_name'];
        $image_name = time() . '_' . basename($_FILES['image']['name']);
        $target_dir = 'uploads/';
        $target_path = $target_dir . $image_name;

        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        if (move_uploaded_file($image_tmp, $target_path)) {
            $image_path = $target_path;
        }
    }

    if (!empty($content)) {
        $stmt = $conn->prepare("INSERT INTO status (user_id, content, image_path) VALUES (?, ?, ?)");
        $stmt->bind_param("iss", $uid, $content, $image_path);
        $stmt->execute();
    }
}

// Fetch only user's own statuses
$stmt = $conn->prepare("SELECT status.*, users.full_name FROM status 
                        JOIN users ON status.user_id = users.id 
                        WHERE status.user_id = ? 
                        ORDER BY created_at DESC");
$stmt->bind_param("i", $uid);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>My Status - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-image: url('fam.jpg');
            background-size: cover;
            background-position: center;
            background-attachment: fixed;
            color: #fff;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.6);
            z-index: -1;
        }

        .container {
            margin-top: 60px;
            max-width: 700px;
            background-color: rgba(44, 62, 80, 0.85);
            padding: 30px;
            border-radius: 15px;
        }

        h2 {
            color: #f1c40f;
            margin-bottom: 20px;
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

        .status-box {
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 20px;
            color: #fff;
        }

        .status-box img {
            max-width: 100%;
            margin-top: 10px;
            border-radius: 10px;
        }

        .status-header {
            font-weight: 500;
            color: #f39c12;
        }

        .timestamp {
            font-size: 0.85rem;
            color: #bdc3c7;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>📝 My Status</h2>

        <!-- Status Post Form -->
        <form method="POST" enctype="multipart/form-data" class="mb-4">
            <div class="mb-3">
                <textarea name="content" class="form-control" placeholder="What's on your mind?" required></textarea>
            </div>
            <div class="mb-3">
                <input type="file" name="image" class="form-control">
            </div>
            <button type="submit" class="btn btn-primary">Post Status</button>
            <a href="dashboard.php" class="btn btn-secondary">Back</a>
        </form>

        <!-- Display Statuses -->
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="status-box">
                <div class="status-header"><?= htmlspecialchars($row['full_name']) ?></div>
                <div class="timestamp"><?= date("F j, Y, g:i a", strtotime($row['created_at'])) ?></div>
                <p><?= nl2br(htmlspecialchars($row['content'])) ?></p>
                <?php if (!empty($row['image_path'])): ?>
                    <img src="<?= htmlspecialchars($row['image_path']) ?>" alt="Status Image">
                <?php endif; ?>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
