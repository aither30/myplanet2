<?php
session_start();
include("../config/config.php");

if (isset($_POST['cart_id'])) {
    $cart_id = intval($_POST['cart_id']);

    // Hapus item dari keranjang
    $stmt = $koneksi->prepare("DELETE FROM cart WHERE cart_id = ?");
    $stmt->bind_param("i", $cart_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menghapus item']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
}
?>
