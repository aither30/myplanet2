<?php
session_start();
include("../config/config.php");

// Ambil username dari session
$username = $_SESSION['username'];

// Dapatkan vendor_id berdasarkan username
$query_vendor = "SELECT vendor_id FROM business_account WHERE username = '$username'";
$result_vendor = $koneksi->query($query_vendor);

$has_active_package = false; // Variable to track if the vendor has an active package
$jumlah_iklan_landscape = 0;
$jumlah_iklan_slider2 = 0;
$jumlah_iklan_slider3 = 0;

if ($result_vendor->num_rows > 0) {
    $row_vendor = $result_vendor->fetch_assoc();
    $vendor_id = $row_vendor['vendor_id'];

    // Query untuk mendapatkan paket iklan yang dibeli oleh vendor
    $query = "
        SELECT p.nama_paket, p.harga, p.durasi, p.jumlah_iklan_landscape, p.jumlah_iklan_slider2, p.jumlah_iklan_slider3,
               ppa.tanggal_pembayaran, ppa.tanggal_expired
        FROM pembelian_paket_ads ppa
        JOIN paket_ads p ON ppa.paket_id = p.paket_id
        WHERE ppa.vendor_id = '$vendor_id' AND ppa.status_pembayaran = 'paid' AND ppa.status_iklan = 'active'
    ";
    $result = $koneksi->query($query);

    if ($result->num_rows > 0) {
        $has_active_package = true; // Set to true if the vendor has an active package
        $row = $result->fetch_assoc();
        $jumlah_iklan_landscape = $row['jumlah_iklan_landscape'];
        $jumlah_iklan_slider2 = $row['jumlah_iklan_slider2'];
        $jumlah_iklan_slider3 = $row['jumlah_iklan_slider3'];
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Iklan Banner</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            background-color: #f0f0f0;
        }
        .container {
            text-align: center;
        }
        .btn {
            padding: 15px 25px;
            font-size: 18px;
            margin: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background-color: #4CAF50;
            color: white;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .package-info {
            margin: 20px;
            font-size: 16px;
            color: #333;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Kelola Iklan Banner</h1>

    <!-- Tombol hanya muncul jika vendor memiliki iklan landscape, slider2, atau slider3 -->
    <?php if ($jumlah_iklan_landscape > 0) { ?>
        <button class="btn" onclick="window.location.href='kelola_banner1.php'">Kelola Iklan Landscape</button>
    <?php } ?>
    
    <?php if ($jumlah_iklan_slider2 > 0) { ?>
        <button class="btn" onclick="window.location.href='kelola_banner2.php'">Kelola Iklan Slider 2</button>
    <?php } ?>
    
    <?php if ($jumlah_iklan_slider3 > 0) { ?>
        <button class="btn" onclick="window.location.href='kelola_banner3.php'">Kelola Iklan Slider 3</button>
    <?php } ?>

    <!-- Tombol untuk pembelian atau upgrade paket iklan -->
    <?php if ($has_active_package) { ?>
        <button class="btn" onclick="window.location.href='upgrade_paket.php'">Upgrade Paket</button>
    <?php } else { ?>
        <button class="btn" onclick="window.location.href='pembelian_paket.php'">Beli Paket Iklan</button>
    <?php } ?>

    <!-- Menampilkan informasi paket iklan yang dimiliki -->
    <div class="package-info">
        <?php
        if (isset($result) && $result->num_rows > 0) {
            echo "Paket Iklan Anda: " . $row['nama_paket'] . "<br>";
            echo "Harga: Rp " . number_format($row['harga'], 2, ',', '.') . "<br>";
            echo "Durasi: " . $row['durasi'] . " hari<br>";
            echo "Tanggal Pembayaran: " . $row['tanggal_pembayaran'] . "<br>";
            echo "Tanggal Kedaluwarsa: " . $row['tanggal_expired'] . "<br>";
        } else {
            echo "Anda belum mempunyai paket iklan.";
        }
        ?>
    </div>
</div>

</body>
</html>
