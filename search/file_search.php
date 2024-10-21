<?php
session_start();

// Tangkap semua output error dan tampung di buffer
ob_start();

// Menangani semua error PHP agar tidak tampil di web
function customErrorHandler($errno, $errstr, $errfile, $errline) {
    // Simpan error ke dalam buffer dan tampilkan ke console browser
    echo "<script>console.error('PHP Error: [$errno] $errstr in $errfile on line $errline');</script>";
    return true; // Supaya PHP tidak menampilkan error di layar
}

// Mengatur handler untuk error
set_error_handler("customErrorHandler");

// Fungsi untuk menangani exception
function customExceptionHandler($exception) {
    // Simpan exception ke dalam buffer dan tampilkan ke console browser
    echo "<script>console.error('PHP Exception: {$exception->getMessage()} in {$exception->getFile()} on line {$exception->getLine()}');</script>";
}

// Mengatur handler untuk exception
set_exception_handler("customExceptionHandler");

// Matikan laporan error PHP di layar
ini_set('display_errors', 0);
error_reporting(E_ALL);

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

include ("../config/config.php");

// Ambil nilai filter dari URL jika ada
$search_query = isset($_GET['search_query']) ? $_GET['search_query'] : '';
$filter_price_min = isset($_GET['filter_price_min']) ? $_GET['filter_price_min'] : '';
$filter_price_max = isset($_GET['filter_price_max']) ? $_GET['filter_price_max'] : '';
$filter_stock = isset($_GET['filter_stock']) ? $_GET['filter_stock'] : '';
$filter_discount = isset($_GET['filter_discount']) ? $_GET['filter_discount'] : '';
$filter_newest = isset($_GET['filter_newest']) ? $_GET['filter_newest'] : '';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hasil Pencarian</title>
    <!-- Link CSS dan Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link rel="stylesheet" href="styles.search.1.css">
    <link rel="stylesheet" href="../css/style.nav.css">
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
        <div class="search-form-container">
            <form action="file_search.php" method="GET">
                <input type="text" name="search_query" placeholder="Masukkan kata kunci..." value="<?php echo htmlspecialchars($search_query); ?>" required>
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
            <input type="hidden" name="search_query" value="<?php echo htmlspecialchars($search_query); ?>">

            <!-- Filter Harga -->
            <label for="filter_price_min">Harga Minimum:</label>
            <input type="number" name="filter_price_min" id="filter_price_min" placeholder="Harga Minimum" value="<?php echo htmlspecialchars($filter_price_min); ?>"><br>

            <label for="filter_price_max">Harga Maksimum:</label>
            <input type="number" name="filter_price_max" id="filter_price_max" placeholder="Harga Maksimum" value="<?php echo htmlspecialchars($filter_price_max); ?>"><br>

            <!-- Filter Stok Tersedia -->
            <label for="filter_stock">Stok Tersedia:</label>
            <input type="checkbox" name="filter_stock" id="filter_stock" value="1" <?php if ($filter_stock) echo 'checked'; ?>><br>

            <!-- Filter Produk Diskon -->
            <label for="filter_discount">Produk Diskon:</label>
            <input type="checkbox" name="filter_discount" id="filter_discount" value="1" <?php if ($filter_discount) echo 'checked'; ?>><br>

            <!-- Filter Produk Terbaru -->
            <label for="filter_newest">Produk Terbaru:</label>
            <input type="checkbox" name="filter_newest" id="filter_newest" value="1" <?php if ($filter_newest) echo 'checked'; ?>><br>

            <div class="button">
                <button type="submit">Terapkan Filter</button>
                <button type="reset" onclick="window.location.href='file_search.php'">Reset Filter</button>
            </div>
        </form>
    </div>

    <!-- Kolom hasil pencarian -->
    <div class="result-column">
        <div class="result-grid">
        <?php
        if (!empty($search_query)) {
            // Pertama, tampilkan card vendor
            $sql_vendor = "
                SELECT b.vendor_id AS id, b.company_name AS vendor_name, b.logo AS vendor_logo, b.address AS vendor_address
                FROM business_account b
                WHERE b.company_name LIKE '%$search_query%'
                   OR b.name_owner LIKE '%$search_query%'
                   OR b.email LIKE '%$search_query%'
                   OR b.phone_vendor LIKE '%$search_query%'
            ";

            $result_vendor = mysqli_query($koneksi, $sql_vendor);

            if (mysqli_num_rows($result_vendor) > 0) {
                // Menampilkan card vendor
                while ($row_vendor = mysqli_fetch_assoc($result_vendor)) {
                    echo "<div class='vendor-card'>"; // Card untuk vendor
                    echo "
                        <div class='vendor-info2'>
                            <div class='image2'>
                            <img src='../dashboard_Vendor/" . htmlspecialchars($row_vendor['vendor_logo']) . "' alt='Logo Vendor' class='vendor-logo2'>
                            </div>
                            <div class='infoVendor2'>
                            <span class='vendor-name2'>" . htmlspecialchars($row_vendor['vendor_name']) . "</span>
                            <span class='vendor-address2'>" . htmlspecialchars($row_vendor['vendor_address']) . "</span>
                            <a href='../content_detail_vendor/?vendor_id=" . htmlspecialchars($row_vendor['id']) . "'>
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

            // Kemudian, tampilkan card produk
            // Membuat filter dinamis berdasarkan input pengguna
            $product_filter = '';
            if (!empty($filter_price_min)) {
                $product_filter .= " AND p.prices >= $filter_price_min";
            }
            if (!empty($filter_price_max)) {
                $product_filter .= " AND p.prices <= $filter_price_max";
            }
            if (!empty($filter_stock)) {
                $product_filter .= " AND p.stock > 0";
            }
            if (!empty($filter_discount)) {
                $product_filter .= " AND p.discount > 0";
            }
            $order_by = '';
            if (!empty($filter_newest)) {
                $order_by = " ORDER BY p.created_at DESC";
            }

            // Query produk dengan filter
            $sql_product = "
                SELECT p.product_id AS id, p.name AS product_name, p.prices, b.company_name AS vendor_name, 
                       b.logo AS vendor_logo, p.images AS product_image, b.address AS vendor_address
                FROM product p
                JOIN business_account b ON p.vendor_id = b.vendor_id
                WHERE (p.name LIKE '%$search_query%' OR p.description LIKE '%$search_query%') $product_filter $order_by
            ";

            $result_product = mysqli_query($koneksi, $sql_product);

            // Menampilkan hasil pencarian produk
            if (mysqli_num_rows($result_product) > 0) {
                while ($row_product = mysqli_fetch_assoc($result_product)) {
                    echo "<div class='product-card'>";
                    echo "
                    <div class='vendor-info'>
                        <img src='../dashboard_Vendor/" . htmlspecialchars($row_product['vendor_logo']) . "' alt='Logo Vendor' class='vendor-logo'>
                        <div class='infoVendor'>
                            <span class='vendor-name'>" . htmlspecialchars($row_product['vendor_name']) . "</span>
                            <span class='vendor-address'>" . htmlspecialchars($row_product['vendor_address']) . "</span>
                        </div>
                    </div>";
                    echo "
                    <div class='product-info'>
                        <img src='../dashboard_Vendor/" . htmlspecialchars($row_product['product_image']) . "' alt='Gambar Produk' class='product-image'>
                        <div class='product-details'>
                            <span class='product-name'>" . htmlspecialchars($row_product['product_name']) . "</span><br>
                            <span class='product-price'>Rp " . number_format($row_product['prices'], 0, ',', '.') . "</span>
                            <a href='../content_detail_product/?product_id=" . htmlspecialchars($row_product['id']) . "'>
                                <button class='detail-button'>Lihat Detail</button>
                            </a>
                        </div>
                    </div>";
                    echo "</div>"; // Tutup div product-card
                }
            } else {
                echo "<div class='tidakada-hasil'><div class='no-results'>Tidak ada hasil yang ditemukan untuk pencarian '$search_query'.</div></div>";
            }
        } else {
            echo "<div class='tidakada-hasil'><div class='no-results'>Masukkan kata kunci pencarian.</div></div>";
        }
        mysqli_close($koneksi);
        ?>
        </div>
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
