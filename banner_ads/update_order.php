<?php
include("../config/config.php");

$data = json_decode(file_get_contents("php://input"), true);

if (isset($data['order_id']) && isset($data['midtrans_order_id'])) {
    $order_id = $data['order_id'];
    $midtrans_order_id = $data['midtrans_order_id'];

    // Update midtrans_order_id di database
    $update_query = "UPDATE pembelian_paket_ads SET midtrans_order_id = '$midtrans_order_id' WHERE order_id = '$order_id'";
    if ($koneksi->query($update_query) === TRUE) {
        echo json_encode(["message" => "Midtrans Order ID berhasil diperbarui"]);
    } else {
        echo json_encode(["message" => "Gagal memperbarui Midtrans Order ID", "error" => $koneksi->error]);
    }
} else {
    echo json_encode(["message" => "Data tidak lengkap"]);
}
?>
