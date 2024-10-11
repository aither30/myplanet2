<?php
session_start();
include("../config/config.php");

// Ambil order ID dari query string
$order_id = $_GET['order_id'];

// Ambil informasi transaksi dari database berdasarkan order_id
$query = "SELECT p.nama_paket, p.harga, p.durasi, ppa.status_pembayaran
          FROM pembelian_paket_ads ppa
          JOIN paket_ads p ON ppa.paket_id = p.paket_id
          WHERE ppa.order_id = '$order_id'";
$result = $koneksi->query($query);
$row = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Konfirmasi Pembelian Paket</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 800px;
            margin: auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        h1 {
            text-align: center;
            margin-bottom: 20px;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            cursor: pointer;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #45a049;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Konfirmasi Pembelian Paket Iklan</h1>

    <p><strong>Nama Paket:</strong> <?php echo $row['nama_paket']; ?></p>
    <p><strong>Harga:</strong> Rp <?php echo number_format($row['harga'], 2, ',', '.'); ?></p>
    <p><strong>Durasi:</strong> <?php echo $row['durasi']; ?> hari</p>
    <p><strong>Status Pembayaran:</strong> <?php echo $row['status_pembayaran']; ?></p>

    <button class="btn" onclick="window.location.href='pembelian_paket.php'">Kembali</button>
</div>

</body>
</html>

<?php
$koneksi->close();
?>
