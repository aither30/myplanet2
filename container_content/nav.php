<nav>
        <div class="left_nav">
            <div class="logo">
                <img src="../assets/attribute myplanet/Logo My PlanET.png" alt="My PlanET" />
                <a href="../index.php">My PlanET</a>
            </div>
        </div>
        <div class="mid_nav">
            <ul>
                <li><a href="../Tentang_kami/index.php">Tentang Kami</a></li>
                <li><a href="../cek_transaksi/index.php">Cek Transaksi</a></li>
                <li><a href="#" onclick="openSearchPopup()">Search</a></li>
            </ul>
            <div class="Dropdown2">
                <div class="bandingharga">
                    <button onclick="toggleDropdown()">Banding Harga</button>
                </div>
                <div class="Content-dropdown2" id="dropdownContent2">
                    <a href="../banding_harga_vendor/index.php">Banding Harga Vendor</a>
                    <a href="../banding_harga_product/index.php">Banding Harga Produk</a>
                </div>
            </div>
        </div>
        <div class="right_nav">
            <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
                <button id="openCartBtn"><i class="fa-solid fa-cart-shopping"></i></button>
            <button id="theme-toggle"><i class="fa-solid fa-circle-half-stroke"></i></button>
            <button id="openChatBtn" class="open-chat-btn"><i class="fa-solid fa-message"></i></button>
                <div class="Dropdown">
                    <div class="profil">
                        <button><?php echo $_SESSION['username']; ?></button>
                    </div>
                    <div class="Content-dropdown">
                    <?php if ($_SESSION['type_account'] === 'User'): ?>
                    <a href="../dashboard_user/index.php">Dashboard</a>
                <?php elseif ($_SESSION['type_account'] === 'Vendor'): ?>
                    <a href="../dashboard_Vendor/index.php">Dashboard</a>
                    <a href="../banner_ads/index.php">kelola Iklan</a>
                <?php endif; ?>
                        <a href="../system.message/index.php">Pesan</a>
                        <a href="../logout.php">Keluar</a>
                    </div>
                </div>
            <?php else: ?>
                <div class="masuk-daftar">
                    <a href="../login.php">Masuk</a>
                    <a href="../login.php">Daftar</a>
                </div>
            <?php endif; ?>
        </div>
    </nav>


    </div>

<!-- HTML untuk popup pencarian -->
<div class="search-overlay" id="searchOverlay">
    <div class="search-popup">
        <button class="close-btn" onclick="closeSearchPopup()">X</button>
        <h2>Search</h2>
        <!-- Form pencarian -->
        <form action="../search/file_search.php" method="GET" id="searchForm">
            <input type="text" name="search_query" placeholder="Masukkan kata kunci..." required>
            <button type="submit">Cari</button>
        </form>
    </div>
</div>

<div id="cart-popup" style="display: none;">
                <h3>Produk di Keranjang</h3>
                <div id="cart-items"></div>
                <button onclick="goToCartPage()">Lihat Keranjang</button>
                <script>function goToCartPage() {
                    window.location.href = '../content_detail_product/cart.page.php'; // Redirect ke halaman keranjang
}</script>
                <button class="close-btn" onclick="closeCartPopup()"><i class="fa-regular fa-circle-xmark"></i></button>
            </div>

            
    <div class="chat-popup" id="myChatPopup" style="display: none;">
        <div class="chat-header">
            <span id="chatHeaderTitle">Chat</span>
            <button id="closeChatListBtn" class="close-chat-btn"><i class="fa-regular fa-circle-xmark"></i></button>
            <button id="closeChatViewBtn" class="close-chat-btn" style="display: none;"><i class="fa-regular fa-circle-xmark"></i></button>
        </div>
        <div class="chat-body">
            <div id="chatList" class="chat-list">
                <div id="chatListContent"></div>
            </div>
            <div id="chatView" class="chat-view" style="display: none;">
                <div id="chatMessages"></div>
            </div>
        </div>
        <div class="chat-footer">
            <textarea id="chatInput" placeholder="Type a message..."></textarea>
            <button id="sendMessageBtn">Send</button>
        </div>
    </div>
    <script>
            function displayCartItems() {
        $.ajax({
            url: '../content_detail_product/get_cart_items.php',
            type: 'GET',
            success: function(response) {
                document.getElementById('cart-items').innerHTML = response;
                document.getElementById('cart-popup').style.display = 'block';
                isCartPopupOpen = true;
            }
        });
    }

    function closeCartPopup() {
        document.getElementById('cart-popup').style.display = 'none';
        isCartPopupOpen = false;
    }

    document.getElementById('openCartBtn').addEventListener('click', function() {
        displayCartItems();
    });
    </script>
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


<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="../js/dropdown.js"></script>
<script src="../js/slider.js"></script>
<script src="../js/chatfitur.js"></script>
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
        url: '../content_detail_product/add_to_cart.php', // Pastikan URL file PHP yang akan menangani penambahan produk ke cart
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
                    window.location.href = '../content_detail_product/cart.page.php';
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

<script>
function openSearchPopup() {
    document.getElementById('searchOverlay').style.display = 'flex'; // Menampilkan popup dengan display flex
}

function closeSearchPopup() {
    document.getElementById('searchOverlay').style.display = 'none'; // Menyembunyikan popup
}


</script>

<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.js"></script>
