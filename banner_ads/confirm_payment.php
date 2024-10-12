<?php
session_start();
include("../config/config.php");
include("../midtrans/Midtrans.php");

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-NyRCp2qWByvl54BPl6tUpnai';
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Periksa apakah paket telah dipilih sebelumnya
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paket_id'])) {
    $paket_id = $_POST['paket_id'];
    $vendor_id = $_POST['vendor_id'];

    // Ambil informasi paket dari database
    $query_paket = "SELECT * FROM paket_ads WHERE paket_id = '$paket_id'";
    $result_paket = $koneksi->query($query_paket);
    $row_paket = $result_paket->fetch_assoc();

    // Ambil informasi vendor dari database
    $query_vendor = "SELECT * FROM business_account WHERE vendor_id = '$vendor_id'";
    $result_vendor = $koneksi->query($query_vendor);
    $row_vendor = $result_vendor->fetch_assoc();

    // Konfigurasi transaksi Midtrans
    $order_id = rand(); // Gunakan order_id dari sistem
    $transaction_details = array(
        'order_id' => $order_id,
        'gross_amount' => $row_paket['harga'],
    );

    $item_details = array(
        array(
            'id' => $paket_id,
            'price' => $row_paket['harga'],
            'quantity' => 1,
            'name' => $row_paket['nama_paket']
        )
    );

    $customer_details = array(
        'first_name' => $row_vendor['name'],
        'email' => $row_vendor['email'],
    );

    $transaction = array(
        'transaction_details' => $transaction_details,
        'item_details' => $item_details,
        'customer_details' => $customer_details
    );

    try {
        $snapToken = \Midtrans\Snap::getSnapToken($transaction);

        // Simpan Snap Token dan order ID ke session untuk digunakan di modal
        $_SESSION['snapToken'] = $snapToken;
        $_SESSION['order_id'] = $order_id;

        // Simpan transaksi ke dalam database dengan status pending dan order_id dari sistem (belum Midtrans)
        $tanggal_pembelian = date('Y-m-d H:i:s');
        $insert_sql = "INSERT INTO pembelian_paket_ads (paket_id, vendor_id, status_pembayaran, status_iklan, tanggal_pembayaran, order_id, midtrans_order_id) 
                        VALUES ('$paket_id', '$vendor_id', 'unpaid', 'pending', '$tanggal_pembelian', '$order_id', NULL)";
        $koneksi->query($insert_sql);
    } catch (Exception $e) {
        echo "Error: " . $e->getMessage();
    }
} else {
    echo "Paket tidak ditemukan atau belum dipilih.";
    exit;
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pemesanan</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style.nav.css">
    <link rel="stylesheet" href="confirm_payment.css"> <!-- Menghubungkan dengan file CSS eksternal -->
    <script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-NyRCp2qWByvl54BPl6tUpnai"></script>
</head>
<body>
<?php include ("../container_content/nav.php")?>
<div class="container">
    <h1>Konfirmasi Pemesanan</h1>

    <!-- Menampilkan informasi paket yang dipilih -->
    <div class="card">
        <h2>Paket: <?php echo $row_paket['nama_paket']; ?></h2>
        <p>Harga: <span class="price">Rp <?php echo number_format($row_paket['harga'], 2, ',', '.'); ?></span></p>
        <p>Durasi: <?php echo $row_paket['durasi']; ?> hari</p>
        <p>Jumlah Iklan Landscape: <?php echo $row_paket['jumlah_iklan_landscape']; ?></p>
        <p>Jumlah Iklan Slider 2: <?php echo $row_paket['jumlah_iklan_slider2']; ?></p>
        <p>Jumlah Iklan Slider 3: <?php echo $row_paket['jumlah_iklan_slider3']; ?></p>
    </div>

    <button id="pay-button" class="btn">Lanjutkan Pembayaran</button>
</div>

<script>
    document.getElementById('pay-button').onclick = function() {
        var snapToken = "<?php echo isset($_SESSION['snapToken']) ? $_SESSION['snapToken'] : ''; ?>";
        var orderId = "<?php echo $_SESSION['order_id']; ?>";  // Ambil order_id dari sistem

        if (snapToken) {
            snap.pay(snapToken, {
                onSuccess: function(result) {
                    console.log('Pembayaran berhasil:', result);
                    // Redirect ke halaman konfirmasi dengan order_id dari sistem
                    window.location.href = `confirm_order.php?order_id=${orderId}&transaction_status=${result.transaction_status}&gross_amount=${result.gross_amount}`;
                },
                onPending: function(result) {
                    console.log('Pembayaran pending:', result);
                },
                onError: function(result) {
                    console.log('Pembayaran gagal:', result);
                }
            });
        } else {
            alert("Snap Token tidak ditemukan!");
        }
    }
</script>

</body>
</html>
