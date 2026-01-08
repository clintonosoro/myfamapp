<?php
session_start();
require 'connect.php';

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

// Get the logged-in user's details
$user = $_SESSION['user'];

// Handle liking a status
if (isset($_POST['like_status'])) {
    $status_id = $_POST['status_id'];
    $user_id = $user['id'];

    // Check if the user has already liked the status
    $check_like_query = "SELECT * FROM likes WHERE status_id = ? AND user_id = ?";
    $stmt_check = $conn->prepare($check_like_query);
    $stmt_check->bind_param("ii", $status_id, $user_id);
    $stmt_check->execute();
    $result_check = $stmt_check->get_result();

    if ($result_check->num_rows == 0) {
        // If not liked yet, insert a like
        $insert_like_query = "INSERT INTO likes (status_id, user_id) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($insert_like_query);
        $stmt_insert->bind_param("ii", $status_id, $user_id);
        $stmt_insert->execute();
    }
}

// Prepare the SQL query to fetch statuses
$sql = "SELECT s.id, s.content, s.image_path, s.created_at, u.full_name, u.profile_pic, 
               (SELECT COUNT(*) FROM likes l WHERE l.status_id = s.id) AS like_count,
               (SELECT COUNT(*) FROM comments c WHERE c.status_id = s.id) AS comment_count,
               (SELECT COUNT(*) FROM likes l WHERE l.status_id = s.id AND l.user_id = ?) AS is_liked
        FROM status s
        JOIN users u ON s.user_id = u.id
        ORDER BY s.created_at DESC";

$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>View Statuses - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css" rel="stylesheet"> <!-- FontAwesome for Like icon -->
    <style>
        .profile-pic {
            width: 60px;
            height: 60px;
            border-radius: 50%;
            object-fit: cover;
        }
        .status-container {
            margin-top: 20px;
        }
        .status-card {
            margin-bottom: 15px;
        }
        .status-text {
            font-size: 14px;
            color: #555;
        }
        .status-image {
            max-width: 100%;
            height: auto;
        }
        .like-comment-count {
            font-size: 12px;
            color: #888;
        }
        /* Like button styling */
        .like-button {
            background-color: #ff4d4d; /* Red background for "Like" button */
            color: white;
            border: none;
            font-size: 16px;
            padding: 5px 15px;
            cursor: pointer;
            border-radius: 20px;
            transition: background-color 0.3s, transform 0.2s;
        }
        .like-button:hover {
            background-color: #ff1a1a; /* Darker red on hover */
            transform: scale(1.1);
        }
        .liked {
            background-color: #4CAF50; /* Green color when liked */
            color: white;
        }
        .liked:hover {
            background-color: #45a049; /* Darker green on hover */
        }
    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h3>View Statuses</h3>
    <div class="status-container">
        <?php while ($status = $result->fetch_assoc()): ?>
            <div class="card status-card">
                <div class="card-body">
                    <div class="d-flex align-items-center">
                        <img src="<?= htmlspecialchars($status['profile_pic']) ?>" alt="Profile Picture" class="profile-pic me-3">
                        <div>
                            <h5 class="mb-0"><?= htmlspecialchars($status['full_name']) ?></h5>
                            <small class="text-muted"><?= date('F j, Y, g:i a', strtotime($status['created_at'])) ?></small>
                        </div>
                    </div>
                    <div class="mt-3">
                        <p class="status-text"><?= htmlspecialchars($status['content']) ?></p>
                        <?php if (!empty($status['image_path'])): ?>
                            <img src="<?= htmlspecialchars($status['image_path']) ?>" alt="Status Image" class="status-image mt-2">
                        <?php endif; ?>
                    </div>

                    <div class="mt-2">
                        <form action="view_status.php" method="POST" class="d-inline">
                            <input type="hidden" name="status_id" value="<?= $status['id'] ?>">
                            <button type="submit" name="like_status" class="like-button <?= ($status['is_liked'] > 0) ? 'liked' : '' ?>">
                                <i class="fas fa-heart"></i> <?= $status['like_count'] ?> Likes
                            </button> 
                        </form> | 
                        <a href="view_comments.php?status_id=<?= $status['id'] ?>" class="btn btn-link">
                            <?= $status['comment_count'] ?> Comments
                        </a>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>

    <!-- Comment Form -->
    <form action="comment_status.php" method="POST">
        <input type="hidden" name="status_id" value="<?= $status['id'] ?>">
        <textarea name="comment_text" class="form-control" rows="3" placeholder="Add a comment..." required></textarea>
        <button type="submit" class="btn btn-outline-primary mt-2">Post Comment</button>
    </form>

    <!-- Display Comments -->
    <?php
    $sql_comments = "SELECT c.comment_text, u.full_name, c.created_at 
                     FROM comments c
                     JOIN users u ON c.user_id = u.id
                     WHERE c.status_id = ? 
                     ORDER BY c.created_at ASC";

    $stmt_comments = $conn->prepare($sql_comments);
    $stmt_comments->bind_param("i", $status['id']);
    $stmt_comments->execute();
    $comments_result = $stmt_comments->get_result();

    while ($comment = $comments_result->fetch_assoc()):
    ?>
        <div class="comment mt-3">
            <strong><?= htmlspecialchars($comment['full_name']) ?></strong>
            <p><?= htmlspecialchars($comment['comment_text']) ?></p>
            <small><?= date('F j, Y, g:i a', strtotime($comment['created_at'])) ?></small>
        </div>
    <?php endwhile; ?>

</div>

</body>
</html>
