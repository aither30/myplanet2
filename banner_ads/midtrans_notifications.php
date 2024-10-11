<?php 
session_start();
include("../config/config.php");
include("../midtrans/Midtrans.php");

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-NyRCp2qWByvl54BPl6tUpnai'; 
\Midtrans\Config::$isProduction = false;

try {
    // Ambil notifikasi dari Midtrans
    $notif = new \Midtrans\Notification();

    // Log notifikasi untuk debugging
    error_log(print_r($notif, true));

    // Periksa apakah data yang diperlukan ada
    if (!isset($notif->transaction_status) || !isset($notif->order_id)) {
        error_log("Notifikasi tidak lengkap: " . print_r($notif, true));
        exit("Notifikasi tidak lengkap.");
    }

    // Ambil informasi transaksi
    $transaction_status = $notif->transaction_status;
    $order_id = $notif->order_id;

    // Cari transaksi di database
    $query = "SELECT * FROM pembelian_paket_ads WHERE order_id = '$order_id'";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        // Mengupdate status berdasarkan status transaksi
        switch ($transaction_status) {
            case 'capture':
            case 'settlement':
                $sql = "UPDATE pembelian_paket_ads 
                        SET status_pembayaran = 'paid', status_iklan = 'active', tanggal_aktif = NOW(), 
                        tanggal_expired = DATE_ADD(NOW(), INTERVAL (SELECT durasi FROM paket_ads WHERE paket_id = (SELECT paket_id FROM pembelian_paket_ads WHERE order_id = '$order_id')) DAY) 
                        WHERE order_id = '$order_id'";
                break;

            case 'pending':
                $sql = "UPDATE pembelian_paket_ads SET status_pembayaran = 'pending' WHERE order_id = '$order_id'";
                break;

            case 'expire':
                $sql = "UPDATE pembelian_paket_ads SET status_pembayaran = 'expired', status_iklan = 'expired' WHERE order_id = '$order_id'";
                break;

            case 'cancel':
                $sql = "UPDATE pembelian_paket_ads SET status_pembayaran = 'cancelled', status_iklan = 'cancelled' WHERE order_id = '$order_id'";
                break;
        }

        // Eksekusi query jika ada yang di-update
        if (isset($sql)) {
            $koneksi->query($sql);
        }

        // Kirim respons 200 OK ke Midtrans
        http_response_code(200);
    } else {
        // Jika tidak ditemukan, kirim respons 404
        http_response_code(404);
    }
} catch (Exception $e) {
    // Log kesalahan
    error_log("Error: " . $e->getMessage());
    http_response_code(500);
}
?>
