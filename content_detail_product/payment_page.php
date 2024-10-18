<?php
session_start();
include("../midtrans/Midtrans.php");

// Set konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-NyRCp2qWByvl54BPl6tUpnai'; // Ganti dengan Server Key Anda
\Midtrans\Config::$isProduction = false; // Ubah ke true jika di production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Periksa apakah data produk yang dipilih dan total pembayaran dikirim melalui POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['selectedProducts']) && isset($_POST['totalAmount'])) {
    
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

    // Ambil data produk yang dipilih dan total pembayaran dari POST
    $selectedProducts = json_decode($_POST['selectedProducts'], true);
    $totalAmount = $_POST['totalAmount'];

    // Pastikan total amount tidak kurang dari 100 (atau minimum yang diperbolehkan)
    if ($totalAmount < 100) {
        die("Total pembayaran tidak valid. Pastikan total lebih dari Rp 100.");
    }

    // Ambil informasi user dari session
    $userEmail = isset($_SESSION['user_email']) ? $_SESSION['user_email'] : '';
    $userName = isset($_SESSION['user_name']) ? $_SESSION['user_name'] : '';
    $userPhone = isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : '';
    $userAddress = isset($_SESSION['user_address']) ? $_SESSION['user_address'] : '';

    // Validasi email sebelum digunakan
    if (!filter_var($userEmail, FILTER_VALIDATE_EMAIL)) {
        die("Format email tidak valid. Silakan periksa kembali.");
    }

    // Simpan produk yang dipilih ke session untuk nanti digunakan di halaman konfirmasi
    $_SESSION['selected_products'] = $selectedProducts;
    $_SESSION['total_amount'] = $totalAmount;
    $_SESSION['order_id'] = rand();

    // Buat item detail untuk setiap produk yang dipilih
    $itemDetails = [];
    foreach ($selectedProducts as $product) {
        $itemDetails[] = [
            'id' => isset($product['id']) ? $product['id'] : 'ID tidak tersedia',
            'price' => isset($product['price']) ? intval($product['price']) : 0, // Pastikan harga tidak memiliki desimal
            'quantity' => isset($product['quantity']) ? $product['quantity'] : 1,
            'name' => isset($product['name']) ? $product['name'] : 'Produk tidak tersedia'
        ];
    }

    // Data transaksi untuk Midtrans
    $transactionDetails = [
        'order_id' => $_SESSION['order_id'],
        'gross_amount' => intval($totalAmount) // Pastikan total amount tanpa desimal
    ];

    // Data customer
    $customerDetails = [
        'first_name' => $userName,
        'email' => $userEmail,
        'phone' => $userPhone,
        'address' => $userAddress
    ];

    // Buat parameter transaksi Midtrans
    $transaction = [
        'transaction_details' => $transactionDetails,
        'item_details' => $itemDetails,
        'customer_details' => $customerDetails
    ];

    try {
        // Buat token transaksi
        $snapToken = \Midtrans\Snap::getSnapToken($transaction);
    } catch (Exception $e) {
        // Tangkap error jika ada
        die("Error: " . $e->getMessage());
    }

    // HTML dan PHP untuk halaman pembayaran
    ?>
    <!DOCTYPE html>
    <html lang='en'>
    <head>
        <meta charset='UTF-8'>
        <meta name='viewport' content='width=device-width, initial-scale=1.0'>
        <title>Pembayaran dengan Midtrans</title>
        <script src='https://app.sandbox.midtrans.com/snap/snap.js' data-client-key='SB-Mid-client-hwHh9OsT2F3eqgmy'></script>
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
        <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
        <link rel='stylesheet' href='stylepayment.css'>
        <link rel="stylesheet" href="../css/style.footer.css">
    </head>
    <body>
<?php include ("../container_content/nav.php")?>
        <div class='container'>
            <div class='payment-section'>
                <div class='product-details'>
                    <h2>Detail Produk:</h2>
                    <table class='product-table'>
                        <thead>
                            <tr>
                                <th>Nama Produk</th>
                                <th>Kuantitas</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            foreach ($selectedProducts as $product) {
                                $productName = $product['name'];
                                $quantity = $product['quantity'];
                                $price = number_format($product['price'], 0, ',', '.');
                                $totalPrice = number_format($quantity * $product['price'], 0, ',', '.');
                                echo "<tr><td>$productName</td><td>$quantity</td><td>Rp $price</td><td>Rp $totalPrice</td></tr>";
                            }
                            ?>
                        </tbody>
                    </table>
                    <p class='payment-total'>Total pembayaran: Rp <?= number_format($totalAmount, 0, ',', '.'); ?></p>
                </div>
                
                <div class='customer-details'>
                    <h2>Informasi Pembeli:</h2>
                    <table class='info-table'>
                        <tr><td><strong>Nama:</strong></td><td><?= htmlspecialchars($userName); ?></td></tr>
                        <tr><td><strong>Email:</strong></td><td><?= htmlspecialchars($userEmail); ?></td></tr>
                        <tr><td><strong>Telepon:</strong></td><td><?= htmlspecialchars($userPhone); ?></td></tr>
                        <tr><td><strong>Alamat:</strong></td><td><?= htmlspecialchars($userAddress); ?></td></tr>
                    </table>
                </div>
            </div>
            <button id='pay-button'>Bayar Sekarang</button>

            <script type='text/javascript'>
                var snapToken = '<?= $snapToken; ?>';
                var payButton = document.getElementById('pay-button');
                payButton.addEventListener('click', function() {
                    snap.pay(snapToken, {
                        onSuccess: function(result) {
                            window.location.href = 'confirmation_page.php';
                        },
                        onError: function(result) {
                            alert('Pembayaran Gagal! Silakan coba lagi.');
                        }
                    });
                });
            </script>
        </div>
<?php include ("../container_content/footer.php")?>

    </body>
    </html>
    <?php

    $koneksi->close();
}
?>
