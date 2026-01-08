<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$current_user = $_SESSION['user']['id'];

// Delete old statuses
$conn->query("DELETE FROM status WHERE created_at < NOW() - INTERVAL 1 DAY");

// Get active statuses from other users
$stmt = $conn->prepare("
    SELECT s.content, s.created_at, u.full_name, u.profile_pic
    FROM status s
    JOIN users u ON s.user_id = u.id
    WHERE s.user_id != ? AND s.created_at >= NOW() - INTERVAL 1 DAY
    ORDER BY s.created_at DESC
");
$stmt->bind_param("i", $current_user);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Status Feed - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .story-card {
            border-radius: 20px;
            background: #f9f9f9;
            padding: 1rem;
            transition: 0.3s;
        }
        .story-card:hover {
            transform: scale(1.03);
            background-color: #f0f8ff;
        }
        .profile-thumb {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            margin-right: 10px;
        }
    </style>
</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-5">
    <h4>Family Status Updates</h4>
    <div class="row mt-4">
        <?php while ($row = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="story-card shadow-sm p-3">
                    <div class="d-flex align-items-center mb-2">
                        <img src="<?= htmlspecialchars($row['profile_pic']) ?>" class="profile-thumb" alt="Pic">
                        <strong><?= htmlspecialchars($row['full_name']) ?></strong>
                    </div>
                    <p><?= htmlspecialchars($row['content']) ?></p>
                    <small class="text-muted"><?= date('F j, g:i A', strtotime($row['created_at'])) ?></small>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>
</body>
</html>
