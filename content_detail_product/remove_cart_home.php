<?php
session_start();
include("../config/config.php");

$cartId = $_POST['cart_id'];

// Hapus produk dari keranjang
$sql = "DELETE FROM cart WHERE cart_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $cartId);
$stmt->execute();

if ($stmt->affected_rows > 0) {
    echo json_encode(['status' => 'success']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Failed to remove item from cart']);
}

$koneksi->close();
?>
