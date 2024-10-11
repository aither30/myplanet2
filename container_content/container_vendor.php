<?php
// Koneksi ke database
$host = "localhost";
$username = "root";
$password = "";
$database = "myplanet_db";

$koneksi = new mysqli($host, $username, $password, $database);

// Periksa koneksi
if ($koneksi->connect_error) {
    die("Koneksi gagal: " . $koneksi->connect_error);
}

// Ambil daftar vendor dari tabel business_account
$sql = "SELECT vendor_id, company_name, logo, jenis_bisnis FROM business_account WHERE type_account = 'vendor'";
$result = $koneksi->query($sql);

echo '<div class="container-vendor">';
echo '<div class="vendor-container">';

// Loop melalui setiap vendor di database
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Ambil data vendor dari database
        $vendorName = htmlspecialchars($row['company_name']);
        $vendorDescription = htmlspecialchars($row['jenis_bisnis']); // Deskripsi sebagai jenis bisnis
        $vendorImage = !empty($row['logo']) ? '../myplanet2/dashboard_vendor/' . $row['logo'] : './assets/placeholder.jpg'; // Gunakan gambar logo atau default

        // Output vendor dalam bentuk kartu
        echo '
        <div class="vendor-card">
            <img src="' . $vendorImage . '" alt="' . $vendorName . '" class="vendor-image">
            <div class="vendor-info">
                <h3 class="vendor-name">' . $vendorName . '</h3>
                <p class="vendor-description">' . $vendorDescription . '</p>
                <a href="../myplanet2/content_detail_vendor_myplanet/?vendor_id=' . $row['vendor_id'] . '" class="vendor-link">Lihat Detail</a>
            </div>
        </div>';
    }
} else {
    echo "<p>Tidak ada vendor yang tersedia.</p>";
}

echo '</div>';
echo '</div>';

// Tutup koneksi
$koneksi->close();
?>
