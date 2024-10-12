<?php
session_start();
include("../config/config.php");

// Ambil username dari session
$username = $_SESSION['username'];

// Dapatkan vendor_id berdasarkan username
$query_vendor = "SELECT vendor_id FROM business_account WHERE username = '$username'";
$result_vendor = $koneksi->query($query_vendor);

$has_active_package = false;

if ($result_vendor->num_rows > 0) {
    $row_vendor = $result_vendor->fetch_assoc();
    $vendor_id = $row_vendor['vendor_id'];

    // Ambil semua paket yang aktif yang dimiliki oleh vendor
    $query_paket_active = "
        SELECT p.nama_paket, p.harga, p.durasi, p.jumlah_iklan_landscape, p.jumlah_iklan_slider2, p.jumlah_iklan_slider3,
               ppa.tanggal_pembayaran, ppa.tanggal_expired
        FROM pembelian_paket_ads ppa
        JOIN paket_ads p ON ppa.paket_id = p.paket_id
        WHERE ppa.vendor_id = '$vendor_id' AND ppa.status_pembayaran = 'paid' AND ppa.status_iklan = 'active'
    ";
    $result_paket_active = $koneksi->query($query_paket_active);
    $active_packages = [];

    if ($result_paket_active->num_rows > 0) {
        $has_active_package = true;
        while ($row = $result_paket_active->fetch_assoc()) {
            $active_packages[] = $row;
        }
    }

    // Ambil daftar paket yang lebih tinggi dari paket tertinggi yang aktif
    $query_upgrade_paket = "
        SELECT * FROM paket_ads WHERE harga > (
            SELECT MAX(p.harga) FROM paket_ads p
            JOIN pembelian_paket_ads ppa ON ppa.paket_id = p.paket_id 
            WHERE ppa.vendor_id = '$vendor_id' AND ppa.status_pembayaran = 'paid' AND ppa.status_iklan = 'active'
        ) ORDER BY harga ASC
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
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="style.upgrade.css"> <!-- Menghubungkan dengan file CSS eksternal -->
    <link rel="stylesheet" href="../css/style.nav.css">
</head>
<body>
<?php include ("../container_content/nav.php")?>
<div class="container">
    <h1>Upgrade Paket Iklan</h1>

    <!-- Menampilkan semua paket aktif dan pilihan upgrade dalam satu row -->
    <div class="row">

        <!-- Card: Paket Aktif -->
        <div class="current-package-card">
            <h2>Paket Aktif Anda:</h2>
            <div class="card-row">
            <?php if ($has_active_package) { ?>
                <?php foreach ($active_packages as $package) { ?>
                    <div class="package-card">
                        <h3><?php echo $package['nama_paket']; ?></h3>
                        <p>Harga: Rp <?php echo number_format($package['harga'], 2, ',', '.'); ?></p>
                        <p>Durasi: <?php echo $package['durasi']; ?> hari</p>
                        <p>Jumlah Iklan Landscape: <?php echo $package['jumlah_iklan_landscape']; ?></p>
                        <p>Jumlah Iklan Slider 2: <?php echo $package['jumlah_iklan_slider2']; ?></p>
                        <p>Jumlah Iklan Slider 3: <?php echo $package['jumlah_iklan_slider3']; ?></p>
                        <button class="btn" disabled>Paket Anda Saat Ini</button>
                        <hr>
                    </div>
                <?php } ?>
            <?php } else { ?>
                <p>Tidak ada paket aktif saat ini.</p>
            <?php } ?>
            </div>
        </div>

        <!-- Menampilkan pilihan upgrade paket -->
        <div class="current-package-card">
        <h2>Paket Upgrade Anda</h2>
        <div class="card-row">

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
            <p class="no-result">Tidak ada paket yang tersedia untuk diupgrade saat ini.</p>
        <?php } ?>
        </div>

        </div>
    </div>

    <button class="btn back-btn" onclick="window.location.href='index.php'">Kembali ke Halaman Utama</button>
</div>

</body>
</html>
