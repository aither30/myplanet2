<?php
session_start();

include ("../config/config.php");


// Ambil data yang dikirim dari JavaScript
$cart_id = $_POST['cart_id'];
$new_quantity = $_POST['quantity'];

// Query untuk memperbarui quantity di tabel cart
$sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("ii", $new_quantity, $cart_id);

if ($stmt->execute()) {
    // Jika berhasil
    echo json_encode(["success" => true]);
} else {
    // Jika gagal
    echo json_encode(["success" => false]);
}

$stmt->close();
$koneksi->close();
?>
