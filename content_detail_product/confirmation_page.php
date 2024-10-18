<?php
session_start();

include ("../config/config.php");


// Pastikan session username ada
if (!isset($_SESSION['username'])) {
    die("Username tidak ditemukan dalam session.");
}

// Ambil data dari session
$selectedProducts = isset($_SESSION['selected_products']) ? $_SESSION['selected_products'] : null;
$totalAmount = isset($_SESSION['total_amount']) ? $_SESSION['total_amount'] : null;
$orderId = isset($_SESSION['order_id']) ? $_SESSION['order_id'] : null;
$username = $_SESSION['username'];

// Cek apakah semua data session tersedia
if (!$selectedProducts || !$totalAmount || !$orderId) {
    die("Data session tidak lengkap.");
}

// Ambil user ID dan informasi pengguna dari user_account terlebih dahulu
$userQuery = $koneksi->prepare("SELECT user_id, email, name, phone, address FROM user_account WHERE username = ?");
$userQuery->bind_param("s", $username);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Jika tidak ditemukan di user_account, cek di business_account
if (!$user) {
    $userQuery = $koneksi->prepare("SELECT vendor_id AS user_id, email, company_name AS name, NULL AS phone, address FROM business_account WHERE username = ?");
    $userQuery->bind_param("s", $username);
    $userQuery->execute();
    $userResult = $userQuery->get_result();
    $user = $userResult->fetch_assoc();
    
    if (!$user) {
        die("User tidak ditemukan di database.");
    }
}

// Ambil data pengguna
$userId = $user['user_id'];
$userEmail = $user['email'];
$userName = $user['name'];
$userPhone = isset($user['phone']) ? $user['phone'] : 'Telepon tidak tersedia'; // Telepon bisa null untuk business_account
$userAddress = $user['address'];

// Loop melalui produk untuk mengurangi stok dan memperbarui keranjang
foreach ($selectedProducts as $product) {
    $productName = $product['name'];
    $quantityPurchased = $product['quantity'];

    // Kurangi stok produk
    $updateStock = $koneksi->prepare("UPDATE product SET stocks = GREATEST(stocks - ?, 0) WHERE name = ?");
    $updateStock->bind_param("is", $quantityPurchased, $productName);
    $updateStock->execute();

    // Ambil product_id dari tabel product
    $checkProductId = $koneksi->prepare("SELECT product_id FROM product WHERE name = ?");
    $checkProductId->bind_param("s", $productName);
    $checkProductId->execute();
    $productId = $checkProductId->get_result()->fetch_assoc()['product_id'];

    // Hapus produk dari keranjang
    $deleteCart = $koneksi->prepare("DELETE FROM cart WHERE username = ? AND product_id = ?");
    $deleteCart->bind_param("si", $username, $productId);
    $deleteCart->execute();
}

// Simpan data transaksi ke tabel 'transaction'
$paymentMethod = 'credit_card'; // Ubah sesuai metode pembayaran dari Midtrans
$paymentDate = date('Y-m-d H:i:s');
$status = 'completed';

$insertTransaction = $koneksi->prepare("INSERT INTO transaction (payment_id, user_id, amount, payment_method, payment_date, status) VALUES (?, ?, ?, ?, ?, ?)");
$insertTransaction->bind_param("iidsss", $orderId, $userId, $totalAmount, $paymentMethod, $paymentDate, $status);
$insertTransaction->execute();

// Ambil transaction_id yang baru saja diinsert
$transactionId = $koneksi->insert_id;

$logMessages = ''; // Variabel untuk menyimpan pesan log

foreach ($selectedProducts as $product) {
    $productName = $product['name'];
    $quantityPurchased = $product['quantity'];
    $productPrice = $product['price'];
    $totalPrice = $quantityPurchased * $productPrice;

    // Generate invoice number dan waktu pembuatan
    $invoiceNumber = 'INV-' . strtoupper(uniqid());
    $createdAt = date('Y-m-d H:i:s');

    // Insert data ke tabel invoice
    $insertInvoice = $koneksi->prepare("
        INSERT INTO invoice (
            transaction_id, user_name, user_email, user_phone, user_address, 
            product_name, product_quantity, product_price, total_price, 
            invoice_number, created_at
        ) 
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $insertInvoice->bind_param(
        "isssssiidss", 
        $transactionId, 
        $userName, 
        $userEmail, 
        $userPhone, 
        $userAddress, 
        $productName, 
        $quantityPurchased, 
        $productPrice, 
        $totalPrice, 
        $invoiceNumber, 
        $createdAt
    );

    // Eksekusi perintah SQL
    if ($insertInvoice->execute()) {
        $logMessages .= "Invoice untuk produk $productName berhasil dibuat.\n"; 
    } else {
        $logMessages .= "Error: " . $insertInvoice->error . "\n";
    }
}

// Tutup koneksi
$koneksi->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <title>Konfirmasi Pembayaran Berhasil</title>
    <link rel="stylesheet" href="../css/style.nav.css">
    <link rel="stylesheet" href="style_confirmation_page.css">
</head>
<body>
<?php include ("../container_content/nav.php")?>
    <div class="container">
        <h1>Pembayaran Berhasil!</h1>
        <a href="download_invoice.php?order_id=<?= $orderId ?>" class="btn-download">Unduh Invoice</a>
        <!-- Informasi Pengguna -->
        <h2>Informasi Pembeli</h2>
        <table class="info-table">
            <tr><td><strong>Nama:</strong></td><td><?= $userName; ?></td></tr>
            <tr><td><strong>Email:</strong></td><td><?= $userEmail; ?></td></tr>
            <tr><td><strong>Telepon:</strong></td><td><?= $userPhone; ?></td></tr>
            <tr><td><strong>Alamat:</strong></td><td><?= $userAddress; ?></td></tr>
        </table>

        <!-- Informasi Produk -->
        <h2>Detail Produk yang Dibeli</h2>
        <table>
            <thead>
                <tr>
                    <th>Nama Produk</th>
                    <th>Kuantitas</th>
                    <th>Harga</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody id="productBody">
                <?php
                foreach ($selectedProducts as $product) {
                    echo "<tr>";
                    echo "<td>" . $product['name'] . "</td>";
                    echo "<td>" . $product['quantity'] . "</td>";
                    echo "<td>Rp " . number_format($product['price'], 2, ',', '.') . "</td>";
                    echo "<td>Rp " . number_format($product['quantity'] * $product['price'], 2, ',', '.') . "</td>";
                    echo "</tr>";
                }
                ?>
            </tbody>
            <tfoot>
                <tr>
                    <td colspan="3" class="total">Total Pembayaran:</td>
                    <td id="totalAmount">Rp <?= number_format($totalAmount, 2, ',', '.'); ?></td>
                </tr>
            </tfoot>
        </table>

    <!-- Tambahkan script untuk menampilkan pesan di console -->
    <script>
        console.log('Stok telah diperbarui dan produk telah dihapus dari keranjang.');
    </script>
<div class="button-container">
    <button class="btn" id="createPdfBtn">Kembali ke Beranda</button>
</div>

<script>
document.getElementById("createPdfBtn").addEventListener("click", function() {
    // Definisikan objek XMLHttpRequest
    var xhr = new XMLHttpRequest();
    
    // Menggunakan AJAX untuk memanggil skrip PHP tanpa merefresh halaman
    xhr.open("POST", "create_invoice_pdf.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");

    xhr.onreadystatechange = function() {
        if (xhr.readyState === 4) {
            console.log("Status: " + xhr.status);
            if (xhr.status === 200) {
                window.location.href = "../index.php"; // Arahkan ke beranda jika sukses
            } else {
                console.log("Error: Unable to create PDF");
            }
        }
    };

    // Kirim permintaan dengan order ID atau informasi lain yang dibutuhkan
    xhr.send("order_id=<?php echo $orderId; ?>&user_id=<?php echo $userId; ?>");
});

</script>

    </div>

    <footer>
    <div class="content-footer">
        <div class="container-footer">
            <div class="desksingkatmyplanet">
                <div class="logo">
                    <img src="./assets/attribute myplanet/Logo My PlanEt.png" alt="My PlanET" />
                    <h3>My PlanET</h3>
                </div>
                <p>My PlanEt adalah platform perencana acara yang menggabungkan teknologi dan kreativitas untuk menyelenggarakan acara yang tak terlupakan.</p>
            </div>
            <div class="sitemap">
                <h3>Situs Map</h3>
                <ul>
                    <li>Cek Transaksi</li>
                    <li>Banding Harga</li>
                    <li>Dashboard</li>
                    <li>Tentang Kami</li>
                </ul>
            </div>
            <div class="sosmed">
                <h3>Sosial Media</h3>
                <ul>
                    <li>Tiktok</li>
                    <li>Instagram</li>
                    <li>Facebook</li>
                </ul>
            </div>
        </div>
    </div>
    <div class="copyright">
        <p>©℗ 2024 My Planet. All rights reserved.</p>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="../js/dropdown.js"></script>
<script src="../js/searchPopup.js"></script>
<script src="../js/slider.js"></script>
<script src="../js/chat.js"></script>

<script class="script-theme">
    document.getElementById("theme-toggle").addEventListener("click", function () {
    document.body.classList.toggle("dark-theme");

    const icon = this.querySelector("i");
    if (document.body.classList.contains("dark-theme")) {
        icon.classList.remove("fa-circle-half-stroke");
        icon.classList.add("fa-sun"); // Ganti dengan ikon matahari saat dark theme
    } else {
        icon.classList.remove("fa-sun");
        icon.classList.add("fa-circle-half-stroke"); // Ganti kembali ke ikon setengah lingkaran
    }
});
</script>

<script>
// Tampilkan pesan di console.log
console.log(`<?php echo nl2br($logMessages); ?>`);
</script>
</body>
</html>
