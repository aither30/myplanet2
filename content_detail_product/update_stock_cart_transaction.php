<?php
// Koneksi ke database
include ("../config/config.php");

// Ambil data JSON dari permintaan
$data = json_decode(file_get_contents('php://input'), true);
$selectedProducts = $data['selected_products'];
$username = $data['username'];
$orderId = $data['order_id'];
$totalAmount = $data['total_amount'];
$transactionDetails = $data['transaction_details'];

// Ambil user ID dari tabel user_account
$userQuery = $koneksi->prepare("SELECT user_id FROM user_account WHERE username = ?");
if (!$userQuery) {
    die(json_encode(["status" => "error", "message" => "Query gagal: " . $koneksi->error]));
}
$userQuery->bind_param("s", $username);
$userQuery->execute();
$user = $userQuery->get_result()->fetch_assoc();
if (!$user) {
    die(json_encode(["status" => "error", "message" => "User tidak ditemukan"]));
}
$userId = $user['user_id'];

// Loop melalui produk yang dibeli untuk mengurangi stok dan memperbarui keranjang
foreach ($selectedProducts as $product) {
    $productName = $product['name'];
    $quantityPurchased = $product['quantity'];

    // Kurangi stok produk
    $updateStock = $koneksi->prepare("UPDATE product SET stock = stock - ? WHERE name = ?");
    if (!$updateStock) {
        die(json_encode(["status" => "error", "message" => "Query update stok gagal: " . $koneksi->error]));
    }
    $updateStock->bind_param("is", $quantityPurchased, $productName);
    $updateStock->execute();
    if ($updateStock->affected_rows == 0) {
        die(json_encode(["status" => "error", "message" => "Stok tidak diperbarui untuk produk: " . $productName]));
    }

    // Periksa jika stok produk menjadi 0, hapus barisnya
    $checkStock = $koneksi->prepare("SELECT stock FROM product WHERE name = ?");
    if (!$checkStock) {
        die(json_encode(["status" => "error", "message" => "Query check stok gagal: " . $koneksi->error]));
    }
    $checkStock->bind_param("s", $productName);
    $checkStock->execute();
    $stockResult = $checkStock->get_result()->fetch_assoc();
    if ($stockResult['stock'] <= 0) {
        $deleteProduct = $koneksi->prepare("DELETE FROM product WHERE name = ?");
        if (!$deleteProduct) {
            die(json_encode(["status" => "error", "message" => "Query delete produk gagal: " . $koneksi->error]));
        }
        $deleteProduct->bind_param("s", $productName);
        $deleteProduct->execute();
    }

    // Kurangi kuantitas di keranjang
    $updateCart = $koneksi->prepare("UPDATE cart SET quantity = quantity - ? WHERE username = ? AND product_id = (SELECT product_id FROM product WHERE name = ?)");
    if (!$updateCart) {
        die(json_encode(["status" => "error", "message" => "Query update cart gagal: " . $koneksi->error]));
    }
    $updateCart->bind_param("iss", $quantityPurchased, $username, $productName);
    $updateCart->execute();
    if ($updateCart->affected_rows == 0) {
        die(json_encode(["status" => "error", "message" => "Cart tidak diperbarui untuk produk: " . $productName]));
    }

    // Periksa jika kuantitas di keranjang menjadi 0, hapus barisnya
    $checkCart = $koneksi->prepare("SELECT quantity FROM cart WHERE username = ? AND product_id = (SELECT product_id FROM product WHERE name = ?)");
    if (!$checkCart) {
        die(json_encode(["status" => "error", "message" => "Query check cart gagal: " . $koneksi->error]));
    }
    $checkCart->bind_param("ss", $username, $productName);
    $checkCart->execute();
    $cartResult = $checkCart->get_result()->fetch_assoc();
    if ($cartResult['quantity'] <= 0) {
        $deleteCart = $koneksi->prepare("DELETE FROM cart WHERE username = ? AND product_id = (SELECT product_id FROM product WHERE name = ?)");
        if (!$deleteCart) {
            die(json_encode(["status" => "error", "message" => "Query delete cart gagal: " . $koneksi->error]));
        }
        $deleteCart->bind_param("ss", $username, $productName);
        $deleteCart->execute();
    }
}

// Simpan data transaksi ke tabel 'transaction'
$paymentMethod = $transactionDetails['payment_type'];
$paymentDate = date('Y-m-d H:i:s');
$status = 'completed'; // Transaksi sukses

$insertTransaction = $koneksi->prepare("
    INSERT INTO transaction (payment_id, user_id, amount, payment_method, payment_date, status)
    VALUES (?, ?, ?, ?, ?, ?)
");
if (!$insertTransaction) {
    die(json_encode(["status" => "error", "message" => "Query insert transaksi gagal: " . $koneksi->error]));
}
$insertTransaction->bind_param(
    "iidsss",
    $orderId,
    $userId,
    $totalAmount,
    $paymentMethod,
    $paymentDate,
    $status
);
$insertTransaction->execute();
if ($insertTransaction->affected_rows == 0) {
    die(json_encode(["status" => "error", "message" => "Transaksi tidak tersimpan"]));
}

// Tutup koneksi
$koneksi->close();

// Respon JSON ke frontend
echo json_encode(["status" => "success", "message" => "Transaksi berhasil dan stok diperbarui"]);
?>
