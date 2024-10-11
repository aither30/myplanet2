<?php
session_start(); // Pastikan session tetap aktif
$account_id = $_SESSION['account_id'];

include ("../config/config.php");

// Ambil daftar akun kecuali akun yang sedang login serta pesan terbaru
$query = "
    SELECT a.account_id, a.username, 
    (SELECT m.message_content FROM messages m WHERE (m.sender_id = a.account_id OR m.receiver_id = a.account_id) AND (m.sender_id = $account_id OR m.receiver_id = $account_id) ORDER BY m.sent_at DESC LIMIT 1) AS last_message,
    (SELECT m.sent_at FROM messages m WHERE (m.sender_id = a.account_id OR m.receiver_id = a.account_id) AND (m.sender_id = $account_id OR m.receiver_id = $account_id) ORDER BY m.sent_at DESC LIMIT 1) AS last_message_time,
    (SELECT COUNT(*) FROM messages m WHERE m.sender_id = a.account_id AND m.receiver_id = $account_id AND m.is_read = 0) AS unread_messages
    FROM account a
    WHERE a.account_id != $account_id";

$result = $koneksi->query($query);

if ($result) {
    while ($row = $result->fetch_assoc()) {
        $username = htmlspecialchars($row['username']);
        $lastMessage = htmlspecialchars($row['last_message']);
        $lastMessageTime = htmlspecialchars($row['last_message_time']);
        $unreadMessages = (int)$row['unread_messages'];

        // Simpan nama pengguna di data atribut
        echo '<div class="account-card" data-receiver-id="' . $row['account_id'] . '" data-username="' . $username . '">';
        echo '<h3>' . $username . '</h3>';
        echo '<p>' . $lastMessage . '</p>';
        
        echo '</div>';
    }
} else {
    echo "Error dalam pengambilan data: " . $koneksi->error;
}

$koneksi->close();
?>
