<?php
session_start();
require("../pdf/fpdf.php");
require("../config/config.php");

$invoiceNotFound = false;
$invoiceDetails = null;
$transactionDetails = null;
$invoicePdfCreated = false;

if (isset($_POST['invoice_number'])) {
    $invoice_number = $_POST['invoice_number'];

    // Query untuk mendapatkan detail invoice berdasarkan nomor invoice
    $queryInvoice = "
        SELECT i.invoice_number, i.created_at, i.transaction_id, i.user_name, i.user_email, i.user_phone, i.user_address, 
               i.product_name, i.product_quantity, i.product_price, i.total_price
        FROM invoice i
        WHERE i.invoice_number = ?
    ";
    
    $stmt = $koneksi->prepare($queryInvoice);
    $stmt->bind_param("s", $invoice_number);
    $stmt->execute();
    $result = $stmt->get_result();

    // Cek apakah ada hasil invoice
    if ($result->num_rows > 0) {
        $invoiceDetails = $result->fetch_assoc();

        // Ambil transaction_id dari invoice
        $transaction_id = $invoiceDetails['transaction_id'];

        // Query untuk mendapatkan detail transaksi berdasarkan transaction_id
        $queryTransaction = "
            SELECT t.transaction_id, t.payment_method, t.status, t.amount, t.payment_date
            FROM transaction t
            WHERE t.transaction_id = ?
        ";

        $stmtTransaction = $koneksi->prepare($queryTransaction);
        $stmtTransaction->bind_param("i", $transaction_id);
        $stmtTransaction->execute();
        $resultTransaction = $stmtTransaction->get_result();

        // Cek apakah ada hasil transaksi
        if ($resultTransaction->num_rows > 0) {
            $transactionDetails = $resultTransaction->fetch_assoc();
        }

        // Jika invoice dan transaksi ditemukan, buat PDF
        if ($invoiceDetails && $transactionDetails) {
            // Buat PDF menggunakan FPDF
            $pdf = new FPDF();
            $pdf->AddPage();

            // Nama Perusahaan di sisi kiri atas
            $pdf->SetFont('Arial', 'B', 16);
            $pdf->Cell(100, 10, 'My PlanET', 0, 0, 'L');

            // No. Invoice di sisi kanan atas
            $pdf->SetFont('Arial', '', 12);
            $pdf->Cell(0, 10, 'No. Invoice: ' . $invoiceDetails['invoice_number'], 0, 1, 'R');

            // Tanggal di sisi kanan, tepat di bawah nomor invoice
            $pdf->Cell(0, 10, 'Tanggal: ' . date('d-m-Y', strtotime($invoiceDetails['created_at'])), 0, 1, 'R');
            $pdf->Ln(10); // Jarak setelah nama perusahaan dan nomor invoice

            // Lebar kolom untuk informasi di sisi kiri dan sisi kanan
            $leftColumnWidth = 100;
            $rightColumnWidth = 60;
            $cellHeight = 10;
            $verticalGap = 5;

            // Informasi Pembeli di sisi kiri tabel
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell($leftColumnWidth, $cellHeight, 'Informasi Pembeli', 0, 0, 'L');
            $pdf->SetFont('Arial', '', 12);

            // Posisikan header kanan (Detail Transaksi) di sebelah kanan tanpa pindah baris
            $pdf->SetXY(130, $pdf->GetY());
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell($rightColumnWidth, $cellHeight, 'Detail Transaksi', 0, 1, 'L');
            $pdf->SetFont('Arial', '', 12);

            // Detail Informasi Pembeli
            $pdf->Cell($leftColumnWidth, $cellHeight, 'Nama: ' . $invoiceDetails['user_name'], 0, 0, 'L');
            $pdf->SetXY(130, $pdf->GetY());
            $pdf->Cell($rightColumnWidth, $cellHeight, 'Nomor Transaksi: ' . $transactionDetails['transaction_id'], 0, 1, 'L');

            $pdf->Cell($leftColumnWidth, $cellHeight, 'Email: ' . $invoiceDetails['user_email'], 0, 0, 'L');
            $pdf->SetXY(130, $pdf->GetY());
            $pdf->Cell($rightColumnWidth, $cellHeight, 'Metode Pembayaran: ' . ucfirst($transactionDetails['payment_method']), 0, 1, 'L');

            $pdf->Cell($leftColumnWidth, $cellHeight, 'Telepon: ' . $invoiceDetails['user_phone'], 0, 0, 'L');
            $pdf->SetXY(130, $pdf->GetY());
            $pdf->Cell($rightColumnWidth, $cellHeight, 'Status Pembayaran: ' . ucfirst($transactionDetails['status']), 0, 1, 'L');

            $pdf->Cell($leftColumnWidth, $cellHeight, 'Alamat: ' . $invoiceDetails['user_address'], 0, 1, 'L');
            $pdf->Ln($verticalGap); // Tambahkan jarak setelah informasi

            // Kembali ke posisi awal untuk tabel produk
            $pdf->SetXY(10, 90);

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
            $pdf->Cell(80, 10, $invoiceDetails['product_name'], 1);
            $pdf->Cell(30, 10, $invoiceDetails['product_quantity'], 1);
            $pdf->Cell(40, 10, 'Rp ' . number_format($invoiceDetails['product_price'], 0, ',', '.'), 1);
            $pdf->Cell(40, 10, 'Rp ' . number_format($invoiceDetails['total_price'], 0, ',', '.'), 1);
            $pdf->Ln();

            // Total Pembayaran di bawah kanan tabel
            $pdf->SetFont('Arial', 'B', 12);
            $pdf->Cell(0, 10, 'Total Pembayaran: Rp ' . number_format($transactionDetails['amount'], 2, ',', '.'), 0, 1, 'R');

            // Simpan PDF ke file
            $pdfFileName = 'Invoice_' . $invoiceDetails['invoice_number'] . '.pdf';
            $pdf->Output('F', $pdfFileName);

            // Tandai bahwa PDF berhasil dibuat
            $invoicePdfCreated = true;
        }

        $stmtTransaction->close();
    } else {
        $invoiceNotFound = true;
    }

    $stmt->close();
}

$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Cek Invoice</title>
    <link rel="stylesheet" href="stylessearch.css"> <!-- Menghubungkan file CSS -->
</head>
<body>
    <?php include ("../container_content/nav.php")?>
    <div class="container">
        <h2>Cek Transaksi</h2>
        <form action="" method="POST">
            <label for="invoice_number">Masukkan Nomor Invoice:</label>
            <input type="text" id="invoice_number" name="invoice_number" required>
            <input type="submit" value="Cek Invoice">
        </form>

        <?php if ($invoiceDetails && $transactionDetails): ?>
            <h3>Detail Invoice:</h3>
            <table>
                <tr>
                    <th>No. Invoice</th>
                    <th>Nama Pembeli</th>
                    <th>Produk</th>
                    <th>Kuantitas</th>
                    <th>Harga Satuan</th>
                    <th>Total Harga</th>
                </tr>
                <tr>
                    <td><?= htmlspecialchars($invoiceDetails['invoice_number']) ?></td>
                    <td><?= htmlspecialchars($invoiceDetails['user_name']) ?></td>
                    <td><?= htmlspecialchars($invoiceDetails['product_name']) ?></td>
                    <td><?= htmlspecialchars($invoiceDetails['product_quantity']) ?></td>
                    <td>Rp <?= number_format($invoiceDetails['product_price'], 2, ',', '.') ?></td>
                    <td>Rp <?= number_format($invoiceDetails['total_price'], 2, ',', '.') ?></td>
                </tr>
            </table>

            <h3>Detail Transaksi:</h3>
            <table>
                <tr>
                    <th>No. Transaksi</th>
                    <th>Metode Pembayaran</th>
                    <th>Status</th>
                    <th>Total Pembayaran</th>
                    <th>Tanggal Pembayaran</th>
                </tr>
                <tr>
                    <td><?= htmlspecialchars($transactionDetails['transaction_id']) ?></td>
                    <td><?= htmlspecialchars($transactionDetails['payment_method']) ?></td>
                    <td><?= htmlspecialchars($transactionDetails['status']) ?></td>
                    <td>Rp <?= number_format($transactionDetails['amount'], 2, ',', '.') ?></td>
                    <td><?= htmlspecialchars(date('d-m-Y H:i:s', strtotime($transactionDetails['payment_date']))) ?></td>
                </tr>
            </table>

            <!-- Tambahkan tombol Download Invoice -->
            <?php if ($invoicePdfCreated): ?>
                <a href="<?= $pdfFileName ?>" class="download-btn" download>Download Invoice (PDF)</a>
            <?php else: ?>
                <button class="disabled-btn" disabled>Download Invoice (PDF)</button>
            <?php endif; ?>
        <?php elseif ($invoiceNotFound): ?>
            <p class="not-found">Invoice dengan nomor <?= htmlspecialchars($invoice_number) ?> tidak ditemukan.</p>
        <?php endif; ?>
    </div>


</body>
</html>
