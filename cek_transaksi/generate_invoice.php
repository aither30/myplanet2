<?php
session_start();
require("../pdf/fpdf.php");
require("../config/config.php");

// Ambil nomor invoice dari parameter GET
$invoiceNumber = isset($_GET['invoice_number']) ? $_GET['invoice_number'] : null;

if (!$invoiceNumber) {
    die("Nomor invoice tidak ditemukan.");
}

// Koneksi ke database
$koneksi = new mysqli("localhost", "root", "", "myplanet_db");

if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil data invoice berdasarkan nomor invoice
$invoiceQuery = $koneksi->prepare("SELECT * FROM invoice WHERE invoice_number = ?");
$invoiceQuery->bind_param("s", $invoiceNumber);
$invoiceQuery->execute();
$invoiceResult = $invoiceQuery->get_result();
$invoice = $invoiceResult->fetch_assoc();

if (!$invoice) {
    die("Invoice tidak ditemukan.");
}

// Ambil data transaksi berdasarkan transaction_id dari invoice
$transQuery = $koneksi->prepare("SELECT * FROM transaction WHERE transaction_id = ?");
$transQuery->bind_param("i", $invoice['transaction_id']);
$transQuery->execute();
$transResult = $transQuery->get_result();
$transaction = $transResult->fetch_assoc();

if (!$transaction) {
    die("Transaksi tidak ditemukan.");
}

// Ambil informasi user berdasarkan user_id dari transaksi
$userQuery = $koneksi->prepare("SELECT * FROM user_account WHERE user_id = ?");
$userQuery->bind_param("i", $transaction['user_id']);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Buat PDF menggunakan FPDF
$pdf = new FPDF();
$pdf->AddPage();

// Nama Perusahaan di sisi kiri atas
$pdf->SetFont('Arial', 'B', 16);
$pdf->Cell(100, 10, 'My PlanET', 0, 0, 'L');

// No. Invoice di sisi kanan atas
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'No. Invoice: ' . $invoice['invoice_number'], 0, 1, 'R');

// Tanggal di sisi kanan, tepat di bawah nomor invoice
$pdf->Cell(0, 10, 'Tanggal: ' . date('d-m-Y', strtotime($invoice['created_at'])), 0, 1, 'R');
$pdf->Ln(10); // Jarak setelah nama perusahaan dan nomor invoice

// Lebar kolom untuk informasi di sisi kiri dan sisi kanan
$leftColumnWidth = 100;
$rightColumnWidth = 60;
$cellHeight = 10;
$verticalGap = 5;

// Informasi Pembeli di sisi kiri tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($leftColumnWidth, $cellHeight, 'Informasi Pembeli', 0, 0, 'L'); // Judul informasi pembeli
$pdf->SetFont('Arial', '', 12);

// Posisikan header kanan (Detail Transaksi) di sebelah kanan tanpa pindah baris
$pdf->SetXY(130, $pdf->GetY());
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell($rightColumnWidth, $cellHeight, 'Detail Transaksi', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);

// Detail Informasi Pembeli
$pdf->Cell($leftColumnWidth, $cellHeight, 'Nama: ' . $user['name'], 0, 0, 'L'); // Kiri
$pdf->SetXY(130, $pdf->GetY());
$pdf->Cell($rightColumnWidth, $cellHeight, 'Nomor Transaksi: ' . $transaction['transaction_id'], 0, 1, 'L'); // Kanan

$pdf->Cell($leftColumnWidth, $cellHeight, 'Email: ' . $user['email'], 0, 0, 'L'); // Kiri
$pdf->SetXY(130, $pdf->GetY());
$pdf->Cell($rightColumnWidth, $cellHeight, 'Metode Pembayaran: ' . ucfirst($transaction['payment_method']), 0, 1, 'L'); // Kanan

$pdf->Cell($leftColumnWidth, $cellHeight, 'Telepon: ' . $user['phone'], 0, 0, 'L'); // Kiri
$pdf->SetXY(130, $pdf->GetY());
$pdf->Cell($rightColumnWidth, $cellHeight, 'Status Pembayaran: ' . ucfirst($transaction['status']), 0, 1, 'L'); // Kanan

$pdf->Cell($leftColumnWidth, $cellHeight, 'Alamat: ' . $user['address'], 0, 1, 'L'); // Alamat tetap di sisi kiri, tanpa baris kanan di sebelahnya

$pdf->Ln($verticalGap); // Tambahkan jarak setelah informasi

// Kembali ke posisi awal untuk tabel produk
$pdf->SetXY(10, 90); // Mulai tabel di bawah informasi pembeli dan transaksi

// Tabel Produk yang Dibeli
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Rincian Produk yang Dibeli', 0, 1);
$pdf->SetFont('Arial', '', 12);

// Header Tabel
$pdf->Cell(80, 10, 'Nama Produk', 1);
$pdf->Cell(30, 10, 'Kuantitas', 1);
$pdf->Cell(40, 10, 'Harga', 1);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Ln();

// Menampilkan Produk dalam Tabel dari invoice
$pdf->Cell(80, 10, $invoice['product_name'], 1);
$pdf->Cell(30, 10, $invoice['product_quantity'], 1);
$pdf->Cell(40, 10, 'Rp ' . number_format($invoice['product_price'], 0, ',', '.'), 1);
$pdf->Cell(40, 10, 'Rp ' . number_format($invoice['total_price'], 0, ',', '.'), 1);
$pdf->Ln();

// Total Pembayaran di bawah kanan tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total Pembayaran: Rp ' . number_format($transaction['amount'], 2, ',', '.'), 0, 1, 'R');

// Pastikan header untuk unduhan PDF
header('Content-Type: application/pdf');
header('Content-Disposition: attachment; filename="Invoice_' . $invoice['invoice_number'] . '.pdf"');

// Output PDF
$pdf->Output('D', 'Invoice_' . $invoice['invoice_number'] . '.pdf');

// Tutup koneksi
$koneksi->close();
