<?php

session_start(); // Memulai session

include ("../config/config.php");

// Query untuk mengambil data produk dari tabel product
$sql = "SELECT product_id, name, prices, spesifikasi, images FROM product";
$result = $koneksi->query($sql);

$products = [];
if ($result->num_rows > 0) {
    // Masukkan hasil query ke dalam array
    while($row = $result->fetch_assoc()) {
        $products[] = $row;
    }
} else {
    echo "Tidak ada produk ditemukan";
}
$koneksi->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <title>Bandingkan Produk</title>
    <link rel="stylesheet" href="styles.css">
    <!-- SweetAlert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
</head>
<body>
<?php include ("../container_content/nav.php")?>
<div class="foot_container_compare_vendor">
    <div class="foot_compare_vendor" id="footCompareVendor">
        <div class="desk_foot_compare_vendor">
            <div class="vs_vendor">
                <p>VS</p>
            </div>
            <div class="cost_vendor">
                <p id="product_count">0 Produk yang dipilih</p>
            </div>
            <div class="show_vendor">
                <button id="toggleButtonVendor">
                    <i class="fa-solid fa-angle-down"></i>
                </button>
            </div>
        </div>
        <div class="detail_vendor_compare" id="detailVendorCompare" style="display: none;">
            <!-- Bagian Pencarian Produk di dalam perbandingan -->
            <div class="search_container_compare">
                <input type="text" id="searchInputCompare" placeholder="Cari produk untuk dibandingkan..." />
            </div>

            <div class="list_vendor" id="productList"></div>
        </div>
        <div class="increase_vendor_compare">
            <div class="compare_vendor">
                <button id="compareBtn" style="display: none;">Bandingkan</button>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Produk yang Dibandingkan akan Muncul Disini -->
<div class="content_container_compare_vendor" id="contentContainerCompareVendor"></div>
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script>
        const products = <?= json_encode($products) ?>;

        document.addEventListener("DOMContentLoaded", function () {
            let selectedProducts = JSON.parse(localStorage.getItem("selectedProducts")) || [];
            const maxProducts = 3; // Maksimal produk yang bisa dipilih

            const productCountElement = document.getElementById("product_count");
            const productListElement = document.getElementById("productList");
            const compareButtonElement = document.getElementById("compareBtn");
            const contentContainerCompareVendor = document.getElementById("contentContainerCompareVendor");

            // Fungsi untuk memperbarui tampilan daftar produk yang dipilih
            function updateSelectedProducts() {
                productListElement.innerHTML = "";
                contentContainerCompareVendor.innerHTML = ""; // Bersihkan produk yang dibandingkan

                selectedProducts.forEach((product, index) => {
                    productListElement.innerHTML += `
                        <div class="selected_product_item">
                            <p>${product.name}</p>
                            <button class="removeProduct" data-index="${index}">x</button>
                        </div>
                    `;

                    // Tampilkan card lengkap produk yang dipilih di container perbandingan
                    contentContainerCompareVendor.innerHTML += `
                        <div class="compare_product_card">
                            <img src="../dashboard_Vendor/${product.image}" alt="${product.name}" />
                            <h3>${product.name}</h3>
                            <p>Harga: Rp ${parseInt(product.price).toLocaleString()}</p>
                            <p>Spesifikasi: ${product.spesifikasi}</p>
                        </div>
                    `;
                });

                productCountElement.textContent = `${selectedProducts.length} Produk yang dipilih`;

                // Simpan ke localStorage
                localStorage.setItem("selectedProducts", JSON.stringify(selectedProducts));

                // Tampilkan atau sembunyikan tombol "Bandingkan" berdasarkan apakah ada produk yang dipilih
                if (selectedProducts.length > 0) {
                    compareButtonElement.style.display = "block";
                } else {
                    compareButtonElement.style.display = "none";
                }

                // Pasang event listener untuk tombol remove
                document.querySelectorAll('.removeProduct').forEach((button) => {
                    button.addEventListener('click', function () {
                        const index = this.getAttribute("data-index");
                        selectedProducts.splice(index, 1);
                        updateSelectedProducts();
                    });
                });
            }

            // Inisialisasi tampilan dari localStorage
            updateSelectedProducts();

            // Menangani tombol Bandingkan
            compareButtonElement.addEventListener("click", function () {
                if (selectedProducts.length > 0) {
                    Swal.fire({
                        title: 'Produk yang dibandingkan',
                        text: selectedProducts.map((p) => p.name).join(", "),
                        icon: 'info',
                        confirmButtonText: 'Lanjutkan'
                    });
                } else {
                    Swal.fire({
                        title: 'Perhatian!',
                        text: 'Pilih minimal satu produk untuk dibandingkan.',
                        icon: 'warning',
                        confirmButtonText: 'Ok'
                    });
                }
            });

            // Menangani pencarian produk
            document.getElementById("searchInputCompare").addEventListener("input", function () {
                const searchQuery = this.value.toLowerCase();

                productListElement.innerHTML = ""; // Bersihkan hasil pencarian sebelumnya

                products.forEach(product => {
                    if (product.name.toLowerCase().includes(searchQuery)) {
                        productListElement.innerHTML += `
                            <div class="product_card" data-product-id="${product.product_id}" data-product-name="${product.name}" data-product-price="${product.prices}" data-product-spesifikasi="${product.spesifikasi}" data-product-image="${product.images}">
                                <p class="name_product" >${product.name}</p>
                                <p class="price_product" >Harga: Rp ${parseInt(product.prices).toLocaleString()}</p>
                                <button class="compare_button">Bandingkan</button>
                            </div>
                        `;
                    }
                });

                // Menangani klik pada tombol bandingkan di hasil pencarian
                document.querySelectorAll(".compare_button").forEach((button) => {
                    button.addEventListener("click", function () {
                        const productCard = this.closest(".product_card");
                        const productId = productCard.getAttribute("data-product-id");
                        const productName = productCard.getAttribute("data-product-name");
                        const productPrice = productCard.getAttribute("data-product-price");
                        const productSpesifikasi = productCard.getAttribute("data-product-spesifikasi");
                        const productImage = productCard.getAttribute("data-product-image");

                        // Cek apakah produk sudah dipilih
                        const isAlreadySelected = selectedProducts.some(product => product.id === productId);
                        if (isAlreadySelected) {
                            Swal.fire({
                                title: 'Perhatian!',
                                text: 'Produk ini sudah dipilih untuk dibandingkan.',
                                icon: 'warning',
                                confirmButtonText: 'Ok'
                            });
                            return;
                        }

                        if (selectedProducts.length < maxProducts) {
                            selectedProducts.push({
                                id: productId,
                                name: productName,
                                price: productPrice,
                                spesifikasi: productSpesifikasi,
                                image: productImage,
                            });
                            updateSelectedProducts();
                            Swal.fire({
                                title: 'Berhasil!',
                                text: 'Produk berhasil ditambahkan untuk dibandingkan.',
                                icon: 'success',
                                confirmButtonText: 'Lanjutkan'
                            });
                        } else {
                            Swal.fire({
                                title: 'Perhatian!',
                                text: 'Anda hanya dapat memilih maksimal 3 produk.',
                                icon: 'warning',
                                confirmButtonText: 'Ok'
                            });
                        }
                    });
                });
            });

            // Toggle vendor comparison details
            document.getElementById("toggleButtonVendor").addEventListener("click", function () {
                var detailVendorCompare = document.querySelector("#detailVendorCompare");

                // Toggle visibility of the product comparison section
                if (detailVendorCompare.style.display === "none" || detailVendorCompare.style.display === "") {
                    detailVendorCompare.style.display = "block";
                } else {
                    detailVendorCompare.style.display = "none";
                }

                // Animasi rotasi pada tombol
                this.querySelector('i').classList.toggle('rotated');
            });
        });
    </script>
    <footer>
      <div class="content-footer">
        <div class="container-footer">
          <div class="desksingkatmyplanet">
            <div class="logo">
              <img
                src="./assets/attribute myplanet/Logo My PlanEt.png"
                alt="My PlanET"
              />
              <h3>My PlanET</h3>
            </div>
            <p>
              My PlanEt adalah platform perencana acara yang menggabungkan
              teknologi dan kreativitas untuk menyelenggarakan acara yang tak
              terlupakan.
            </p>
          </div>
          <div class="sitemap">
            <h3>Situs Map</h3>
            <ul>
              <li>Cek Transaksi</li>
              <li>Banding Harga</li>
              <li>Dashboard</li>
              <li>Tentang Kami</li>
            </ul>
          </div>
          <div class="sosmed">
            <h3>Sosial Media</h3>
            <ul>
              <li>Tiktok</li>
              <li>Instagram</li>
              <li>Facebook</li>
            </ul>
          </div>
        </div>
      </div>
      <div class="copyright">
        <p>©℗ 2024 My Planet. All rights reserved.</p>
      </div>
    </footer>

    <?php include ("../container_content/cart-chat.php") ?>

</body>
</html>
