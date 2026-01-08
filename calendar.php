<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$uid = $user['id'];

// Add event
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $title = $_POST['title'];
    $event_date = $_POST['event_date'];
    $desc = $_POST['description'];

    $stmt = $conn->prepare("INSERT INTO calendar_events (title, event_date, description, created_by) VALUES (?, ?, ?, ?)");
    $stmt->bind_param("sssi", $title, $event_date, $desc, $uid);
    $stmt->execute();
}

// Fetch events
$events = $conn->query("SELECT calendar_events.*, users.full_name 
                        FROM calendar_events 
                        JOIN users ON calendar_events.created_by = users.id 
                        ORDER BY event_date ASC");
?>

<!DOCTYPE html>
<html>
<head>
    <title>Family Calendar - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
  
    body {
        font-family: 'Roboto', sans-serif;
        background-image: url('fam.jpg');
        background-size: cover;
        background-position: center center;
        background-attachment: fixed;
        color: #ecf0f1;
        margin: 0;
        padding: 0;
    }

    body::before {
        content: "";
        position: fixed;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        background: rgba(0, 0, 0, 0.6);
        z-index: -1;
    }

    .container {
        max-width: 900px;
        margin-top: 50px;
        background-color: rgba(44, 62, 80, 0.9);
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 8px 24px rgba(0, 0, 0, 0.5);
    }

    h2, h4 {
        color: #fff;
        font-weight: 700;
    }

    label {
        color: #ecf0f1;
    }

    .form-control {
        background-color: rgba(255, 255, 255, 0.1);
        color: #fff;
        border: 1px solid #3498db;
    }

    .form-control:focus {
        background-color: rgba(255, 255, 255, 0.15);
        color: #fff;
        border-color: #2980b9;
        box-shadow: none;
    }

    ::placeholder {
        color: #bdc3c7;
        opacity: 1;
    }

    .btn-primary {
        background-color: #3498db;
        border: none;
        font-weight: 600;
        border-radius: 25px;
    }

    .btn-primary:hover {
        background-color: #2980b9;
    }

    .btn-secondary {
        background-color: transparent;
        color: #ecf0f1;
        border: 1px solid #bdc3c7;
        border-radius: 25px;
    }

    .btn-secondary:hover {
        background-color: #7f8c8d;
        color: white;
    }

    .table {
        background-color: rgba(236, 240, 241, 0.1);
        color: #ecf0f1;
    }

    .table th, .table td {
        text-align: center;
        vertical-align: middle;
        border: 1px solid rgba(236, 240, 241, 0.2);
    }

    .table thead th {
        background-color: rgba(52, 73, 94, 0.8);
        color: #fff;
    }

    .table tbody tr:hover {
        background-color: rgba(236, 240, 241, 0.2);
    }


    </style>
</head>
<body>
<div class="container">
    <h2>🗓️ Family Calendar</h2>

    <form method="POST" class="mt-4 mb-5">
        <div class="mb-3">
            <label>Event Title</label>
            <input type="text" name="title" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Event Date</label>
            <input type="date" name="event_date" class="form-control" required>
        </div>
        <div class="mb-3">
            <label>Description</label>
            <textarea name="description" class="form-control" placeholder="Optional details"></textarea>
        </div>
        <button type="submit" class="btn btn-primary">Add Event</button>
        <a href="dashboard.php" class="btn btn-secondary">Back to Dashboard</a>
    </form>

    <h4>📅 Upcoming Events</h4>
    <table class="table table-bordered mt-3">
        <thead>
            <tr>
                <th>Title</th>
                <th>Date</th>
                <th>Description</th>
                <th>Added By</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $events->fetch_assoc()): ?>
                <tr>
                    <td><?= htmlspecialchars($row['title']) ?></td>
                    <td><?= htmlspecialchars($row['event_date']) ?></td>
                    <td><?= htmlspecialchars($row['description']) ?></td>
                    <td><?= htmlspecialchars($row['full_name']) ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</div>
</body>

</html>
