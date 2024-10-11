<?php
include ("../config/config.php");


// Ambil data JSON yang dikirim dari frontend
$data = json_decode(file_get_contents('php://input'), true);

// Ambil data yang relevan
$orderId = $data['order_id'];
$transactionStatus = $data['transaction_status'];
$selectedProducts = $data['selected_products'];
$totalAmount = $data['total_amount'];
$username = $data['username'];

// Ambil user ID dari username
$userQuery = $koneksi->prepare("SELECT user_id FROM user_account WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();
$userId = $user['user_id'];

// Insert data transaksi ke dalam tabel `transaction`
$insertTransaction = $koneksi->prepare("INSERT INTO `transaction` (payment_id, user_id, amount, payment_method, payment_date, status) VALUES (?, ?, ?, ?, NOW(), ?)");
$paymentMethod = 'midtrans'; // Sesuaikan jika ada beberapa metode pembayaran
$insertTransaction->bind_param("iisss", $orderId, $userId, $totalAmount, $paymentMethod, $transactionStatus);
$insertTransaction->execute();

// Jika transaksi berhasil, kurangi stok produk dan hapus dari keranjang
if ($transactionStatus === 'success') {
    foreach ($selectedProducts as $product) {
        $productId = $product['id'];
        $quantityPurchased = $product['quantity'];

        // Update stok produk
        $updateStock = $koneksi->prepare("UPDATE product SET stock = stock - ? WHERE product_id = ?");
        $updateStock->bind_param("ii", $quantityPurchased, $productId);
        $updateStock->execute();

        // Hapus produk dari keranjang setelah berhasil checkout
        $deleteCart = $koneksi->prepare("DELETE FROM cart WHERE product_id = ? AND username = ?");
        $deleteCart->bind_param("is", $productId, $username);
        $deleteCart->execute();
    }
}

// Tutup koneksi
$insertTransaction->close();
$koneksi->close();

// Beri respon ke frontend
echo json_encode(["status" => "success"]);
?>
