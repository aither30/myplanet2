<?php
session_start(); // Memulai session

include ("../config/config.php");

// Query untuk mengambil data vendor dari tabel business_account
$sql = "SELECT vendor_id, company_name, phone_vendor, email, logo, address, description 
        FROM business_account";
$result = $koneksi->query($sql);

$vendors = [];
if ($result->num_rows > 0) {
    // Masukkan hasil query ke dalam array
    while($row = $result->fetch_assoc()) {
        $vendors[] = $row;
    }
} else {
    echo "Tidak ada vendor ditemukan";
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
    <title>Bandingkan Vendor</title>
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
                <p id="vendor_count">0 Vendor yang dipilih</p>
            </div>
            <div class="show_vendor">
                <button id="toggleButtonVendor">
                    <i class="fa-solid fa-angle-down"></i>
                </button>
            </div>
        </div>
        <div class="detail_vendor_compare" id="detailVendorCompare" style="display: none;">
            <!-- Bagian Pencarian Vendor di dalam perbandingan -->
            <div class="search_container_compare">
                <input type="text" id="searchInputCompare" placeholder="Cari vendor untuk dibandingkan..." />
            </div>

            <div class="list_vendor" id="vendorList"></div>
        </div>
        <div class="increase_vendor_compare">
            <div class="compare_vendor">
                <button id="compareBtn" style="display: none;">Bandingkan</button>
            </div>
        </div>
    </div>
</div>

<!-- Daftar Vendor yang Dibandingkan akan Muncul Disini -->
<div class="content_container_compare_vendor" id="contentContainerCompareVendor"></div>
<script>
    const vendors = <?= json_encode($vendors) ?>;

    document.addEventListener("DOMContentLoaded", function () {
        let selectedVendors = JSON.parse(localStorage.getItem("selectedVendors")) || [];
        const maxVendors = 3; // Maksimal vendor yang bisa dipilih

        const vendorCountElement = document.getElementById("vendor_count");
        const vendorListElement = document.getElementById("vendorList");
        const compareButtonElement = document.getElementById("compareBtn");
        const contentContainerCompareVendor = document.getElementById("contentContainerCompareVendor");

        // Fungsi untuk memperbarui tampilan daftar vendor yang dipilih
        function updateSelectedVendors() {
            vendorListElement.innerHTML = "";
            contentContainerCompareVendor.innerHTML = ""; // Bersihkan vendor yang dibandingkan

            selectedVendors.forEach((vendor, index) => {
                vendorListElement.innerHTML += `
                    <div class="selected_vendor_item">
                        <p>${vendor.company_name}</p>
                        <button class="removeVendor" data-index="${index}">x</button>
                    </div>
                `;

                // Tampilkan card lengkap vendor yang dipilih di container perbandingan
                contentContainerCompareVendor.innerHTML += `
                    <div class="compare_vendor_card">
                        <img src="../dashboard_Vendor/${vendor.logo}" alt="${vendor.company_name}" />
                        <h3>${vendor.company_name}</h3>
                        <p>Telepon: ${vendor.phone_vendor}</p>
                        <p>Email: ${vendor.email}</p>
                        <p>Alamat: ${vendor.address}</p>
                        <p>Deskripsi: ${vendor.description}</p>
                    </div>
                `;
            });

            vendorCountElement.textContent = `${selectedVendors.length} Vendor yang dipilih`;

            // Simpan ke localStorage
            localStorage.setItem("selectedVendors", JSON.stringify(selectedVendors));

            // Tampilkan atau sembunyikan tombol "Bandingkan" berdasarkan apakah ada vendor yang dipilih
            if (selectedVendors.length > 0) {
                compareButtonElement.style.display = "block";
            } else {
                compareButtonElement.style.display = "none";
            }

            // Pasang event listener untuk tombol remove
            document.querySelectorAll('.removeVendor').forEach((button) => {
                button.addEventListener('click', function () {
                    const index = this.getAttribute("data-index");
                    selectedVendors.splice(index, 1);
                    updateSelectedVendors();
                });
            });
        }

        // Inisialisasi tampilan dari localStorage
        updateSelectedVendors();

        // Menangani tombol Bandingkan
        compareButtonElement.addEventListener("click", function () {
            if (selectedVendors.length > 0) {
                Swal.fire({
                    title: 'Vendor yang dibandingkan',
                    text: selectedVendors.map((v) => v.company_name).join(", "),
                    icon: 'info',
                    confirmButtonText: 'Lanjutkan'
                });
            } else {
                Swal.fire({
                    title: 'Perhatian!',
                    text: 'Pilih minimal satu vendor untuk dibandingkan.',
                    icon: 'warning',
                    confirmButtonText: 'Ok'
                });
            }
        });

        // Menangani pencarian vendor
        document.getElementById("searchInputCompare").addEventListener("input", function () {
            const searchQuery = this.value.toLowerCase();

            vendorListElement.innerHTML = ""; // Bersihkan hasil pencarian sebelumnya

            vendors.forEach(vendor => {
                if (vendor.company_name.toLowerCase().includes(searchQuery)) {
                    vendorListElement.innerHTML += `
                        <div class="vendor_card" data-vendor-id="${vendor.vendor_id}" data-vendor-name="${vendor.company_name}">
                            <p>${vendor.company_name}</p> 
                            <button class="compare_button">Bandingkan</button>
                        </div>
                    `;
                }
            });

            // Menangani klik pada tombol bandingkan di hasil pencarian
            document.querySelectorAll(".compare_button").forEach((button) => {
                button.addEventListener("click", function () {
                    const vendorCard = this.closest(".vendor_card");
                    const vendorId = vendorCard.getAttribute("data-vendor-id");
                    const vendorName = vendorCard.getAttribute("data-vendor-name");
                    
                    // Mencari vendor berdasarkan ID untuk mendapatkan data lengkapnya
                    const selectedVendor = vendors.find(vendor => vendor.vendor_id == vendorId);

                    // Cek apakah vendor sudah dipilih
                    const isAlreadySelected = selectedVendors.some(vendor => vendor.vendor_id === vendorId);
                    if (isAlreadySelected) {
                        Swal.fire({
                            title: 'Perhatian!',
                            text: 'Vendor ini sudah dipilih untuk dibandingkan.',
                            icon: 'warning',
                            confirmButtonText: 'Ok'
                        });
                        return;
                    }

                    if (selectedVendors.length < maxVendors) {
                        selectedVendors.push(selectedVendor);
                        updateSelectedVendors();
                        Swal.fire({
                            title: 'Berhasil!',
                            text: 'Vendor berhasil ditambahkan untuk dibandingkan.',
                            icon: 'success',
                            confirmButtonText: 'Lanjutkan'
                        });
                    } else {
                        Swal.fire({
                            title: 'Perhatian!',
                            text: 'Anda hanya dapat memilih maksimal 3 vendor.',
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

            // Toggle visibility of the vendor comparison section
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
    <script src="../js/chatfitur.js"></script>
</body>
</html>
