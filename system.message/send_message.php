<?php
session_start(); // Memulai session

include ("../config/config.php");


if (isset($_POST['message_content']) && isset($_POST['receiver_id'])) {
    $message_content = $koneksi->real_escape_string($_POST['message_content']);
    $receiver_id = $_POST['receiver_id'];
    $sender_id = $_SESSION['account_id'];

    // Simpan pesan ke database
    $stmt = $koneksi->prepare("INSERT INTO messages (sender_id, receiver_id, message_content) VALUES (?, ?, ?)");
    $stmt->bind_param("iis", $sender_id, $receiver_id, $message_content);
    $stmt->execute();
}

$stmt->close();
$koneksi->close();
?>
