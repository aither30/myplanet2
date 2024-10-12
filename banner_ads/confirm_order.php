<?php
session_start();
include("../config/config.php");

// Ambil data dari URL setelah pembayaran berhasil
$order_id = isset($_GET['order_id']) ? $_GET['order_id'] : null;
$transaction_status = isset($_GET['transaction_status']) ? $_GET['transaction_status'] : null;
$gross_amount = isset($_GET['gross_amount']) ? $_GET['gross_amount'] : null;

if ($order_id) {
    // Ambil informasi pesanan dari database berdasarkan order_id dari sistem
    $query = "SELECT p.nama_paket, p.durasi, ppa.status_pembayaran, ppa.tanggal_pembayaran, ppa.tanggal_expired 
              FROM pembelian_paket_ads ppa 
              JOIN paket_ads p ON ppa.paket_id = p.paket_id 
              WHERE ppa.order_id = '$order_id'";
    $result = $koneksi->query($query);

    if ($result && $result->num_rows > 0) {
        $order_data = $result->fetch_assoc();
        $durasi_paket = $order_data['durasi']; // Durasi dari paket (dalam hari)

        // Hitung tanggal kedaluwarsa berdasarkan tanggal pembelian dan durasi paket
        $tanggal_pembelian = date('Y-m-d H:i:s');
        $tanggal_kedaluwarsa = date('Y-m-d H:i:s', strtotime("$tanggal_pembelian + $durasi_paket days"));

        // Perbarui status pembayaran dan tanggal kadaluwarsa berdasarkan transaction_status
        if ($transaction_status == 'settlement') {
            $update_query = "UPDATE pembelian_paket_ads 
                             SET status_pembayaran = 'paid', 
                                 status_iklan = 'active', 
                                 tanggal_pembayaran = NOW(), 
                                 tanggal_expired = '$tanggal_kedaluwarsa' 
                             WHERE order_id = '$order_id'";
        } elseif ($transaction_status == 'pending') {
            $update_query = "UPDATE pembelian_paket_ads SET status_pembayaran = 'pending' WHERE order_id = '$order_id'";
        } elseif ($transaction_status == 'expire') {
            $update_query = "UPDATE pembelian_paket_ads SET status_pembayaran = 'expired', status_iklan = 'expired' WHERE order_id = '$order_id'";
        } elseif ($transaction_status == 'cancel') {
            $update_query = "UPDATE pembelian_paket_ads SET status_pembayaran = 'cancelled', status_iklan = 'cancelled' WHERE order_id = '$order_id'";
        }

        if (isset($update_query)) {
            $koneksi->query($update_query);
        }
    } else {
        echo "Pesanan tidak ditemukan.";
        exit;
    }
} else {
    echo "Order ID tidak tersedia.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pesanan</title>
    <link rel="stylesheet" href="../css/style.nav.css">
    <link rel="stylesheet" href="style.confim.order.css"> <!-- Menghubungkan dengan file CSS eksternal -->
</head>
<body>
<?php include ("../container_content/nav.php")?>
<div class="container">
    <h1>Konfirmasi Pesanan</h1>
    <p><strong>Nama Paket:</strong> <?php echo isset($order_data['nama_paket']) ? $order_data['nama_paket'] : 'Data tidak tersedia'; ?></p>
    <p><strong>Status Pembayaran:</strong> <?php echo ucfirst($transaction_status); ?></p>
    <p><strong>Total Pembayaran:</strong> Rp <?php echo number_format($gross_amount, 2, ',', '.'); ?></p>
    <p><strong>Tanggal Pembayaran:</strong> <?php echo isset($order_data['tanggal_pembayaran']) ? $order_data['tanggal_pembayaran'] : 'Data tidak tersedia'; ?></p>
    <p><strong>Tanggal Kedaluwarsa:</strong> <?php echo isset($tanggal_kedaluwarsa) ? $tanggal_kedaluwarsa : 'Data tidak tersedia'; ?></p>

    <!-- Tombol Kembali ke Halaman Index -->
    <a href="index.php" class="btn">Kembali</a>
</div>

</body>
</html>
