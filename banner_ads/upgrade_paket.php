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

    // Ambil paket saat ini yang dimiliki oleh vendor
    $query_paket_current = "
        SELECT p.nama_paket, p.harga, p.durasi, p.paket_id
        FROM pembelian_paket_ads ppa
        JOIN paket_ads p ON ppa.paket_id = p.paket_id
        WHERE ppa.vendor_id = '$vendor_id' AND ppa.status_pembayaran = 'paid' AND ppa.status_iklan = 'active'
        LIMIT 1
    ";
    $result_paket_current = $koneksi->query($query_paket_current);
    $current_paket = $result_paket_current->fetch_assoc();
    
    // Ambil daftar paket yang lebih tinggi dari paket saat ini
    $query_upgrade_paket = "
        SELECT * FROM paket_ads WHERE harga > '{$current_paket['harga']}' ORDER BY harga ASC
    ";
    $result_upgrade_paket = $koneksi->query($query_upgrade_paket);
} else {
    echo "Vendor tidak ditemukan.";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upgrade Paket Iklan</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 20px;
        }
        .container {
            max-width: 900px;
            margin: auto;
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        h1 {
            font-size: 2em;
            color: #333;
            margin-bottom: 20px;
        }
        .current-package {
            background-color: #f9f9f9;
            border: 1px solid #ddd;
            padding: 20px;
            margin-bottom: 30px;
            border-radius: 8px;
        }
        .current-package h2 {
            color: #4CAF50;
            margin-bottom: 10px;
        }
        .package-card {
            background-color: #fafafa;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        .package-card h3 {
            color: #333;
        }
        .price {
            font-size: 1.6em;
            color: #4CAF50;
            margin: 10px 0;
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
    <h1>Upgrade Paket Iklan</h1>

    <!-- Menampilkan paket saat ini -->
    <div class="current-package">
        <h2>Paket Saat Ini: <?php echo $current_paket['nama_paket']; ?></h2>
        <p>Harga: Rp <?php echo number_format($current_paket['harga'], 2, ',', '.'); ?></p>
        <p>Durasi: <?php echo $current_paket['durasi']; ?> hari</p>
    </div>

    <!-- Menampilkan pilihan upgrade paket -->
    <?php if ($result_upgrade_paket->num_rows > 0) { ?>
        <?php while ($row_upgrade = $result_upgrade_paket->fetch_assoc()) { ?>
            <div class="package-card">
                <h3><?php echo $row_upgrade['nama_paket']; ?></h3>
                <p>Harga: <span class="price">Rp <?php echo number_format($row_upgrade['harga'], 2, ',', '.'); ?></span></p>
                <p>Durasi: <?php echo $row_upgrade['durasi']; ?> hari</p>
                <p>Jumlah Iklan Landscape: <?php echo $row_upgrade['jumlah_iklan_landscape']; ?></p>
                <p>Jumlah Iklan Slider 2: <?php echo $row_upgrade['jumlah_iklan_slider2']; ?></p>
                <p>Jumlah Iklan Slider 3: <?php echo $row_upgrade['jumlah_iklan_slider3']; ?></p>
                <form action="confirm_payment.php" method="POST">
                    <input type="hidden" name="paket_id" value="<?php echo $row_upgrade['paket_id']; ?>">
                    <input type="hidden" name="vendor_id" value="<?php echo $vendor_id; ?>">
                    <button type="submit" class="btn">Pilih Paket Ini</button>
                </form>
            </div>
        <?php } ?>
    <?php } else { ?>
        <p>Tidak ada paket yang tersedia untuk diupgrade saat ini.</p>
    <?php } ?>

    <button class="btn" onclick="window.location.href='index.php'">Kembali ke Halaman Utama</button>
</div>

</body>
</html>

<?php
$koneksi->close();
?>
