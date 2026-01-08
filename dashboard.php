<?php
session_start();
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MyFamApp Dashboard</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        /* General Styles */
       /* General Styles */
body {
    font-family: 'Roboto', sans-serif;
    background-image: url('fam.jpg');
    background-size: cover;  /* Makes sure the image covers the entire screen */
    background-position: center center;  /* Centers the image */
    background-attachment: fixed;  /* Keeps the background fixed when scrolling */
    color: #333;
}

/* You can also add an overlay to make the text more readable */
body::before {
    content: "";
    position: absolute;
    top: 0;
    left: 0;
    right: 0;
    bottom: 0;
    background: rgba(0, 0, 0, 0.5);  /* Dark overlay */
    z-index: -1;  /* Makes sure the overlay stays behind the content */
}

/* Navbar */
.navbar {
    background-color: rgba(44, 62, 80, 0.9); /* Semi-transparent dark background */
}

/* Other styles remain the same */

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

        /* Navbar Style */
        .navbar {
            background-color: #2c3e50; /* Darker, professional blue */
        }

        .navbar-brand {
            font-size: 1.75rem;
            font-weight: 700;
        }

        .navbar-toggler {
            border: none;
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
</head>
<body>

<!-- Navbar -->
<nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
        <a class="navbar-brand" href="#">MyFamApp</a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent">
            <span class="navbar-toggler-icon"></span>
        </button>

        <div class="collapse navbar-collapse justify-content-end" id="navbarContent">
            <ul class="navbar-nav">
                <!-- Edit Profile -->
                <li class="nav-item">
                    <a href="edit_profile.php" class="nav-link text-white">
                        <i class="bi bi-person"></i> <span>Edit Profile</span>
                    </a>
                </li>
                <!-- Post Status -->
                <li class="nav-item">
                    <a href="post_status.php" class="nav-link text-white">
                        <i class="bi bi-megaphone"></i> <span>Post Status</span>
                    </a>
                </li>
                <!-- Family Events -->
                <li class="nav-item dropdown">
                    <a class="nav-link dropdown-toggle text-white" href="#" id="navbarDropdown" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <i class="bi bi-calendar"></i> <span>Events</span>
                    </a>
                    <ul class="dropdown-menu" aria-labelledby="navbarDropdown">
                        <li><a class="dropdown-item" href="calendar.php">Calendar</a></li>
                        <li><a class="dropdown-item" href="todo.php">To-Do List</a></li>
                    </ul>
                </li>
                <!-- Gallery -->
                <li class="nav-item">
                    <a href="gallery.php" class="nav-link text-white">
                        <i class="bi bi-camera"></i> <span>Gallery</span>
                    </a>
                </li>
                <!-- Announcements -->
                <li class="nav-item">
                    <a href="announcements.php" class="nav-link text-white">
                        <i class="bi bi-bullhorn"></i> <span>Announcements</span>
                    </a>
                </li>
                <!-- Family Members -->
                <li class="nav-item">
                    <a href="family_members.php" class="nav-link text-white">
                        <i class="bi bi-person-bounding-box"></i> <span>Family Members</span>
                    </a>
                </li>
                <!-- My Status -->
                <li class="nav-item">
                    <a href="status.php" class="nav-link text-white">
                        <i class="bi bi-chat-square-text"></i> <span>My Status</span>
                    </a>
                </li>
                <!-- Inbox -->
                <li class="nav-item">
                    <a href="inbox.php" class="nav-link text-white">
                        <i class="bi bi-envelope"></i> <span>Inbox</span>
                    </a>
                </li>
                <!-- Profile Picture -->
                <li class="nav-item profile-area">
                    <img src="<?= htmlspecialchars($user['profile_pic']) ?>" alt="Profile" class="navbar-profile">
                    <a href="logout.php" class="btn">Logout</a>
                </li>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Dashboard Content -->
<div class="container">
    <h2 class="mb-4">Dashboard</h2>
    <p class="lead">What would you like to do today?</p>
</div>

<!-- Bootstrap JS (for dropdowns and other interactions) -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
