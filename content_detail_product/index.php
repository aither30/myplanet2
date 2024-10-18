<?php
session_start(); // Memulai session

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header('Location: ../login.php');
    exit;
}

include ("../config/config.php");

// Ambil data produk berdasarkan product_id dari URL
if (isset($_GET['product_id'])) {
    $product_id = (int) $_GET['product_id'];

    // Query untuk mengambil data produk, termasuk spesifikasi, nama vendor, dan logo
    $sql = "SELECT p.*, b.name AS vendor_name, b.logo AS vendor_logo, p.vendor_id
            FROM product p
            JOIN business_account b ON p.vendor_id = b.vendor_id
            WHERE p.product_id = ?";

    $stmt = $koneksi->prepare($sql);
    if ($stmt === false) {
        die("Error: " . $koneksi->error);
    }

    $stmt->bind_param("i", $product_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $product = $result->fetch_assoc();
    
    // Periksa apakah produk ditemukan
    if (!$product) {
        echo "Produk tidak ditemukan.";
        exit;
    }
    
    // Cek apakah vendor_id ada di product
    if (!isset($product['vendor_id'])) {
        die("vendor_id tidak ditemukan di product.");
    }
} else {
    echo "Produk tidak ditemukan.";
    exit;
}

// Ambil data vendor berdasarkan vendor_id sudah dilakukan dalam query di atas
$vendor_name = $product['vendor_name'];
$vendor_logo = $product['vendor_logo'];
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
<!-- Slick CSS -->
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick.min.css"/>
<link rel="stylesheet" type="text/css" href="https://cdnjs.cloudflare.com/ajax/libs/slick-carousel/1.8.1/slick-theme.min.css"/>
    <title>Detail Product - <?= htmlspecialchars($product['name']); ?></title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" />
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css">
    <link rel="stylesheet" href="../css/style.footer.css">
    <link rel="stylesheet" href="styles.css" />
</head>
<body>
<?php include ("../container_content/nav.php")?>
    <div class="foto-sampul-product">
        <img src="../dashboard_vendor/<?= htmlspecialchars($product['images']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" />
    </div>

    <div class="container" style="gap: 3vw;">
        <div class="containercontent">
        <div class="container-head">
    <div class="content-head">
        <div class="head-product">
            <div class="foto-product">
                <img src="../dashboard_vendor/<?= htmlspecialchars($product['images']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" />
                <img src="../dashboard_vendor/<?= htmlspecialchars($product['images']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" />
                <img src="../dashboard_vendor/<?= htmlspecialchars($product['images']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" />
            </div>
        </div>
    </div>

    <!-- Detail produk -->
    <div class="detail-vendor">
        <div class="nama-produk">
            <h3><?= htmlspecialchars($product['name']); ?></h3>
            <p><?= htmlspecialchars($product['description']); ?></p>
        </div>

        <div class="spesifikasi-produk">
    <h4>Spesifikasi Produk:</h4>
    <p>
        <?php
        if (!empty($product['specifications'])) {
            echo nl2br(htmlspecialchars($product['specifications']));
        } else {
            echo "Spesifikasi belum diisi.";
        }
        ?>
    </p>
</div>


    </div>

<!-- Card Vendor -->
<div class="cardVendor">
    <div class="vendor-image">
        <!-- Gambar Vendor -->
        <?php if (!empty($vendor_logo)): ?>
            <img src="../dashboard_Vendor/<?= htmlspecialchars($vendor_logo); ?>" alt="<?= htmlspecialchars($vendor_name); ?>" />
        <?php endif; ?>
    </div>
    <div class="vendor-info">
        <!-- Nama Vendor -->
        <h3><?= htmlspecialchars($vendor_name); ?></h3>
    </div>

    <!-- Button Kunjungi Vendor -->
    <div class="vendor-action">
        <a href="../content_detail_vendor_myplanet/index.php?vendor_id=<?= htmlspecialchars($product['vendor_id']); ?>" class="btn-visit-vendor">Kunjungi Vendor</a>
    </div>
</div>


</div>


            <div class="price-product">
    <div class="nama-product">
        <h3><?= htmlspecialchars($product['name']); ?></h3>
        <p>by <?= htmlspecialchars($product['vendor_name']); ?></p>
    </div>
    <div class="harga-product">
        <p>Harga</p>
        <h3 id="pricePerItem">Rp <?= number_format($product['prices'], 2); ?></h3>
        <p>Stok Tersedia: <span id="stockAvailable"><?= $product['stocks']; ?></span></p>
    </div>

    <div class="quantity-product">
        <button onclick="decreaseQuantity()">-</button>
        <input type="number" id="quantity" value="1" min="1" max="<?= $product['stocks']; ?>" readonly>
        <button onclick="increaseQuantity()">+</button>
    </div>

    <div class="buy-product">
        <button class="btn-add-to-cart" onclick="checkStockAndAddToCart()">Tambah ke Keranjang</button>
    </div>
</div>

            <div id="cart-popup" style="display: none;">
                <h3>Produk di Keranjang</h3>
                <div id="cart-items"></div>
                <button onclick="goToCartPage()">Lihat Keranjang</button>
                <script>function goToCartPage() {
                    window.location.href = 'cart.page.php'; // Redirect ke halaman keranjang
}</script>
                <button class="close-btn" onclick="closeCartPopup()"><i class="fa-regular fa-circle-xmark"></i></button>
            </div>
        </div>
         <?php include ("../container_content/container_product_slider.php"); ?>
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
    // Variabel untuk melacak status popup keranjang
    let isCartPopupOpen = false;

 // Ambil stok produk dari PHP
var stockAvailable = <?= $product['stocks']; ?>;

// Fungsi untuk memeriksa stok sebelum menambahkan ke keranjang
function checkStockAndAddToCart() {
    var quantity = parseInt(document.getElementById('quantity').value);

    if (quantity > stockAvailable) {
        // Jika jumlah yang diminta melebihi stok yang tersedia
        Swal.fire({
            title: 'Stok Tidak Cukup!',
            text: 'Anda meminta lebih dari stok yang tersedia. Stok saat ini hanya ' + stockAvailable + ' unit.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } else if (stockAvailable <= 0) {
        // Jika stok habis
        Swal.fire({
            title: 'Stok Habis!',
            text: 'Barang ini sudah tidak tersedia.',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    } else {
        // Jika stok mencukupi, lanjutkan proses
        addToCart();
    }
}

function addToCart() {
    var quantity = document.getElementById('quantity').value;
    var productId = <?= $product['product_id']; ?>;
    
    // Tampilkan loader sebelum request AJAX
    Swal.fire({
        title: 'Menambahkan ke Keranjang...',
        text: 'Harap tunggu...',
        showConfirmButton: false,
        allowOutsideClick: false,
        onBeforeOpen: () => {
            Swal.showLoading();
        }
    });

    // Tambahkan jeda 1 detik sebelum mengirim permintaan AJAX
    setTimeout(function() {
        $.ajax({
            url: 'add_to_cart.php',
            type: 'POST',
            data: {
                product_id: productId,
                quantity: quantity,
            },
            success: function(response) {
                var res = JSON.parse(response);
                Swal.close();  // Tutup loader setelah selesai

                if (res.status === 'success') {
                    // Perbarui popup keranjang setelah berhasil ditambahkan
                    loadCartItems();

                    Swal.fire({
                        title: 'Produk Ditambahkan!',
                        text: 'Produk berhasil ditambahkan ke keranjang.',
                        icon: 'success',
                        showConfirmButton: false,
                        timer: 2000
                    });

                    // Tampilkan popup keranjang
                    document.getElementById('cart-popup').style.display = 'block';
                } else {
                    Swal.fire({
                        title: 'Gagal!',
                        text: res.message,
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            },
            error: function(xhr, status, error) {
                Swal.close();
                Swal.fire({
                    title: 'Error!',
                    text: 'Terjadi masalah saat menambahkan ke keranjang. Coba lagi nanti.',
                    icon: 'error',
                    confirmButtonText: 'OK'
                });
            }
        });
    }, 1000); // Jeda 1 detik (1000 milidetik)
}


    function increaseQuantity() {
        let quantity = parseInt(document.getElementById('quantity').value);
        let stockAvailable = <?= $product['stocks']; ?>;
        if (quantity < stockAvailable) {
            quantity++;
            document.getElementById('quantity').value = quantity;
        } else {
            Swal.fire('Stok Limit', 'Jumlah tidak bisa melebihi stok tersedia', 'warning');
        }
    }

    function decreaseQuantity() {
        let quantity = parseInt(document.getElementById('quantity').value);
        if (quantity > 1) {
            quantity--;
            document.getElementById('quantity').value = quantity;
        } else {
            Swal.fire('Minimal Quantity', 'Jumlah tidak bisa kurang dari 1', 'warning');
        }
    }

    function displayCartItems() {
        $.ajax({
            url: 'get_cart_items.php',
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
    loadCartItems();  // Muat ulang item keranjang ketika popup dibuka
    document.getElementById('cart-popup').style.display = 'block';  // Tampilkan popup
});

    </script>



<script>
  $(document).ready(function(){
    $('.container_product').slick({
      slidesToShow: 3, // Jumlah card yang ditampilkan
      slidesToScroll: 1,
      autoplay: true,
      autoplaySpeed: 3000,
      dots: true, // Tampilkan dot navigasi di bawah slider
      arrows: true, // Tampilkan tombol panah navigasi
      prevArrow: '<button type="button" class="slick-prev">Previous</button>',
      nextArrow: '<button type="button" class="slick-next">Next</button>',
      responsive: [
        {
          breakpoint: 1024,
          settings: {
            slidesToShow: 2,
            slidesToScroll: 1
          }
        },
        {
          breakpoint: 600,
          settings: {
            slidesToShow: 1,
            slidesToScroll: 1
          }
        }
      ]
    });
  });
</script>


<script class="script-popup-cart">
    function loadCartItems() {
    $.ajax({
        url: 'get_cart_items.php',  // Backend untuk mengambil item keranjang
        success: function(response) {
            $('#cart-items').html(response);  // Masukkan data ke dalam elemen cart-items
        }
    });
}


    function updateQuantity(cart_id, change) {
    var quantityElement = document.getElementById('quantity-' + cart_id);
    var currentQuantity = parseInt(quantityElement.textContent);
    var newQuantity = currentQuantity + change;

    if (newQuantity > 0) {
        $.ajax({
            url: 'update_cart_quantity_popup.php',
            type: 'POST',
            data: {
                cart_id: cart_id,
                quantity: newQuantity
            },
            success: function(response) {
                var res = JSON.parse(response);
                if (res.status === 'success') {
                    quantityElement.textContent = newQuantity;  // Update quantity di halaman
                    loadCartItems();  // Refresh total dan item cart
                } else {
                    Swal.fire('Error', res.message, 'error');
                }
            },
            error: function() {
                Swal.fire('Error', 'Gagal memperbarui quantity. Coba lagi nanti.', 'error');
            }
        });
    } else {
        Swal.fire('Invalid Quantity', 'Quantity tidak bisa kurang dari 1', 'warning');
    }
}

function removeFromCart(cart_id) {
    // Tampilkan dialog konfirmasi dengan SweetAlert2
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Item ini akan dihapus dari keranjang Anda!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6',
        cancelButtonColor: '#d33',
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            // Jika pengguna mengonfirmasi, jalankan proses penghapusan
            $.ajax({
                url: 'remove_from_cart_popup.php',
                type: 'POST',
                data: { cart_id: cart_id },
                success: function(response) {
                    var res = JSON.parse(response);
                    if (res.status === 'success') {
                        Swal.fire(
                            'Dihapus!',
                            'Item telah dihapus dari keranjang.',
                            'success'
                        );
                        loadCartItems();  // Refresh item cart
                    } else {
                        Swal.fire('Error', res.message, 'error');
                    }
                },
                error: function() {
                    Swal.fire('Error', 'Gagal menghapus item dari keranjang. Coba lagi nanti.', 'error');
                }
            });
        }
    });
}



</script>
<?php include ("../container_content/footer.php")?>

</body>
</html>
