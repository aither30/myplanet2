<?php
session_start();

include("../config/config.php");
// Ambil username dari session
$username = $_SESSION['username'];

// Query untuk mengambil item keranjang beserta informasi vendor dan stok produk
$sql = "SELECT c.cart_id, c.quantity, p.name, p.prices, p.images, p.stocks, b.company_name, b.logo
        FROM cart c
        JOIN product p ON c.product_id = p.product_id
        JOIN business_account b ON p.vendor_id = b.vendor_id
        WHERE c.username = ?";

$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Variabel untuk menyimpan produk berdasarkan vendor
$vendorProducts = [];

// Loop melalui hasil dan kelompokkan produk berdasarkan vendor
while ($row = $result->fetch_assoc()) {
    $vendorName = $row['company_name'];

    // Jika vendor belum ada di array, tambahkan
    if (!isset($vendorProducts[$vendorName])) {
        $vendorProducts[$vendorName] = [
            'vendor_name' => $row['company_name'],
            'vendor_logo' => $row['logo'],
            'products' => []
        ];
    }

    // Tambahkan produk ke array vendor yang sesuai
    $vendorProducts[$vendorName]['products'][] = $row;
}

if (empty($vendorProducts)) {
    echo '<p>Keranjang kosong.</p>';
} else {
    // Loop melalui setiap vendor dan tampilkan produk mereka
    foreach ($vendorProducts as $vendor) {
        echo '
        <div class="cart-item">
            <div class="vendor-info">
                <img src="../dashboard_vendor/' . htmlspecialchars($vendor['vendor_logo']) . '" alt="' . htmlspecialchars($vendor['vendor_name']) . '" class="vendor-logo" />
                <p class="vendor-name">' . htmlspecialchars($vendor['vendor_name']) . '</p>
            </div>';

        // Tampilkan produk dari vendor tersebut
        foreach ($vendor['products'] as $product) {
            echo '
            <div class="cart-details">
                <img src="../dashboard_vendor/' . htmlspecialchars($product['images']) . '" alt="' . htmlspecialchars($product['name']) . '" class="cart-image" />
                <div class="info-details">
                    <p>' . htmlspecialchars($product['name']) . ' - Rp ' . number_format($product['prices'], 2) . '</p>
                    <p>Quantity: 
                        <button class="quantity-btn" onclick="updateQuantity(' . $product['cart_id'] . ', -1)">-</button>
                        <span class="quantity-value" id="quantity-' . $product['cart_id'] . '">' . $product['quantity'] . '</span>
                        <button class="quantity-btn" onclick="updateQuantity(' . $product['cart_id'] . ', 1)">+</button>
                    </p>
                    <p>Total: Rp. ' . number_format($product['prices'] * $product['quantity'], 2) . '</p>
                    <button class="remove-btn" onclick="removeFromCart(' . $product['cart_id'] . ')">Hapus</button>
                </div>
            </div>';

            // Cek apakah stok sudah habis
            if ($product['stocks'] <= 0) {
                echo '<p class="out-of-stock">Barang habis!</p>';
            }
        }

        echo '</div>';  // Menutup cart-item untuk satu vendor
    }
}

$koneksi->close();
?>
