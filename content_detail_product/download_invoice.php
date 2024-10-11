<?php
session_start();
include("../pdf/fpdf.php");
include("../config/config.php");

// Ambil order_id dari request GET atau session
$orderId = isset($_GET['order_id']) ? $_GET['order_id'] : (isset($_SESSION['order_id']) ? $_SESSION['order_id'] : null);

if (!$orderId) {
    die("Order ID tidak ditemukan.");
}

$invoiceDirectory = '../invoices/';

// Cek apakah direktori ada, jika tidak buat direktori
if (!is_dir($invoiceDirectory)) {
    mkdir($invoiceDirectory, 0777, true); // Buat direktori dengan izin penuh
}

// Ambil data transaksi dari tabel 'transaction'
$transQuery = $koneksi->prepare("SELECT * FROM transaction WHERE payment_id = ?");
$transQuery->bind_param("i", $orderId);
$transQuery->execute();
$transResult = $transQuery->get_result();
$transaction = $transResult->fetch_assoc();

if (!$transaction) {
    die("Transaksi tidak ditemukan.");
}

// Cek apakah sudah ada invoice untuk transaksi ini
$invoiceQuery = $koneksi->prepare("SELECT * FROM invoice WHERE transaction_id = ?");
$invoiceQuery->bind_param("i", $transaction['transaction_id']);
$invoiceQuery->execute();
$invoiceResult = $invoiceQuery->get_result();
$invoice = $invoiceResult->fetch_assoc();

if (!$invoice) {
    // Jika belum ada, buat nomor invoice baru dan simpan ke tabel 'invoice'
    $invoiceNumber = 'INV-' . strtoupper(uniqid());
    $createdAt = date('Y-m-d H:i:s');
    
    $insertInvoice = $koneksi->prepare("INSERT INTO invoice (transaction_id, invoice_number, created_at) VALUES (?, ?, ?)");
    $insertInvoice->bind_param("iss", $transaction['transaction_id'], $invoiceNumber, $createdAt);
    $insertInvoice->execute();
    
    // Ambil data invoice yang baru dibuat
    $invoiceId = $koneksi->insert_id;
    $invoice = [
        'invoice_number' => $invoiceNumber,
        'created_at' => $createdAt
    ];
}

// Ambil informasi user berdasarkan user_id dari database
$userQuery = $koneksi->prepare("SELECT * FROM user_account WHERE user_id = ?");
$userQuery->bind_param("i", $transaction['user_id']);
$userQuery->execute();
$userResult = $userQuery->get_result();
$user = $userResult->fetch_assoc();

// Jika tidak ditemukan di database, ambil dari session
if (!$user) {
    $user = [
        'name' => isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Nama tidak tersedia',
        'email' => isset($_SESSION['user_email']) ? $_SESSION['user_email'] : 'Email tidak tersedia',
        'phone' => isset($_SESSION['user_phone']) ? $_SESSION['user_phone'] : 'Telepon tidak tersedia',
        'address' => isset($_SESSION['user_address']) ? $_SESSION['user_address'] : 'Alamat tidak tersedia'
    ];
}

// Ambil data produk dari session
$selectedProducts = isset($_SESSION['selected_products']) ? $_SESSION['selected_products'] : [];

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

// Informasi Pembeli di sisi kiri tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(100, 10, 'Informasi Pembeli', 0, 1, 'L');
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(100, 10, 'Nama: ' . $user['name'], 0, 1, 'L');
$pdf->Cell(100, 10, 'Email: ' . $user['email'], 0, 1, 'L');
$pdf->Cell(100, 10, 'Telepon: ' . $user['phone'], 0, 1, 'L');
$pdf->Cell(100, 10, 'Alamat: ' . $user['address'], 0, 1, 'L');

// Tabel Produk yang Dibeli
$pdf->Ln(10); // Tambah jarak vertikal
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Rincian Produk yang Dibeli', 0, 1);
$pdf->SetFont('Arial', '', 12);

// Header Tabel
$pdf->Cell(80, 10, 'Nama Produk', 1);
$pdf->Cell(30, 10, 'Kuantitas', 1);
$pdf->Cell(40, 10, 'Harga', 1);
$pdf->Cell(40, 10, 'Total', 1);
$pdf->Ln();

// Menampilkan Produk dalam Tabel dari session
foreach ($selectedProducts as $product) {
    $pdf->Cell(80, 10, $product['name'], 1);
    $pdf->Cell(30, 10, $product['quantity'], 1);
    $pdf->Cell(40, 10, 'Rp ' . number_format($product['price'], 0, ',', '.'), 1);
    $pdf->Cell(40, 10, 'Rp ' . number_format($product['quantity'] * $product['price'], 0, ',', '.'), 1);
    $pdf->Ln();
}

// Total Pembayaran di bawah kanan tabel
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, 'Total Pembayaran: Rp ' . number_format($transaction['amount'], 2, ',', '.'), 0, 1, 'R');

// Simpan PDF ke server
$pdfFileName = $invoiceDirectory . 'Invoice_' . $invoice['invoice_number'] . '.pdf';
$pdf->Output('F', $pdfFileName);

// Update tabel invoice dengan path file PDF
$updatePDFPath = "UPDATE invoice SET pdf_file = ?, pdf_status = 'created' WHERE invoice_number = ?";
$stmt = $koneksi->prepare($updatePDFPath);
$stmt->bind_param("ss", $pdfFileName, $invoice['invoice_number']);
$stmt->execute();

// Output PDF untuk diunduh pengguna
$pdf->Output('D', 'Invoice_' . $invoice['invoice_number'] . '.pdf');

// Tutup koneksi
$koneksi->close();
?>
