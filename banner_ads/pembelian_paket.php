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
    <link rel="stylesheet" href="../css/main.css">
    <link rel="stylesheet" href="stylepembelianpaket.css">
</head>
<body>
<?php include ("../container_content/nav.php")?>

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
