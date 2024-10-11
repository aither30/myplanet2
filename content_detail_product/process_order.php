<?php
session_start();

include ("../config/config.php");


// Ambil data pengguna dari session
$username = $_SESSION['username'];

// Pastikan ada data keranjang yang sudah dipilih
if (!isset($_SESSION['selected_cart'])) {
    echo "Keranjang belanja tidak ditemukan.";
    exit();
}

$selectedCart = $_SESSION['selected_cart'];
$success = true;

// Fungsi untuk mengurangi stok dan menghapus item dari cart jika stok atau quantity menjadi 0
function processCartAndStock($koneksi, $cartItem, $username) {
    // Ambil informasi produk dari tabel product
    $sql = "SELECT stocks FROM product WHERE product_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $cartItem['id']);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();

    if ($product) {
        $currentStock = $product['stocks'];
        $quantityPurchased = $cartItem['quantity'];

        // Jika stok saat ini lebih besar atau sama dengan quantity yang dibeli
        if ($currentStock >= $quantityPurchased) {
            // Kurangi stok
            $newStock = $currentStock - $quantityPurchased;

            if ($newStock > 0) {
                // Update stok di database
                $updateStockSql = "UPDATE product SET stocks = ? WHERE product_id = ?";
                $updateStockStmt = $koneksi->prepare($updateStockSql);
                $updateStockStmt->bind_param("ii", $newStock, $cartItem['id']);
                $updateStockStmt->execute();
            } else {
                // Jika stok 0 setelah dikurangi, hapus produk dari tabel product
                $deleteProductSql = "DELETE FROM product WHERE product_id = ?";
                $deleteProductStmt = $koneksi->prepare($deleteProductSql);
                $deleteProductStmt->bind_param("i", $cartItem['id']);
                $deleteProductStmt->execute();
            }

            // Hapus item dari cart
            $deleteCartSql = "DELETE FROM cart WHERE product_id = ? AND username = ?";
            $deleteCartStmt = $koneksi->prepare($deleteCartSql);
            $deleteCartStmt->bind_param("is", $cartItem['id'], $username);
            $deleteCartStmt->execute();

        } else {
            // Stok tidak mencukupi
            echo "Stok tidak cukup untuk produk: " . $cartItem['name'] . " - Rp. " . number_format($cartItem['price'], 0, ',', '.') . "<br>";
            return false;
        }
    } else {
        echo "Produk tidak ditemukan untuk: " . $cartItem['name'] . "<br>";
        return false;
    }

    return true;
}

// Proses setiap item di keranjang belanja
foreach ($selectedCart as $cartItem) {
    if (!processCartAndStock($koneksi, $cartItem, $username)) {
        $success = false;
        break;
    }
}

if ($success) {
    echo "Pesanan berhasil diproses. Terima kasih telah berbelanja!";
    // Redirect ke halaman sukses
    header("Location: success_page.php");
} else {
    echo "Gagal memproses pesanan. Silakan coba lagi.";
}

$koneksi->close();
?>
