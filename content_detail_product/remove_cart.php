<?php
session_start();

include ("../config/config.php");


// Periksa apakah permintaan POST berisi cart_id
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['cart_id'])) {
    $cart_id = $_POST['cart_id'];

    // Query untuk menghapus item dari tabel keranjang berdasarkan cart_id
    $sql = "DELETE FROM cart WHERE cart_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param("i", $cart_id);

    // Eksekusi query dan periksa apakah berhasil
    if ($stmt->execute()) {
        // Jika penghapusan berhasil, kirim respons JSON sukses
        echo json_encode(['success' => true]);
    } else {
        // Jika penghapusan gagal, kirim respons JSON gagal
        echo json_encode(['success' => false, 'message' => 'Gagal menghapus produk dari keranjang']);
    }

    $stmt->close();
} else {
    // Jika tidak ada cart_id yang dikirim, kirim respons JSON gagal
    echo json_encode(['success' => false, 'message' => 'Tidak ada ID keranjang yang diberikan']);
}

// Tutup koneksi ke database
$koneksi->close();
