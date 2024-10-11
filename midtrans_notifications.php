<?php
include("./config/config.php");
include("./midtrans/Midtrans.php");


// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-NyRCp2qWByvl54BPl6tUpnai'; // Ganti dengan Server Key dari Midtrans
\Midtrans\Config::$isProduction = false; // Set ke true jika ingin menggunakan environment production
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

try {
    // Ambil notifikasi dari Midtrans
    $notif = new \Midtrans\Notification();

    $transaction_status = $notif->transaction_status;
    $order_id = $notif->order_id; // Ini harus sesuai dengan order_id yang Anda kirimkan saat transaksi

    // Cek status transaksi
    if ($transaction_status == 'settlement') {
        // Pembayaran sukses, update status pembayaran di database
        $tanggal_aktif = date('Y-m-d H:i:s');
        $durasi_paket = 30; // Asumsikan durasi 30 hari untuk paket (bisa diambil dari database juga)
        $tanggal_expired = date('Y-m-d H:i:s', strtotime($tanggal_aktif . " + $durasi_paket days"));

        // Update status pembayaran menjadi paid dan status iklan menjadi active
        $sql_update = "UPDATE pembelian_paket_ads SET status_pembayaran = 'paid', status_iklan = 'active', tanggal_aktif = '$tanggal_aktif', tanggal_expired = '$tanggal_expired' WHERE paket_ads_id = '$order_id'";
        $koneksi->query($sql_update);

    } else if ($transaction_status == 'pending') {
        // Pembayaran masih menunggu, update status pembayaran ke unpaid
        $sql_update = "UPDATE pembelian_paket_ads SET status_pembayaran = 'unpaid', status_iklan = 'pending' WHERE paket_ads_id = '$order_id'";
        $koneksi->query($sql_update);

    } else if ($transaction_status == 'deny' || $transaction_status == 'expire' || $transaction_status == 'cancel') {
        // Pembayaran gagal atau dibatalkan, update status ke cancelled
        $sql_update = "UPDATE pembelian_paket_ads SET status_pembayaran = 'unpaid', status_iklan = 'cancelled' WHERE paket_ads_id = '$order_id'";
        $koneksi->query($sql_update);
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
