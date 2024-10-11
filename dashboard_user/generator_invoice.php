<?php
session_start();
require("../pdf/fpdf.php");
include("../config/config.php");

// Ambil nomor invoice dari request GET
$invoice_number = isset($_GET['invoice_number']) ? $_GET['invoice_number'] : null;

if (!$invoice_number) {
    die("Nomor invoice tidak tersedia.");
}

// Query untuk mendapatkan detail invoice berdasarkan nomor invoice
$invoiceQuery = "SELECT * FROM invoice WHERE invoice_number = ?";
$stmt = $koneksi->prepare($invoiceQuery);
$stmt->bind_param("s", $invoice_number);
$stmt->execute();
$invoiceResult = $stmt->get_result();
$invoice = $invoiceResult->fetch_assoc();

if (!$invoice) {
    die("Invoice tidak ditemukan.");
}

// Buat PDF menggunakan FPDF
$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Nama Perusahaan di sisi kiri atas
$pdf->Cell(100, 10, 'My PlanET', 0, 0, 'L');

// No. Invoice di sisi kanan atas
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'No. Invoice: ' . $invoice['invoice_number'], 0, 1, 'R');

// Tanggal di sisi kanan, tepat di bawah nomor invoice
$pdf->Cell(0, 10, 'Tanggal: ' . date('d-m-Y', strtotime($invoice['created_at'])), 0, 1, 'R');
$pdf->Ln(10); // Jarak setelah nama perusahaan dan nomor invoice

// Informasi lainnya (data produk, total pembayaran, dll.)
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Rincian Produk', 0, 1);
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(80, 10, 'Nama Produk', 1);
$pdf->Cell(30, 10, 'Kuantitas', 1);
$pdf->Cell(40, 10, 'Harga', 1);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Ln();
$pdf->Cell(80, 10, $invoice['product_name'], 1);
$pdf->Cell(30, 10, $invoice['product_quantity'], 1);
$pdf->Cell(40, 10, 'Rp ' . number_format($invoice['product_price'], 2, ',', '.'), 1);
$pdf->Cell(40, 10, 'Rp ' . number_format($invoice['total_price'], 2, ',', '.'), 1);
$pdf->Ln();

// Simpan PDF ke file
$pdfFileName = 'Invoice_' . $invoice['invoice_number'] . '.pdf';
$filePath = __DIR__ . '/../pdf/' . $pdfFileName;
$pdf->Output('F', $filePath);

// Update status di database bahwa PDF telah dibuat
$updateStatus = "UPDATE invoice SET pdf_status = 'created', pdf_file = ? WHERE invoice_number = ?";
$stmtUpdate = $koneksi->prepare($updateStatus);
$stmtUpdate->bind_param("ss", $pdfFileName, $invoice_number);
$stmtUpdate->execute();

// Redirect untuk mengunduh file PDF
header('Location: ../pdf/' . $pdfFileName);
exit();

?>
