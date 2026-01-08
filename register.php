<?php
session_start();
require 'connect.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];

    if ($password !== $confirm_password) {
        $error = "Passwords do not match.";
    } else {
        $check = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check->bind_param("s", $email);
        $check->execute();
        $check->store_result();

        if ($check->num_rows > 0) {
            $error = "Email already registered.";
        } else {
            $target_dir = "uploads/";
            $profile_pic = "uploads/default.png";

            if (!empty($_FILES["profile_pic"]["name"])) {
                $file_name = time() . "_" . basename($_FILES["profile_pic"]["name"]);
                $target_file = $target_dir . $file_name;
                $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'gif'];

                if (in_array($imageFileType, $allowed)) {
                    if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
                        $profile_pic = $target_file;
                    }
                }
            }

            $password_hash = password_hash($password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("INSERT INTO users (full_name, email, password, profile_pic) VALUES (?, ?, ?, ?)");
            $stmt->bind_param("ssss", $full_name, $email, $password_hash, $profile_pic);
            $stmt->execute();

            $_SESSION['user'] = [
                'id' => $stmt->insert_id,
                'full_name' => $full_name,
                'email' => $email,
                'profile_pic' => $profile_pic
            ];

            header("Location: dashboard.php");
            exit();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Register - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
      
    body {
        background: url('index.png') no-repeat center center fixed;
        background-size: cover;
        font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    }

    .form-container {
        max-width: 500px;
        margin: 80px auto;
        background-color: #f0f4f8; /* New form background */
        padding: 40px;
        border-radius: 16px;
        box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
    }

    .form-container h2 {
        margin-bottom: 30px;
        font-weight: 600;
        color: #2c3e50;
    }

    .form-control:focus {
        border-color: #007bff;
        box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25);
    }

    .btn-primary {
        width: 100%;
            padding: 10px;
            font-weight: 600;
            background: linear-gradient(to right, #28a745, #218838);
            border: none;
            color: white;
            transition: background 0.3s ease;
    }

    .btn-primary:hover {
        background: linear-gradient(to right, #0056b3, #003f7f);
    }

    .form-footer {
        text-align: center;
        margin-top: 15px;
    }

    .form-footer a {
        display: inline-block;
        margin-top: 10px;
        padding: 8px 16px;
        border-radius: 8px;
        text-decoration: none;
        background-color: #6c757d;
        color: white;
        transition: background-color 0.3s ease;
    }

    .form-footer a:hover {
        background-color: #5a6268;
    }

    label {
        font-weight: 500;
    }

    .alert {
        font-size: 0.95rem;
    }
</style>

</head>
<body>
    <div class="form-container">
        <h2 class="text-center">Create Your Family Account</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="full_name">Full Name</label>
                <input type="text" name="full_name" id="full_name" class="form-control" required maxlength="30">
            </div>
            <div class="mb-3">
                <label for="email">Email Address</label>
                <input type="email" name="email" id="email" class="form-control" required maxlength="30">
            </div>
            <div class="mb-3">
                <label for="password">Password</label>
                <input type="password" name="password" id="password" class="form-control" required maxlength="30">
            </div>
            <div class="mb-3">
                <label for="confirm_password">Confirm Password</label>
                <input type="password" name="confirm_password" id="confirm_password" class="form-control" required maxlength="30">
            </div>
            <div class="mb-3">
                <label for="profile_pic">Upload Profile Picture</label>
                <input type="file" name="profile_pic" id="profile_pic" class="form-control">
                <small class="text-muted">Accepted: JPG, PNG, GIF (Max 2MB)</small>
            </div>
            <button type="submit" class="btn btn-primary">Register</button>

            <div class="form-footer">
                <a href="login.php" class="text-decoration-none">Already have an account? Login here</a>
            </div>
        </form>
    </div>
</body>
</html>
