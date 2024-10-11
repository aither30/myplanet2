<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    echo json_encode(['status' => 'error', 'message' => 'Anda harus login terlebih dahulu.']);
    exit;
}


include ("../config/config.php");


// Ambil data dari request
$product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : 0;
$quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 0;

// Periksa apakah produk dan jumlah valid
if ($product_id <= 0 || $quantity <= 0) {
    echo json_encode(['status' => 'error', 'message' => 'Data produk atau jumlah tidak valid.']);
    exit;
}

// Ambil username dari session
$username = $_SESSION['username'];
$product_id = $_POST['product_id'];
$quantity = $_POST['quantity'];


// Cek apakah produk sudah ada di keranjang
$sql_check = "SELECT * FROM cart WHERE username = ? AND product_id = ?";
$stmt_check = $koneksi->prepare($sql_check);
$stmt_check->bind_param("si", $username, $product_id);
$stmt_check->execute();
$result_check = $stmt_check->get_result();

if ($result_check->num_rows > 0) {
    // Jika produk sudah ada, update jumlah
    $sql_update = "UPDATE cart SET quantity = quantity + ? WHERE username = ? AND product_id = ?";
    $stmt_update = $koneksi->prepare($sql_update);
    $stmt_update->bind_param("isi", $quantity, $username, $product_id);
    if ($stmt_update->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Jumlah produk di keranjang berhasil diperbarui']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal memperbarui produk di keranjang']);
    }
} else {
    // Jika produk belum ada, tambahkan ke keranjang
    $sql_insert = "INSERT INTO cart (username, product_id, quantity) VALUES (?, ?, ?)";
    $stmt_insert = $koneksi->prepare($sql_insert);
    $stmt_insert->bind_param("sii", $username, $product_id, $quantity);
    if ($stmt_insert->execute()) {
        echo json_encode(['status' => 'success', 'message' => 'Produk berhasil ditambahkan ke keranjang']);
    } else {
        echo json_encode(['status' => 'error', 'message' => 'Gagal menambahkan produk ke keranjang']);
    }
}

$koneksi->close();
?>
