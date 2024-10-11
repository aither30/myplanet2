<?php
// Koneksi database
include ("../config/config.php");

// Cek koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

$account_id = $_SESSION['account_id']; // ID akun pengguna yang login

// Mengambil pesan yang terkait dengan akun pengguna yang login
$sql = "SELECT * FROM system_message 
        WHERE sender_id = ? OR receiver_id = ? 
        ORDER BY timestamp ASC";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ii", $account_id, $account_id);
$stmt->execute();
$result = $stmt->get_result();

echo "<h2>Pesan</h2>";
while ($row = $result->fetch_assoc()) {
    $sender_id = $row['sender_id'];
    $receiver_id = $row['receiver_id'];
    $message = $row['message'];
    $timestamp = $row['timestamp'];

    // Menampilkan pesan, bisa ditambahkan styling
    echo "<p><strong>Dari ID $sender_id ke ID $receiver_id:</strong> $message <br><small>$timestamp</small></p>";
}

$stmt->close();
$koneksi->close();
?>
