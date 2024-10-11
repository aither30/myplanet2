<?php
session_start();
include("../config/config.php");

// Ambil username dari session
$username = $_SESSION['username'];

// Dapatkan vendor_id berdasarkan username
$query_vendor = "SELECT vendor_id FROM business_account WHERE username = '$username'";
$result_vendor = $koneksi->query($query_vendor);

if ($result_vendor->num_rows > 0) {
    $row_vendor = $result_vendor->fetch_assoc();
    $vendor_id = $row_vendor['vendor_id'];
}

// Query untuk mengambil daftar paket iklan yang tersedia
$query_paket = "SELECT paket_id, nama_paket, harga, durasi, jumlah_iklan_landscape, jumlah_iklan_slider2, jumlah_iklan_slider3 FROM paket_ads";
$result_paket = $koneksi->query($query_paket);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembelian Paket Iklan</title>
    <style>
        body {
            font-family: 'Arial', sans-serif;
            margin: 0;
            padding: 20px;
            background-color: #f0f0f0;
        }
        .container {
            max-width: 1200px;
            margin: auto;
            padding: 20px;
        }
        h1 {
            text-align: center;
            margin-bottom: 30px;
        }
        .card-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
        }
        .card {
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            text-align: center;
            transition: transform 0.3s ease;
        }
        .card:hover {
            transform: translateY(-10px);
        }
        .card h2 {
            margin: 0 0 10px;
            font-size: 1.5em;
        }
        .card p {
            margin: 10px 0;
            font-size: 1.1em;
        }
        .price {
            color: #4CAF50;
            font-size: 1.5em;
            font-weight: bold;
        }
        .btn {
            padding: 10px 20px;
            font-size: 16px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        .btn:hover {
            background-color: #45a049;
        }
        .back-btn {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
            text-align: center;
        }
        .back-btn:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Pilih Paket Iklan</h1>

    <!-- Card Design untuk Paket Iklan -->
    <div class="card-container">
        <?php
        if ($result_paket->num_rows > 0) {
            while ($row_paket = $result_paket->fetch_assoc()) {
                echo "<div class='card'>
                    <h2>{$row_paket['nama_paket']}</h2>
                    <p class='price'>Rp " . number_format($row_paket['harga'], 2, ',', '.') . "</p>
                    <p>Durasi: {$row_paket['durasi']} hari</p>
                    <p>Jumlah Iklan Landscape: {$row_paket['jumlah_iklan_landscape']}</p>
                    <p>Jumlah Iklan Slider 2: {$row_paket['jumlah_iklan_slider2']}</p>
                    <p>Jumlah Iklan Slider 3: {$row_paket['jumlah_iklan_slider3']}</p>
                    <form action='confirm_payment.php' method='POST'>
                        <input type='hidden' name='paket_id' value='{$row_paket['paket_id']}'>
                        <input type='hidden' name='vendor_id' value='$vendor_id'>
                        <button type='submit' class='btn'>Konfirmasi Pembelian</button>
                    </form>
                </div>";
            }
        } else {
            echo "<p>Tidak ada paket iklan yang tersedia.</p>";
        }
        ?>
    </div>

    <!-- Tombol Kembali -->
    <button class="back-btn" onclick="window.location.href='index.php'">Kembali ke Halaman Utama</button>
</div>

</body>
</html>

<?php
$koneksi->close();
?>
