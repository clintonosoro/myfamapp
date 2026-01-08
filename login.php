<?php
session_start();
require 'connect.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = $_POST['email'];
    $pass = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if (password_verify($pass, $user['password'])) {
            // Securely set only the required session fields
            $_SESSION['user'] = [
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'role' => $user['role'] ?? 'Member',
                'profile_pic' => $user['profile_pic'] ?? 'default_profile.png'
            ];
            header("Location: dashboard.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "User not found.";
    }
}
?>


<!DOCTYPE html>
<html>
<head>
    <title>Login - MyFamApp</title>
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
            background-color: #f0f4f8;
            padding: 40px;
            border-radius: 16px;
            box-shadow: 0 0 20px rgba(0, 0, 0, 0.2);
        }

        .form-container h2 {
            margin-bottom: 30px;
            font-weight: 600;
            color: #2c3e50;
            text-align: center;
        }

        .form-control:focus {
            border-color: #28a745;
            box-shadow: 0 0 0 0.2rem rgba(40, 167, 69, 0.25);
        }

        .btn-success {
            width: 100%;
            padding: 10px;
            font-weight: 600;
            background: linear-gradient(to right, #28a745, #218838);
            border: none;
            color: white;
            transition: background 0.3s ease;
        }

        .btn-success:hover {
            background: linear-gradient(to right, #218838, #1c7430);
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

        .alert {
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2>Login to MyFamApp</h2>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST">
            <div class="mb-3">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required maxlength="30">
            </div>
            <div class="mb-3">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required maxlength="15">
            </div>
            <button type="submit" class="btn btn-success">Login</button>
        </form>

        <div class="form-footer">
            <p>Don't have an account?</p>
            <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>
