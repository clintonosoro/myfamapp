<?php
// Start session only if not already active
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require 'connect.php';



$message = "";
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $user_id = $_SESSION['user']['id'];
    $content = trim($_POST['content']);
    $image_path = null;

    // Handle image upload if exists
    if (!empty($_FILES['status_image']['name'])) {
        $targetDir = "uploads/status/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $filename = time() . "_" . basename($_FILES["status_image"]["name"]);
        $targetFile = $targetDir . $filename;
        $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));

        // Validate file type
        $allowedTypes = ["jpg", "jpeg", "png", "gif"];
        if (in_array($fileType, $allowedTypes)) {
            // Validate file size (e.g., 5MB limit)
            if ($_FILES["status_image"]["size"] <= 5242880) {
                if (move_uploaded_file($_FILES["status_image"]["tmp_name"], $targetFile)) {
                    $image_path = $targetFile;
                } else {
                    $message = "Failed to upload image.";
                }
            } else {
                $message = "File size exceeds 5MB limit.";
            }
        } else {
            $message = "Invalid file type. Only JPG, PNG, GIF are allowed.";
        }
    }

    // Insert into DB
    if (empty($message)) {
        // SQL query
        $sql = "INSERT INTO status (user_id, content, image_path, created_at) VALUES (?, ?, ?, NOW())";
        
        // Prepare the SQL statement
        $stmt = $conn->prepare($sql);

        // Check if prepare() was successful
        if ($stmt === false) {
            die('Error in query preparation: ' . $conn->error);
        }

        // Bind parameters and execute
        $stmt->bind_param("iss", $user_id, $content, $image_path);
        if ($stmt->execute()) {
            $message = "Status posted successfully!";
        } else {
            $message = "Failed to post status: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Post Status - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(to right, #2c3e50, #3498db); /* Deep gradient from blue to darker blue */
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .status-card {
            max-width: 700px;
            margin: 80px auto;
            background: #34495e; /* Darker background for the card */
            padding: 40px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .status-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
        }

        .status-card h4 {
            font-size: 1.75rem;
            font-weight: 600;
            color: #ecf0f1; /* Lighter text color */
            margin-bottom: 30px;
        }

        .form-label {
            font-weight: 500;
            color: #ecf0f1; /* Lighter text for labels */
        }

        .form-control {
            border-radius: 10px;
            background-color: #95a5a6; /* Slightly lighter background for inputs */
            transition: all 0.3s ease;
        }

        .form-control:focus {
            background-color: #ffffff;
            border-color: #3498db;
            box-shadow: 0 0 5px rgba(52, 152, 219, 0.25);
        }

        .file-info {
            font-size: 0.85rem;
            color: #bdc3c7; /* Lighter color for file info */
        }

        .btn-primary {
            width: 100%;
            padding: 12px;
            background: linear-gradient(to right, #007bff, #0056b3);
            font-weight: 600;
            border: none;
            border-radius: 10px;
            transition: background 0.3s ease, transform 0.2s ease;
        }

        .btn-primary:hover {
            background: linear-gradient(to right, #0056b3, #003f7f);
            transform: scale(1.05);
        }

        .alert {
            font-size: 1rem;
            padding: 15px;
            margin-bottom: 20px;
            background-color: #ecf0f1;
            border-color: #bdc3c7;
            border-radius: 8px;
        }

        .alert-info {
            border-left: 5px solid #3498db;
        }

        .error-message {
            font-size: 1rem;
            color: #e74c3c;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
        }

        .error-message i {
            margin-right: 10px;
            font-size: 1.5rem;
            color: #e74c3c;
        }

        .file-input-container {
            position: relative;
        }

        .file-input-container input[type="file"] {
            cursor: pointer;
            padding: 15px;
        }

        .file-input-container input[type="file"]::before {
            content: '📸';
            position: absolute;
            top: 10px;
            left: 10px;
            font-size: 1.5rem;
            color: #3498db;
        }

        @media (max-width: 576px) {
            .status-card {
                padding: 25px;
            }

            .status-card h4 {
                font-size: 1.5rem;
            }
        }

    </style>
</head>
<body>

<?php include 'navbar.php'; ?>

<div class="status-card">
    <h4>Share a Moment with Your Family</h4>

    <?php if ($message): ?>
        <div class="alert alert-info"><?= htmlspecialchars($message) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">
        <div class="mb-3">
            <label for="content" class="form-label">Text Status (optional)</label>
            <textarea name="content" class="form-control" rows="4" placeholder="What's on your mind today?"></textarea>
        </div>

        <div class="mb-3 file-input-container">
            <label for="status_image" class="form-label">Add a Picture (optional)</label>
            <input type="file" name="status_image" class="form-control">
            <div class="file-info">Accepted formats: JPG, PNG, GIF — Max size: 5MB</div>
        </div>

        <button type="submit" class="btn btn-primary">Post Status</button>
    </form>
</div>

</body>
</html>
