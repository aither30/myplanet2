<?php
include("../config/config.php");

// Query untuk mengambil invoice yang status PDF-nya sedang diproses
$query = "SELECT * FROM invoice WHERE pdf_status = 'processing'";
$result = $koneksi->query($query);

while ($invoice = $result->fetch_assoc()) {
    // Proses pembuatan PDF
    include("generator_invoice.php");

    // Setelah PDF berhasil dibuat, update status menjadi 'created'
    $updateStatus = "UPDATE invoice SET pdf_status = 'created' WHERE invoice_number = ?";
    $stmt = $koneksi->prepare($updateStatus);
    $stmt->bind_param("s", $invoice['invoice_number']);
    $stmt->execute();
}

echo "Proses PDF selesai.";
?>
