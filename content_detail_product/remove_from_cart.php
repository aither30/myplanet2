<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu.']);
    exit;
}

include ("../config/config.php");


$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;

if ($cart_id > 0) {
    $sql = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    
    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil dihapus dari keranjang']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'ID keranjang tidak valid']);
}

$koneksi->close();
?>
