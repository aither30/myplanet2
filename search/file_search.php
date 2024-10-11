<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

include ("../config/config.php");

$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$filter_price_min = isset($_GET['filter_price_min']) ? $_GET['filter_price_min'] : '';
$filter_price_max = isset($_GET['filter_price_max']) ? $_GET['filter_price_max'] : '';
$filter_category = isset($_GET['filter_category']) ? $_GET['filter_category'] : '';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="stylestheme.css"> <!-- Pastikan path benar -->
    <title>Hasil Pencarian</title>
</head>
<body>
<nav>
        <div class="left_nav">
            <div class="logo">
                <img src="../assets/attribute myplanet/Logo My PlanET.png" alt="My PlanET" />
                <a href="../home.php">My PlanET</a>
            </div>
        </div>
        <div class="mid_nav">
             <!-- Form pencarian di bagian atas halaman -->
<div class="search-form-container">
    <form action="file_search.php" method="GET">
        <input type="text" name="search_query" placeholder="Masukkan kata kunci..." value="<?php echo $search_query; ?>" required>
        <button type="submit"><i class="fa-solid fa-magnifying-glass"></i></button>
    </form>
</div>

        </div>
        <div class="right_nav">
            <button id="openCartBtn"><i class="fa-solid fa-cart-shopping"></i></button>
            <button id="theme-toggle"><i class="fa-solid fa-circle-half-stroke"></i></button>
            <button id="openChatBtn" class="open-chat-btn"><i class="fa-solid fa-message"></i></button>
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <div class="Dropdown">
                    <div class="profil">
                        <button><?php echo $_SESSION['username']; ?></button>
                    </div>
                    <div class="Content-dropdown">
                        <a href="./dashboard_user/index.php">Dashboard</a>
                        <a href="./system.message/index.php">Pesan</a>
                        <a href="../logout.php">Keluar</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="masuk-daftar">
                    <a href="login.php">Masuk</a>
                    <a href="register.php">Daftar</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>

<div class="search-container">
    <!-- Kolom filter kiri -->
    <div class="filter-column-1">
        <h3>Filter Produk</h3>
        <form action="file_search.php" method="GET">
            <input type="hidden" name="search_query" value="<?php echo $search_query; ?>">

            <!-- Filter Harga -->
            <label for="filter_price_min">Harga Minimum:</label>
            <input type="number" name="filter_price_min" id="filter_price_min" placeholder="Harga Minimum" value="<?php echo $filter_price_min; ?>"><br>

            <label for="filter_price_max">Harga Maksimum:</label>
            <input type="number" name="filter_price_max" id="filter_price_max" placeholder="Harga Maksimum" value="<?php echo $filter_price_max; ?>"><br>

            <!-- Filter Kategori -->
            <label for="filter_category">Kategori Produk:</label>
            <select name="filter_category" id="filter_category">
                <option value="">Pilih Kategori</option>
                <option value="Elektronik" <?php if ($filter_category == 'Elektronik') echo 'selected'; ?>>Elektronik</option>
                <option value="Pakaian" <?php if ($filter_category == 'Pakaian') echo 'selected'; ?>>Pakaian</option>
                <option value="Makanan" <?php if ($filter_category == 'Makanan') echo 'selected'; ?>>Makanan</option>
                <option value="Perlengkapan" <?php if ($filter_category == 'Perlengkapan') echo 'selected'; ?>>Perlengkapan</option>
            </select><br>

            <div class="button">
            <button type="submit">Terapkan Filter</button>
            <button type="reset" onclick="window.location.href='file_search.php'">Reset Filter</button> <!-- Tombol Reset Filter -->
            </div>
        </form>
    </div>


    <!-- Kolom hasil pencarian di tengah -->
    <div class="result-column">
    <div class="result-grid">
    <?php
        if (!empty($search_query)) {
            // Filter tambahan untuk produk
            $product_filter = '';
            if (!empty($filter_price_min)) {
                $product_filter .= " AND p.prices >= $filter_price_min";
            }
            if (!empty($filter_price_max)) {
                $product_filter .= " AND p.prices <= $filter_price_max";
            }
            if (!empty($filter_category)) {
                $product_filter .= " AND p.jenis_product = '$filter_category'";
            }


    $sql = "
        SELECT b.vendor_id AS id, b.company_name AS vendor_name, b.logo AS vendor_logo, b.address AS vendor_address
        FROM business_account b
        WHERE b.company_name LIKE '%$search_query%'
           OR b.name_owner LIKE '%$search_query%'
           OR b.email LIKE '%$search_query%'
           OR b.phone_vendor LIKE '%$search_query%'
    ";

    $result = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($result) > 0) {
        // Menampilkan card vendor
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<div class='vendor-card'>"; // Card untuk vendor
            echo "
                <div class='vendor-info2'>
                    <div class='image2'>
                    <img src='../dashboard_Vendor/" . $row['vendor_logo'] . "' alt='Logo Vendor' class='vendor-logo2'>
                    </div>
                    <div class='infoVendor2'>
                    <span class='vendor-name2'>" . $row['vendor_name'] . "</span>
                    <span class='vendor-address2'>" . $row['vendor_address'] . "</span>
                    <a href='../content_detail_vendor/?vendor_id=" . $row['id'] . "'>
                        <button class='detail-button'>Lihat Detail Vendor</button>
                    </a>
                    </div>
                </div>";
            echo "</div>"; // Tutup div vendor-card
        }
    } else {
        echo "<div class='tidakada-hasil'>
        <div class='no-results'>Tidak ada hasil yang ditemukan untuk vendor '$search_query'.</div>
        </div>";
    }
    ?>
</div> <!-- Tutup div grid vendor -->

        <!-- Grid untuk produk -->
        <div class="result-grid">
        <?php

            // Query untuk pencarian produk
            $sql = "
                SELECT p.product_id AS id, p.name AS product_name, p.prices, b.company_name AS vendor_name, b.logo AS vendor_logo, p.images AS product_image, b.address AS vendor_address
                FROM product p
                JOIN business_account b ON p.vendor_id = b.vendor_id
                WHERE (p.name LIKE '%$search_query%' OR p.description LIKE '%$search_query%') $product_filter
            ";

            $result = mysqli_query($koneksi, $sql);

            if (mysqli_num_rows($result) > 0) {
                // Menampilkan card produk
                while ($row = mysqli_fetch_assoc($result)) {
                    echo "<div class='product-card'>"; // Card untuk produk
                    echo "
                    <div class='vendor-info'>
                        <div class='image'>
                        <img src='../dashboard_Vendor/" . $row['vendor_logo'] . "' alt='Logo Vendor' class='vendor-logo'>
                        </div>
                        <div class='infoVendor'>
                        <span class='vendor-name'>" . $row['vendor_name'] . "</span>
                        <span class='vendor-address'>" . $row['vendor_address'] . "</span>
                        </div>
                    </div>";
                    echo "
                    <div class='product-info'>
                        <img src='../dashboard_Vendor/" . $row['product_image'] . "' alt='Gambar Produk' class='product-image'>
                        <div class='product-details'>
                            <span class='product-name'>" . $row['product_name'] . "</span><br>
                            <span class='product-price'>Rp " . number_format($row['prices'], 0, ',', '.') . "</span>
                            <a href='../content_detail_product/?product_id=" . $row['id'] . "'>
                                <button class='detail-button'>Lihat Detail</button>
                            </a>
                        </div>
                    </div>";
                    echo "</div>"; // Tutup div product-card
                }
            } else {
                echo "<div class='tidakada-hasil'><div class='no-results'>Tidak ada hasil yang ditemukan untuk pencarian '$search_query'.</div></div>'";
            }
        } else {
            echo "<div class='tidakada-hasil'><div class='no-results'>Masukkan kata kunci pencarian.</div></div>";
        }
        mysqli_close($koneksi);

        ?>
        </div> <!-- Tutup div grid produk -->


    </div>

    <!-- Kolom filter kanan -->
    <div class="filter-column-2">
        <h3>Filter Tambahan</h3>
        <button>Filter Stok Tersedia</button><br><br>
        <button>Produk Diskon</button><br><br>
        <button>Produk Terbaru</button><br><br>
    </div>
</div>

<script class="script-theme">
        document.getElementById("theme-toggle").addEventListener("click", function () {
            document.body.classList.toggle("dark-theme");
            const icon = this.querySelector("i");
            if (document.body.classList.contains("dark-theme")) {
                icon.classList.remove("fa-circle-half-stroke");
                icon.classList.add("fa-sun");
            } else {
                icon.classList.remove("fa-sun");
                icon.classList.add("fa-circle-half-stroke");
            }
        });
    </script>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../js/dropdown.js"></script>
    <script src="../js/searchPopup.js"></script>
    <script src="../js/slider.js"></script>
    <script src="../js/chatfitur.js"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>

</body>
</html>
