<?php 
session_start();
include("../config/config.php");
include("../midtrans/Midtrans.php");

// Konfigurasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-NyRCp2qWByvl54BPl6tUpnai'; 
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['paket_id'])) {
    // Ambil username dari session
    $username = $_SESSION['username'];

    // Ambil data vendor berdasarkan username
    $query_vendor = "SELECT * FROM business_account WHERE username = '$username'";
    $result_vendor = $koneksi->query($query_vendor);

    if ($result_vendor->num_rows > 0) {
        $row_vendor = $result_vendor->fetch_assoc();
        $vendor_id = $row_vendor['vendor_id'];
        $vendor_name = $row_vendor['name'];
        $vendor_email = $row_vendor['email'];

        // Ambil data dari form (paket yang dipilih)
        $paket_id = $_POST['paket_id'];

        // Ambil informasi paket iklan dari database berdasarkan paket_id
        $query_paket = "SELECT * FROM paket_ads WHERE paket_id = '$paket_id'";
        $result_paket = $koneksi->query($query_paket);

        if ($result_paket->num_rows > 0) {
            $row_paket = $result_paket->fetch_assoc();
            $harga_paket = $row_paket['harga'];
            $nama_paket = $row_paket['nama_paket'];

            // Konfigurasi transaksi Midtrans
            $order_id = rand(); // Generate unique order ID
            $transaction_details = array(
                'order_id' => $order_id,
                'gross_amount' => $harga_paket,
            );

            $item_details = array(
                array(
                    'id' => $paket_id,
                    'price' => $harga_paket,
                    'quantity' => 1,
                    'name' => $nama_paket
                )
            );

            $customer_details = array(
                'first_name' => $vendor_name,
                'email' => $vendor_email,
            );

            $transaction = array(
                'transaction_details' => $transaction_details,
                'item_details' => $item_details,
                'customer_details' => $customer_details
            );

            // Proses pembayaran Midtrans
            try {
                $snapToken = \Midtrans\Snap::getSnapToken($transaction);

                // Simpan detail pembelian paket ke database dengan status 'pending'
                $tanggal_pembelian = date('Y-m-d H:i:s');
                $insert_sql = "INSERT INTO pembelian_paket_ads (paket_id, vendor_id, status_pembayaran, status_iklan, tanggal_pembayaran, tanggal_aktif, tanggal_expired, order_id)
                VALUES ('$paket_id', '$vendor_id', 'unpaid', 'pending', '$tanggal_pembelian', NULL, NULL, '$order_id')";
                $koneksi->query($insert_sql);

                // Mengembalikan JSON dengan URL pembayaran
                echo json_encode(['success' => true, 'payment_url' => "https://app.sandbox.midtrans.com/snap/v2/vtweb/$snapToken", 'nama_paket' => $nama_paket]);
            } catch (Exception $e) {
                echo json_encode(['success' => false, 'message' => "Error: " . $e->getMessage()]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Paket tidak ditemukan."]);
        }
    } else {
        echo json_encode(['success' => false, 'message' => "Vendor tidak ditemukan."]);
    }
} else {
    echo json_encode(['success' => false, 'message' => "Permintaan tidak valid."]);
}
