<?php
// Koneksi ke database
include ("./config/config.php");

session_start(); // Memastikan session dimulai

// Cek apakah pengguna sudah login atau belum
$username = isset($_SESSION['username']) ? $_SESSION['username'] : null;
$loggedin = isset($_SESSION['loggedin']) ? $_SESSION['loggedin'] : false;
$type_account = isset($_SESSION['type_account']) ? $_SESSION['type_account'] : null;

// Jika username ada di session
if ($username) {
    // Debugging - menampilkan username dari session
    // Query untuk memeriksa apakah username ada di 'user_account'
    $query_user = "SELECT 1 FROM user_account WHERE username = ?";
    $stmt_user = $koneksi->prepare($query_user);
    $stmt_user->bind_param("s", $username);
    $stmt_user->execute();
    $stmt_user->store_result();

    // Jika username ada di tabel user_account
    if ($stmt_user->num_rows > 0) {
        $type_account = 'User';
        $_SESSION['type_account'] = 'User'; // Set session type_account untuk user
    } else {
        // Cek apakah username ada di 'business_account'
        $query_business = "SELECT 1 FROM business_account WHERE username = ?";
        $stmt_business = $koneksi->prepare($query_business);
        $stmt_business->bind_param("s", $username);
        $stmt_business->execute();
        $stmt_business->store_result();

        // Jika username ada di tabel business_account
        if ($stmt_business->num_rows > 0) {
            $type_account = 'Vendor';
            $_SESSION['type_account'] = 'Vendor'; // Set session type_account untuk vendor
        } else {
        }
        $stmt_business->close();
    }
    $stmt_user->close();
}



?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://stackpath.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="styles.css">
    <title>My PlanET</title>
</head>
<body>
<nav>
      <div class="left_nav">
      <div class="logo">
          <img
            src="./assets/attribute myplanet/Logo My PlanEt.png"
            alt="My PlanET"
          />
          <a href="./home.php">My PlanET</a>
        </div>
      </div>
      <div class="mid_nav">
        <ul>
            <li><a href="./Tentang_kami/index.php">Tentang Kami</a></li>
            <li><a href="./cek_transaksi/index.php">Cek Transaksi</a></li>
            <li><a href="#" onclick="openSearchPopup()">Search</a></li> <!-- Trigger Search Popup -->
        </ul>
        <div class="Dropdown2">
            <div class="bandingharga">
                <button onclick="toggleDropdown()">Banding Harga</button>
            </div>
            <div class="Content-dropdown2" id="dropdownContent2">
                <a href="./banding_harga_vendor/index.php">Banding Harga Vendor</a>
                <a href="./banding_harga_product/index.php">Banding Harga Produk</a>
            </div>
        </div>
    </div>
    <div class="right_nav">
    <?php 
    // Jika tipe akun berhasil ditemukan, atur session loggedin dan type_account
    if ($type_account !== null) {
        $_SESSION['loggedin'] = true;
    ?>            <button id="openCartBtn"><i class="fa-solid fa-cart-shopping"></i></button>
    <button id="theme-toggle"><i class="fa-solid fa-circle-half-stroke"></i></button>
    <button id="openChatBtn" class="open-chat-btn"><i class="fa-solid fa-message"></i></button>
        <div class="Dropdown">
            <div class="profil">
                <button><?php echo htmlspecialchars($username); ?></button>
            </div>
            <div class="Content-dropdown">
                <?php if ($type_account === 'User'): ?>
                    <a href="./dashboard_user/index.php">Dashboard</a>
                    <a href="./system.message/index.php">Pesan</a>
                    <a href="logout.php">Keluar</a>
                <?php elseif ($type_account === 'Vendor'): ?>
                    <a href="./dashboard_Vendor/index.php">Dashboard</a>
                    <a href="./banner_ads/index.php">Kelola Iklan</a>
                    <a href="./system.message/index.php">Pesan</a>
                    <a href="logout_home.php">Keluar</a>
                <?php endif; ?>
            </div>
        </div>
    <?php 
    } else {
        // Jika pengguna belum login, tampilkan tombol Masuk dan Daftar
        $_SESSION['loggedin'] = false;
    ?>
        <div class="masuk-daftar">
            <a href="login.php">Masuk</a>
            <a href="register.php">Daftar</a>
        </div>
    <?php } ?>
</div>

</nav>


    <div class="container">
      <div class="container2">
      <?php  
     include ('container_content/container2.slider1.php');
     ?>

        <?php  
     include ('container_content/container2.slider2.php');
     ?>

      </div>
      <div class="judul">
        <p>VENDOR</p>
      </div>
      
      <?php  
     include ('container_content/container.vendor.php');
     ?>

      <div class="judul">
        <p>PRODUK</p>
      </div>

      <?php  
     include ('./container_content/container.product.php');
     ?>




<!-- Popup Chat -->
<div class="chat-popup" id="myChatPopup" style="display: none;">
    <div class="chat-header">
        <span id="chatHeaderTitle">Chat</span>
        <button id="closeChatListBtn" class="close-chat-btn"><i class="fa-regular fa-circle-xmark"></i></button> <!-- Tombol untuk menutup popup -->
        <button id="closeChatViewBtn" class="close-chat-btn" style="display: none;"><i class="fa-regular fa-circle-xmark"></i></button> <!-- Tombol kembali ke list -->
    </div>
    <div class="chat-body">
        <!-- Daftar Chat -->
        <div id="chatList" class="chat-list">
            <div id="chatListContent"></div> <!-- Tempat untuk memuat daftar akun pengguna -->
        </div>
        
        <!-- Tampilan Percakapan -->
        <div id="chatView" class="chat-view" style="display: none;">
            <div id="chatMessages"></div> <!-- Pesan akan ditampilkan di sini -->
        </div>
    </div>
    <div class="chat-footer">
        <textarea id="chatInput" placeholder="Type a message..."></textarea>
        <button id="sendMessageBtn">Send</button>
    </div>
</div>

    </div>

<!-- HTML untuk popup pencarian -->
<div class="search-overlay" id="searchOverlay">
    <div class="search-popup">
        <button class="close-btn" onclick="closeSearchPopup()">X</button>
        <h2>Search</h2>
        <!-- Form pencarian -->
        <form action="./search/file_search.php" method="GET" id="searchForm">
            <input type="text" name="search_query" placeholder="Masukkan kata kunci..." required>
            <button type="submit">Cari</button>
        </form>
    </div>
</div>



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

    <script >
      document.getElementById("theme-toggle").addEventListener("click", function () {
  document.body.classList.toggle("dark-theme");

  // Ganti ikon
  const icon = this.querySelector("i");
  if (document.body.classList.contains("dark-theme")) {
    icon.classList.remove("fa-circle-half-stroke");
    icon.classList.add("fa-sun"); // Ganti dengan ikon matahari saat dark theme
  } else {
    icon.classList.remove("fa-sun");
    icon.classList.add("fa-circle-half-stroke"); // Ganti kembali ke ikon setengah lingkaran
  }
});
</script>

<script>
function openSearchPopup() {
    document.getElementById('searchOverlay').style.display = 'flex'; // Menampilkan popup dengan display flex
}

function closeSearchPopup() {
    document.getElementById('searchOverlay').style.display = 'none'; // Menyembunyikan popup
}


</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="./js/dropdown.js"></script>
<script src="./js/slider.js"></script>
<script src="./js/chatfitur-home.js"></script>
<script>
    function addToCartAndGoToCart(productId) {
    let quantity = 1; // Default quantity

    // Tampilkan loading saat produk ditambahkan
    Swal.fire({
        title: 'Menambahkan ke Keranjang...',
        text: 'Harap tunggu...',
        allowOutsideClick: false,
        showConfirmButton: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });

    // Kirim data ke server untuk menambahkan produk ke keranjang
    $.ajax({
        url: './content_detail_product/add_to_cart.php', // Pastikan URL file PHP yang akan menangani penambahan produk ke cart
        type: 'POST',
        data: {
            product_id: productId,
            quantity: quantity
        },
        success: function(response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                // Setelah produk berhasil ditambahkan, arahkan ke halaman keranjang setelah 1 detik
                setTimeout(function() {
                    Swal.close(); // Tutup loading
                    window.location.href = './content_detail_product/cart.page.php';
                }, 1000); // Delay selama 1 detik
            } else {
                Swal.fire({
                    title: 'Error!',
                    text: res.message,
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        },
        error: function() {
            Swal.fire({
                title: 'Error!',
                text: 'Gagal menambahkan produk ke keranjang',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}

</script>
<?php include ("./container_content/cart-chat-home.php")?>
</body>
</html>
