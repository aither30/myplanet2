<?php
session_start(); // Memulai session

include ("../config/config.php");

$receiver_id = $_GET['receiver_id'];
$sender_id = $_SESSION['account_id'];

// Ambil pesan dari database
$result = $koneksi->query("SELECT * FROM messages WHERE (sender_id=$sender_id AND receiver_id=$receiver_id) OR (sender_id=$receiver_id AND receiver_id=$sender_id) ORDER BY sent_at");

while ($row = $result->fetch_assoc()) {
    if ($row['sender_id'] == $sender_id) {
        echo '<div class="message message-sent">' . htmlspecialchars($row['message_content']) . '</div>';
    } else {
        echo '<div class="message message-received">' . htmlspecialchars($row['message_content']) . '</div>';
    }
}

$koneksi->close();
?>
