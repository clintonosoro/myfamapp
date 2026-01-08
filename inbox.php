<?php
session_start();
require 'connect.php';

if (!isset($_SESSION['user'])) {
    header("Location: login.php");
    exit();
}

$user = $_SESSION['user'];
$user_id = intval($user['id']);

// Fetch all family members except the logged-in user
$users_query = $conn->query("SELECT id, full_name, profile_pic FROM users WHERE id != $user_id");
$users = $users_query->fetch_all(MYSQLI_ASSOC);

// Initialize
$chat_with_user = null;
$chat_messages = [];

if (isset($_GET['chat_with'])) {
    $chat_with = intval($_GET['chat_with']);

    // Fetch selected user details
    $stmt = $conn->prepare("SELECT id, full_name, profile_pic FROM users WHERE id = ?");
    $stmt->bind_param("i", $chat_with);
    $stmt->execute();
    $chat_with_result = $stmt->get_result();
    $chat_with_user = $chat_with_result->fetch_assoc();

    // Fetch chat messages
    $stmt = $conn->prepare("
        SELECT m.*, u.full_name AS sender_name, u.profile_pic AS sender_pic
        FROM messages m
        JOIN users u ON m.sender_id = u.id
        WHERE (m.sender_id = ? AND m.receiver_id = ?)
           OR (m.sender_id = ? AND m.receiver_id = ?)
        ORDER BY m.sent_at ASC
    ");
    $stmt->bind_param("iiii", $user_id, $chat_with, $chat_with, $user_id);
    $stmt->execute();
    $chat_messages = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

// Handle message sending
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['message'])) {
    $message = $conn->real_escape_string($_POST['message']);
    $receiver_id = intval($_POST['receiver_id']);

    $stmt = $conn->prepare("INSERT INTO messages (sender_id, receiver_id, message) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $user_id, $receiver_id, $message);
    $stmt->execute();

    header("Location: inbox.php?chat_with=$receiver_id");
    exit();
}

// Handle message deletion
if (isset($_GET['delete_id'])) {
    $delete_id = intval($_GET['delete_id']);
    $stmt = $conn->prepare("DELETE FROM messages WHERE id = ? AND (sender_id = ? OR receiver_id = ?)");
    $stmt->bind_param("iii", $delete_id, $user_id, $user_id);
    $stmt->execute();

    if (isset($_GET['chat_with'])) {
        $chat_with = intval($_GET['chat_with']);
        header("Location: inbox.php?chat_with=$chat_with");
    } else {
        header("Location: inbox.php");
    }
    exit();
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Inbox - MyFamApp</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
  body {
    font-family: 'Roboto', sans-serif;
    background: linear-gradient(145deg, #6a82fb, #fc5c7d); /* Gradient background */
    margin: 0;
}

.chat-wrapper {
    display: flex;
    height: 85vh;
    background: #2c3e50; /* Dark background for the chat area */
    border-radius: 10px;
    overflow: hidden;
    box-shadow: 0 5px 25px rgba(0, 0, 0, 0.08);
    margin: 20px;
}

.user-list {
    width: 25%;
    background: #34495e; /* Dark sidebar */
    color: white;
    border-right: 1px solid #e0e0e0;
    overflow-y: auto;
}

.user-list a {
    display: flex;
    align-items: center;
    padding: 12px 18px;
    text-decoration: none;
    color: #ecf0f1;
    border-bottom: 1px solid #7f8c8d;
    transition: background-color 0.2s;
}

.user-list a:hover,
.user-list a.active {
    background-color: #2980b9; /* Active and hover color */
}

.user-list img {
    border-radius: 50%;
    width: 42px;
    height: 42px;
    object-fit: cover;
    margin-right: 12px;
    border: 2px solid #d4d4d4;
}

.chat-section {
    flex: 1;
    display: flex;
    flex-direction: column;
}

.chat-header {
    padding: 15px 20px;
    background-color: #34495e; /* Dark header */
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
}

.chat-header img {
    border-radius: 50%;
    width: 42px;
    height: 42px;
    margin-right: 10px;
    border: 2px solid #ccc;
}

.chat-messages {
    flex: 1;
    padding: 20px;
    overflow-y: auto;
    background: #ecf0f1; /* Light message area */
    display: flex;
    flex-direction: column;
    gap: 8px;
}

.message-bubble {
    max-width: 65%;
    padding: 12px 16px;
    border-radius: 18px;
    font-size: 14px;
    position: relative;
    word-wrap: break-word;
    white-space: pre-wrap;
    box-shadow: 0 1px 4px rgba(0, 0, 0, 0.05);
}

.sent {
    align-self: flex-end;
    background-color: #3498db; /* Sent message color */
}

.received {
    align-self: flex-start;
    background-color: #ffffff; /* Received message color */
}

.message-time {
    font-size: 11px;
    color: #7f8c8d;
    text-align: right;
    margin-top: 5px;
}

.chat-input {
    display: flex;
    align-items: center;
    padding: 15px 20px;
    background-color: #34495e; /* Dark input area */
    border-top: 1px solid #e0e0e0;
}

.chat-input textarea {
    flex: 1;
    border-radius: 20px;
    border: 1px solid #ccc;
    padding: 10px 15px;
    height: 44px;
    resize: none;
    font-size: 14px;
}

.chat-input button {
    border: none;
    border-radius: 20px;
    background-color: #2980b9; /* Button color */
    color: white;
    padding: 10px 20px;
    margin-left: 10px;
    font-size: 14px;
    transition: background-color 0.2s;
}

.chat-input button:hover {
    background-color: #1f618d; /* Hover color */
}

.delete-msg {
    position: absolute;
    top: 6px;
    right: 10px;
    font-size: 12px;
    color: red;
    display: none;
    cursor: pointer;
}

.message-bubble:hover .delete-msg {
    display: inline;
}

.call-icons a {
    color: inherit;
    margin-left: 12px;
    font-size: 18px;
    transition: transform 0.2s ease, color 0.2s;
}

.call-icons a:hover {
    transform: scale(1.2);
    color: #007bff !important;
}


</style>

</head>
<body>
<?php include 'navbar.php'; ?>
<div class="container mt-4">
    <div class="chat-wrapper">
        <!-- User List -->
        <div class="user-list">
            <?php foreach ($users as $u): ?>
                <a href="inbox.php?chat_with=<?= $u['id'] ?>" class="<?= (isset($_GET['chat_with']) && $_GET['chat_with'] == $u['id']) ? 'active' : '' ?>">
                    <img src="<?= $u['profile_pic'] ?>" alt="">
                    <?= htmlspecialchars($u['full_name']) ?>
                </a>
              <!-- <a href="dashboard.php">home</a>-->
            <?php endforeach; ?>
        </div>

        <!-- Chat Section -->
        <div class="chat-section">
            <?php if (isset($chat_with)): ?>
                <div class="chat-header d-flex justify-content-between align-items-center mb-3">
    <div class="d-flex align-items-center">
        <img src="<?= $chat_with_user['profile_pic'] ?>" alt="" style="width: 40px; height: 40px; border-radius: 50%; margin-right: 10px;">
        <strong><?= htmlspecialchars($chat_with_user['full_name']) ?></strong>
    </div>
    <div class="call-icons">
        <a href="#" title="Voice Chat" class="text-primary me-3"><i class="fas fa-microphone"></i></a>
        <a href="#" title="Voice Call" class="text-success me-3"><i class="fas fa-phone"></i></a>
        <a href="#" title="Video Call" class="text-danger"><i class="fas fa-video"></i></a>
    </div>
</div>


                <!-- Messages -->
                <div class="chat-messages" id="messageArea">
                    <?php foreach ($chat_messages as $msg): ?>
                        <?php
                            $is_sender = $msg['sender_id'] == $user_id;
                            $class = $is_sender ? 'sent' : 'received';
                        ?>
                        <div class="message-bubble <?= $class ?>">
                            <?= htmlspecialchars($msg['message']) ?>
                            <div class="message-time">
                                <?= date("H:i", strtotime($msg['sent_at'])) ?>
                            </div>
                            <?php if ($is_sender): ?>
                                <a href="inbox.php?chat_with=<?= $chat_with ?>&delete_id=<?= $msg['id'] ?>" class="delete-msg" title="Delete">🗑</a>
                            <?php endif; ?>
                        </div>
                    <?php endforeach; ?>
                </div>

                <!-- Message Input -->
                <form method="POST" class="chat-input">
                    
                    <input type="hidden" name="receiver_id" value="<?= $chat_with ?>">
                    <textarea name="message" placeholder="Type a message..." required></textarea>
                    <button type="submit">Send</button>
                    
                </form>
            <?php else: ?>
                <div class="text-center mt-5 w-100">
                    <h5>Select a family member to start chatting</h5>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
    // Scroll to bottom of messages
    const messageArea = document.getElementById('messageArea');
    if (messageArea) {
        messageArea.scrollTop = messageArea.scrollHeight;
    }
</script>
</body>

