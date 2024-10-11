<?php
// check_stock.php
session_start();

include ("../config/config.php");


if (isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];
    
    // Ambil stok dari tabel product
    $sql = "SELECT p.stocks 
            FROM cart c 
            JOIN product p ON c.product_id = p.product_id 
            WHERE c.cart_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $cart_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    
    if ($row) {
        echo json_encode(['stocks' => $row['stocks']]);
    } else {
        echo json_encode(['stocks' => 0]);
    }
} else {
    echo json_encode(['stocks' => 0]);
}

$koneksi->close();
?>
