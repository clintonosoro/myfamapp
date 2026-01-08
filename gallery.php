<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$uid = $user['id'];

// Handle image upload
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_FILES["photo"])) {
    $caption = $_POST["caption"];
    $target_dir = "uploads/";
    $target_file = $target_dir . time() . "_" . basename($_FILES["photo"]["name"]);
    $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

    $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

    if (in_array($imageFileType, $allowed_types)) {
        if (move_uploaded_file($_FILES["photo"]["tmp_name"], $target_file)) {
            $stmt = $conn->prepare("INSERT INTO photos (user_id, file_path, caption) VALUES (?, ?, ?)");
            $stmt->bind_param("iss", $uid, $target_file, $caption);
            $stmt->execute();
        } else {
            echo "❌ Error uploading file.";
        }
    } else {
        echo "❌ Only JPG, JPEG, PNG & GIF allowed.";
    }
}

// Fetch all photos
$photos = $conn->query("SELECT photos.*, users.full_name FROM photos JOIN users ON photos.user_id = users.id ORDER BY uploaded_at DESC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Photo Gallery - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .photo-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(180px, 1fr));
            gap: 15px;
        }
        .photo-grid img {
            width: 100%;
            height: 150px;
            object-fit: cover;
            border-radius: 10px;
        }
        .photo-card {
            border: 1px solid #ccc;
            padding: 8px;
            border-radius: 10px;
            background: #f9f9f9;
        }
    </style>
</head>
<body class="container mt-5">
    <h2>📷 Family Photo Gallery</h2>

    <form method="POST" enctype="multipart/form-data" class="mb-4 mt-4">
        <div class="mb-3">
            <label>Choose Photo</label>
            <input type="file" name="photo" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Caption (optional)</label>
            <input type="text" name="caption" class="form-control">
        </div>
        <button type="submit" class="btn btn-primary">Upload Photo</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>

    <div class="photo-grid">
        <?php while ($row = $photos->fetch_assoc()): ?>
            <div class="photo-card">
                <img src="<?= htmlspecialchars($row['file_path']) ?>" alt="Family Photo">
                <small><strong><?= htmlspecialchars($row['caption']) ?></strong><br>
                by <?= htmlspecialchars($row['full_name']) ?><br>
                <?= date("M d, Y", strtotime($row['uploaded_at'])) ?></small>
            </div>
        <?php endwhile; ?>
    </div>
</body>
</html>
