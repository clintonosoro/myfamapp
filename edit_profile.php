<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user']; // Retrieve user data from session
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="styles.css" rel="stylesheet"> <!-- External CSS Link for styling -->
</head>
<style>
    /* General Styles */
body {
    font-family: 'Roboto', sans-serif;
    background-image: url('fam.jpg');
    background-size: cover;  
    background-position: center center;  
    background-attachment: fixed;  
    color: #333;
}

body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);  
    z-index: -1;
}

/* Navbar */
.navbar {
    background-color: rgba(44, 62, 80, 0.9); /* Semi-transparent dark background */
}

.navbar-profile {
    width: 40px;
    height: 40px;
    object-fit: cover;
    border-radius: 50%;
    border: 2px solid white;
}

.nav-link {
    font-size: 1rem;
    color: #fff;
    text-align: center;
    transition: all 0.3s ease-in-out;
}

.nav-item {
    margin: 20px 15px;
    text-align: center;
    position: relative;
}

.nav-item i {
    font-size: 1.75rem;
    transition: transform 0.3s ease;
}

.nav-item:hover i {
    transform: scale(1.1);
}

.nav-item:hover .nav-link {
    color: #ffd700; /* Gold for hover effect */
}

.dropdown-menu {
    min-width: 200px;
}

.dropdown-item {
    font-size: 1rem;
    padding: 10px 20px;
}

/* Profile Area */
.profile-area {
    display: flex;
    align-items: center;
}

.profile-area img {
    margin-right: 10px;
}

.profile-area .btn {
    font-size: 0.875rem;
    padding: 5px 15px;
    background-color: #3498db;
    color: white;
    border: none;
    border-radius: 20px;
    transition: background-color 0.3s ease;
}

.profile-area .btn:hover {
    background-color: #2980b9;
}

/* Dashboard Section */
.container {
    margin-top: 40px;
}

h2 {
    font-weight: 700;
    color: #2c3e50;
}

p.lead {
    font-size: 1.1rem;
    color: #7f8c8d;
}

.nav-link span {
    display: block;
    font-size: 0.9rem;
    margin-top: 5px;
}

/* Active State */
.nav-link.active {
    color: #3498db;
    font-weight: 500;
}

/* Icon Alignment */
.nav-item i {
    margin-bottom: 10px;
}

</style>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">MyFamApp</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
            <ul class="navbar-nav align-items-center">
                <li class="nav-item me-2">
                    <a href="dashboard.php" class="btn btn-outline-light btn-sm">Dashboard</a>
                </li>
                <li class="nav-item me-2">
                    <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Profile Edit Form -->
<div class="container">
    <div class="card shadow-sm">
        <div class="card-body">
            <h2 class="text-center">Edit Your Profile</h2>
            <form action="update_profile.php" method="POST" enctype="multipart/form-data">

                <!-- Profile Picture -->
                <div class="mb-3">
                    <label for="profile_pic" class="form-label">Profile Picture</label>
                    <input type="file" class="form-control" id="profile_pic" name="profile_pic">
                    <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile Picture" class="mt-3" width="100">
                </div>

                <!-- Full Name -->
                <div class="mb-3">
                    <label for="full_name" class="form-label">Full Name</label>
                    <input type="text" class="form-control" id="full_name" name="full_name" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <!-- Email -->
                <div class="mb-3">
                    <label for="email" class="form-label">Email</label>
                    <input type="email" class="form-control" id="email" name="email" value="<?= htmlspecialchars($user['email']) ?>" required>
                </div>

                <!-- Submit Button -->
                <button type="submit" class="btn btn-primary">Save Changes</button>
            </form>
        </div>
    </div>
</div>

<!-- Bootstrap JS (for navbar collapse) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
