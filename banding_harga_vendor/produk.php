<?php
include ("../config/config.php");

// Query untuk mengambil data produk beserta data vendor terkait
$sql = "SELECT p.product_id, p.name AS product_name, p.prices, p.spesifikasi, p.images, 
               b.company_name, b.logo, b.phone_vendor
        FROM product p
        LEFT JOIN business_account b ON p.vendor_id = b.vendor_id"; // Menggunakan LEFT JOIN

$result = $koneksi->query($sql);

$products = [];
if ($result->num_rows > 0) {
    // Masukkan hasil query ke dalam array
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
}

// Mengembalikan data dalam format JSON untuk AJAX
header('Content-Type: application/json');
echo json_encode($products);
?>
