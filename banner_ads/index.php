<?php
session_start();
include("../config/config.php");

// Ambil username dari session
$username = $_SESSION['username'];

// Dapatkan vendor_id berdasarkan username
$query_vendor = "SELECT vendor_id FROM business_account WHERE username = '$username'";
$result_vendor = $koneksi->query($query_vendor);

$has_active_package = false;
$jumlah_iklan_landscape = 0;
$jumlah_iklan_slider2 = 0;
$jumlah_iklan_slider3 = 0;

if ($result_vendor->num_rows > 0) {
    $row_vendor = $result_vendor->fetch_assoc();
    $vendor_id = $row_vendor['vendor_id'];

    // Query untuk mendapatkan semua paket aktif yang dibeli oleh vendor
    $query = "
        SELECT p.nama_paket, p.harga, p.durasi, p.jumlah_iklan_landscape, p.jumlah_iklan_slider2, p.jumlah_iklan_slider3,
               ppa.tanggal_pembayaran, ppa.tanggal_expired
        FROM pembelian_paket_ads ppa
        JOIN paket_ads p ON ppa.paket_id = p.paket_id
        WHERE ppa.vendor_id = '$vendor_id' AND ppa.status_pembayaran = 'paid' AND ppa.status_iklan = 'active'
    ";
    $result = $koneksi->query($query);

    // Menjumlahkan slot iklan dari semua paket aktif
    if ($result->num_rows > 0) {
        $has_active_package = true;
        $active_packages = [];

        while ($row = $result->fetch_assoc()) {
            // Simpan paket aktif dalam array untuk ditampilkan
            $active_packages[] = $row;

            // Tambahkan slot iklan ke total
            $jumlah_iklan_landscape += $row['jumlah_iklan_landscape'];
            $jumlah_iklan_slider2 += $row['jumlah_iklan_slider2'];
            $jumlah_iklan_slider3 += $row['jumlah_iklan_slider3'];
        }
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Iklan Banner</title>
    <link rel="stylesheet" href="../css/style.nav.css"> <!-- Menghubungkan dengan file CSS yang terpisah -->
    <link rel="stylesheet" href="style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet"> <!-- Menggunakan Google Fonts -->
</head>
<body>

<!-- Navigation Bar -->
<?php include("../container_content/nav.php") ?>

<div class="main-container">
    <!-- Hero Section -->
    <section class="hero-section">
        <div class="hero-text">
            <h1>Kelola Iklan Banner Anda</h1>
            <p>Optimalkan visibilitas bisnis Anda dengan iklan yang efektif. Pilih paket dan kelola iklan dengan mudah.</p>
            <?php if ($has_active_package) { ?>
                <a href="upgrade_paket.php" class="hero-btn">Upgrade Paket</a>
            <?php } else { ?>
                <a href="pembelian_paket.php" class="hero-btn">Beli Paket Iklan</a>
            <?php } ?>
        </div>
        <div class="hero-image">
            <i class="fas fa-ad" style="font-size: 200px; color: #fff;"></i>
        </div>
    </section>

<!-- Informasi Paket Section -->
<section class="package-info">
    <div class="package-card">
        <h2>Paket Iklan Anda</h2>
        <div class="package-row"> <!-- Menggunakan Flexbox atau Grid untuk menampilkan paket secara horizontal -->
            <?php if ($has_active_package) { ?>
                <?php foreach ($active_packages as $package) { ?>
                    <div class="package-item">
                        <div class="card">
                            <div class="card-header">
                                <h3><?= $package['nama_paket'] ?></h3>
                            </div>
                            <div class="card-body">
                                <p><strong>Harga:</strong> Rp <?= number_format($package['harga'], 2, ',', '.') ?></p>
                                <p><strong>Durasi:</strong> <?= $package['durasi'] ?> hari</p>
                                <p><strong>Tanggal Pembayaran:</strong> <?= $package['tanggal_pembayaran'] ?></p>
                                <p><strong>Tanggal Expired:</strong> <?= $package['tanggal_expired'] ?></p>
                                <p><strong>Jumlah Iklan Landscape:</strong> <?= $package['jumlah_iklan_landscape'] ?></p>
                                <p><strong>Jumlah Iklan Slider 2:</strong> <?= $package['jumlah_iklan_slider2'] ?></p>
                                <p><strong>Jumlah Iklan Slider 3:</strong> <?= $package['jumlah_iklan_slider3'] ?></p>
                            </div>
                        </div>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>Anda belum mempunyai paket iklan aktif.</p>
            <?php } ?>
        </div>
    </div>
</section>



    <!-- Kelola Iklan Section -->
    <section class="manage-ads">
        <div class="ads-card">
            <h2>Kelola Iklan Anda</h2>
            <div class="ads-slots">
                <div class="slot">
                    <h3>Landscape</h3>
                    <p>Slot tersedia: <?= $jumlah_iklan_landscape ?></p>
                    <?php if ($jumlah_iklan_landscape > 0) { ?>
                        <a href="kelola_banner1.php" class="manage-btn">Kelola Iklan Landscape</a>
                    <?php } ?>
                </div>
                <div class="slot">
                    <h3>Slider 2</h3>
                    <p>Slot tersedia: <?= $jumlah_iklan_slider2 ?></p>
                    <?php if ($jumlah_iklan_slider2 > 0) { ?>
                        <a href="kelola_banner2.php" class="manage-btn">Kelola Iklan Slider 2</a>
                    <?php } ?>
                </div>
                <div class="slot">
                    <h3>Slider 3</h3>
                    <p>Slot tersedia: <?= $jumlah_iklan_slider3 ?></p>
                    <?php if ($jumlah_iklan_slider3 > 0) { ?>
                        <a href="kelola_banner3.php" class="manage-btn">Kelola Iklan Slider 3</a>
                    <?php } ?>
                </div>
            </div>
        </div>
    </section>
</div>

</body>
</html>
