<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user']; // Retrieve user data from session

// Database connection
require_once 'connect.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the updated details from the form
    $full_name = $_POST['full_name'];
    $email = $_POST['email'];

    // Default profile picture is the current one
    $profile_pic = $user['profile_pic'];

    // Check if a new profile picture was uploaded
    if (isset($_FILES['profile_pic']) && $_FILES['profile_pic']['error'] == 0) {
        $target_dir = "uploads/"; // Directory to save the image
        $target_file = $target_dir . basename($_FILES["profile_pic"]["name"]);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES["profile_pic"]["tmp_name"], $target_file)) {
            $profile_pic = $target_file;
        }
    }

    // Update the user's details in the database
    $user_id = $user['id']; // Assuming 'id' is available in session
    $query = "UPDATE users SET full_name = ?, email = ?, profile_pic = ? WHERE id = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssi", $full_name, $email, $profile_pic, $user_id);

    if ($stmt->execute()) {
        // Update the session with the new user data
        $_SESSION['user']['full_name'] = $full_name;
        $_SESSION['user']['email'] = $email;
        $_SESSION['user']['profile_pic'] = $profile_pic;

        // Redirect to the dashboard or profile page
        header("Location: dashboard.php");
        exit();
    } else {
        echo "Error updating profile: " . $stmt->error;
    }
}
?>
