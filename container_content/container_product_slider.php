<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "myplanet_db";

$koneksi = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil daftar produk dari tabel product di database
$sql = "SELECT * FROM product";
$result = $koneksi->query($sql);

echo '<div class="container_product slider">';


// Loop melalui setiap produk di database
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ambil data produk dari database
        $productName = htmlspecialchars($row['name']);
        $productPrice = number_format($row['prices'], 2);
        $productImage = !empty($row['images']) ? '../dashboard_vendor/' . $row['images'] : '../assets/placeholder.jpg'; // Gambar produk atau default
        $productDescription = !empty($row['description']) ? htmlspecialchars($row['description']) : 'No Description';
        $productDiscount = "20% OFF"; // Contoh diskon tetap, ini bisa diubah sesuai data
        $linkdetailproduct = "../content_detail_product/index.php?product_id=" . $row['product_id']; // Tambahkan product_id ke URL
        $linkbuyproduct = "../content_payment_gateway/index.html";

        // Output produk dalam bentuk kartu
        echo '
        <div class="product-card">
            <div class="product-image">
                <img src="' . $productImage . '" alt="' . $productName . '">
            </div>
            <div class="product-details">
                <h3 class="product-name">' . $productName . '</h3>
                <p class="product-price">Rp ' . $productPrice . '</p>
            </div>
            <div class="product-actions">
                <button class="btn-add-to-cart" onclick="location.href=\'' . $linkdetailproduct . '\'">Lihat Detail</button>
                <button class="btn-buy-now" onclick="location.href=\'' . $linkbuyproduct . '\'">Buy Now</button>
            </div>
        </div>';
    }
} else {
    echo "<p>Tidak ada produk tersedia.</p>";
}

echo '</div>';

// Tutup koneksi
$koneksi->close();
?>
