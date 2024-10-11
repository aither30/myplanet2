<?php
session_start();
include("../config/config.php");

$cartId = $_POST['cart_id'];
$quantityChange = $_POST['quantity_change'];

// Cek apakah cart ID valid
$sql = "SELECT quantity FROM cart WHERE cart_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $cartId);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $newQuantity = $row['quantity'] + $quantityChange;

    if ($newQuantity > 0) {
        // Update kuantitas di database
        $updateSql = "UPDATE cart SET quantity = ? WHERE cart_id = ?";
        $updateStmt = $koneksi->prepare($updateSql);
        $updateStmt->bind_param("ii", $newQuantity, $cartId);
        $updateStmt->execute();

        echo json_encode(['status' => 'success', 'new_quantity' => $newQuantity]);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Quantity cannot be less than 1']);
    }
} else {
    echo json_encode(['status' => 'error', 'message' => 'Cart item not found']);
}

$koneksi->close();
?>
