<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];

// Get all members except self
$sql = "SELECT id, full_name, role, profile_pic FROM users WHERE id != ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user['id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html>
<head>
    <title>Family Members - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
       body {
    font-family: 'Roboto', sans-serif;
    background-color: #f7f9fc;
    margin: 0;
}

.container {
    max-width: 950px;
    margin-top: 50px;
}

.card {
    border: none;
    border-radius: 10px;
    background-color: #fff;
    box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
}

.card-body {
    display: flex;
    align-items: center;
    padding: 20px;
}

.card-body .profile-pic {
    width: 70px;
    height: 70px;
    border-radius: 50%;
    object-fit: cover;
    margin-right: 15px;
}

.card-body h5 {
    font-size: 1.2rem;
    font-weight: 600;
    color: #333;
}

.card-body small {
    font-size: 0.9rem;
    color: #7a7a7a;
}

.card-body .btn {
    border-radius: 25px;
    font-size: 0.85rem;
    padding: 5px 12px;
    margin: 3px;
}

.card-body .btn-outline-primary {
    border-color: #3498db;
    color: #3498db;
}

.card-body .btn-outline-primary:hover {
    background-color: #3498db;
    color: #fff;
    border-color: #3498db;
}

.card-body .btn-outline-success {
    border-color: #2ecc71;
    color: #2ecc71;
}

.card-body .btn-outline-success:hover {
    background-color: #2ecc71;
    color: #fff;
    border-color: #2ecc71;
}

.card-body .btn-outline-info {
    border-color: #1abc9c;
    color: #1abc9c;
}

.card-body .btn-outline-info:hover {
    background-color: #1abc9c;
    color: #fff;
    border-color: #1abc9c;
}

h3 {
    font-size: 1.8rem;
    color: #333;
    font-weight: 700;
    margin-bottom: 30px;
}

.row {
    display: flex;
    flex-wrap: wrap;
}

.col-md-4 {
    display: flex;
    justify-content: center;
}

.mb-4 {
    margin-bottom: 30px;
}


    </style>
</head>
<body>
<?php include 'navbar.php'; ?>

<div class="container mt-5">
    <h3>Family Members</h3>
    <div class="row mt-4">
        <?php while ($member = $result->fetch_assoc()): ?>
            <div class="col-md-4 mb-4">
                <div class="card shadow-sm h-100">
                    <div class="card-body d-flex align-items-center">
                        <img src="<?= htmlspecialchars($member['profile_pic']) ?>" class="profile-pic me-3" alt="Profile">
                        <div>
                            <h5 class="mb-0"><?= htmlspecialchars($member['full_name']) ?></h5>
                            <small class="text-muted"><?= htmlspecialchars($member['role']) ?></small>
                            <div class="mt-2">
                                <a href="inbox.php?to=<?= $member['id'] ?>" class="btn btn-sm btn-outline-primary">Inbox</a>
                                <a href="view_status.php?user_id=<?= $member['id'] ?>" class="btn btn-sm btn-outline-success">Status</a>
                                <a href="locate.php?user_id=<?= $member['id'] ?>" class="btn btn-sm btn-outline-info">Locate</a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; ?>
    </div>
</div>

</body>
</html>
