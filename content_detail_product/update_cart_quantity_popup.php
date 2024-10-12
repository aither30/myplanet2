<?php
session_start();
include("../config/config.php");

if (isset($_POST['cart_id']) && isset($_POST['quantity'])) {
    $cart_id = intval($_POST['cart_id']);
    $quantity = intval($_POST['quantity']);

    if ($quantity < 1) {
        echo json_encode(['status' => 'error', 'message' => 'Invalid quantity']);
        exit;
    }

    // Update quantity di database
    $stmt = $koneksi->prepare("UPDATE cart SET quantity = ? WHERE cart_id = ?");
    $stmt->bind_param("ii", $quantity, $cart_id);
    
    if ($stmt->execute()) {
        echo json_encode(['status' => 'success']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui quantity']);
    }

    $stmt->close();
} else {
    echo json_encode(['status' => 'error', 'message' => 'Data tidak valid']);
}
?>
