<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$status_id = $_GET['status_id'];

// Get the list of users who liked the status
$sql_likes = "SELECT u.full_name, u.profile_pic FROM likes l
              JOIN users u ON l.user_id = u.id
              WHERE l.status_id = ?";
$stmt_likes = $conn->prepare($sql_likes);
$stmt_likes->bind_param("i", $status_id);
$stmt_likes->execute();
$likes_result = $stmt_likes->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Likes</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h3>People who liked this status</h3>
    <div class="row mt-4">
        <?php while ($like = $likes_result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <img src="<?= htmlspecialchars($like['profile_pic']) ?>" class="profile-pic me-3" alt="Profile" width="50" height="50">
                        <div>
                            <h5 class="mb-0"><?= htmlspecialchars($like['full_name']) ?></h5>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
