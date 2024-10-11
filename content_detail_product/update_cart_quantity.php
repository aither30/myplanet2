<?php
session_start();

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu.']);
    exit;
}

include ("../config/config.php");

$cart_id = isset($_POST['cart_id']) ? (int)$_POST['cart_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

if ($cart_id > 0 && $quantity > 0) {
    $sql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("ii", $quantity, $cart_id);
    $stmt->execute();
    
    echo json_encode(['status' => 'success', 'message' => 'Quantity berhasil diperbarui']);
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
}

$koneksi->close();
?>
