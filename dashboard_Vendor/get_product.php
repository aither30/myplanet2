<?php
session_start();
include ("../config/config.php");

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    http_response_code(403); // Forbidden
    echo json_encode(["error" => "Unauthorized access. Please login."]);
    exit();
}

$username = $_SESSION['username'];

// Ambil data vendor berdasarkan username
$sql = "SELECT * FROM business_account WHERE username = '$username'";
$result = $koneksi->query($sql);

if ($result->num_rows > 0) {
    $vendor = $result->fetch_assoc();
    $vendor_id = $vendor['vendor_id'];
} else {
    http_response_code(404); // Not Found
    echo json_encode(["error" => "Vendor not found."]);
    exit();
}

// Pastikan product_id dikirimkan melalui parameter URL
if (!isset($_GET['product_id']) || empty($_GET['product_id'])) {
    http_response_code(400); // Bad Request
    echo json_encode(["error" => "No product ID provided."]);
    exit();
}

$product_id = $_GET['product_id'];

// Ambil data produk terkait vendor dan product_id
$product_sql = "SELECT * FROM product WHERE vendor_id = '$vendor_id' AND product_id = '$product_id'";
$product_result = $koneksi->query($product_sql);

if ($product_result->num_rows > 0) {
    $product = $product_result->fetch_assoc();
    // Mengembalikan data produk dalam format JSON
    echo json_encode($product);
} else {
    http_response_code(404); // Not Found
    echo json_encode(["error" => "Product not found."]);
}
?>
