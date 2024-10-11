<?php
session_start();
include ("../config/config.php");


// Pastikan pengguna sudah login dan ambil username
if (!isset($_SESSION['username'])) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu']);
    exit;
}

$username = $_SESSION['username'];
$product_id = $_POST['product_id'];
$quantity = (int) $_POST['quantity'];

// Cek apakah produk sudah ada di keranjang
$sql_check = "SELECT * FROM cart WHERE username = ? AND product_id = ?";
$stmt_check = $koneksi->prepare($sql_check);
$stmt_check->bind_param("si", $username, $product_id);
$stmt_check->execute();
$result = $stmt_check->get_result();

if ($result->num_rows > 0) {
    // Produk sudah ada di keranjang, update kuantitas
    $row = $result->fetch_assoc();
    $new_quantity = $row['quantity'] + $quantity;
    $sql_update = "UPDATE cart SET quantity = ? WHERE username = ? AND product_id = ?";
    $stmt_update = $koneksi->prepare($sql_update);
    $stmt_update->bind_param("isi", $new_quantity, $username, $product_id);
    $stmt_update->execute();
    
    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil diperbarui di keranjang']);
} else {
    // Produk belum ada, tambahkan ke keranjang
    $sql_insert = "INSERT INTO cart (username, product_id, quantity) VALUES (?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);
    $stmt_insert->bind_param("sii", $username, $product_id, $quantity);
    $stmt_insert->execute();
    
    echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang']);
}

$koneksi->close();
?>
