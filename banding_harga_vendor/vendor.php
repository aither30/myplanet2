<?php
include ("../config/config.php");

// Mengambil `vendor_id` dari request GET
$vendor_id = $_GET['vendor_id'] ?? null;

if ($vendor_id) {
    // Query untuk mengambil data vendor berdasarkan vendor_id
    $sql = "SELECT company_name, logo, phone_vendor FROM business_account WHERE vendor_id = ?";
    $stmt = $koneksi->prepare($sql);
    $stmt->bind_param('i', $vendor_id);
    $stmt->execute();
    $result = $stmt->get_result();

    $vendor = $result->fetch_assoc();

    // Mengembalikan data dalam format JSON
    header('Content-Type: application/json');
    echo json_encode($vendor);
} else {
    // Mengembalikan error jika tidak ada vendor_id
    echo json_encode(['error' => 'Vendor ID tidak ditemukan']);
}
?>
