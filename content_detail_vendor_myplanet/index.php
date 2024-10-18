<?php
session_start();
include("../config/config.php");

// Pastikan pengguna sudah login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Ambil vendor_id dari URL
if (isset($_GET['vendor_id'])) {
    $vendor_id = (int) $_GET['vendor_id'];
} else {
    echo "Vendor tidak ditemukan.";
    exit();
}

// Ambil data vendor berdasarkan vendor_id
$sql = "SELECT * FROM business_account WHERE vendor_id = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("i", $vendor_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $vendor = $result->fetch_assoc();
} else {
    echo "Vendor tidak ditemukan.";
    exit();
}

// Ambil data produk terkait vendor
$product_sql = "SELECT * FROM product WHERE vendor_id = ?";
$product_stmt = $koneksi->prepare($product_sql);
$product_stmt->bind_param("i", $vendor_id);
$product_stmt->execute();
$product_result = $product_stmt->get_result();

// Ambil data portofolio terkait vendor
$portofolioQuery = "SELECT * FROM portofolio WHERE vendor_id = ?";
$portofolio_stmt = $koneksi->prepare($portofolioQuery);
$portofolio_stmt->bind_param("i", $vendor_id);
$portofolio_stmt->execute();
$portofolio_result = $portofolio_stmt->get_result();


function getMetaTags($url) {
  // Cek apakah URL kosong
  if (empty($url)) {
      return [];  // Kembalikan array kosong jika URL kosong
  }

  // Buat opsi untuk stream context dengan User-Agent
  $options = [
      'http' => [
          'header' => "User-Agent: PHP\r\n"
      ]
  ];
  $context = stream_context_create($options);

  // Ambil konten HTML dari URL, pastikan file_get_contents hanya dijalankan jika URL valid
  $html = @file_get_contents($url, false, $context);
  
  // Jika gagal mendapatkan konten dari URL, kembalikan array kosong
  if ($html === false) {
      return [];
  }

  // Parse HTML menggunakan DOMDocument
  $doc = new DOMDocument();

  // Menonaktifkan error/warning HTML parsing
  libxml_use_internal_errors(true);
  $doc->loadHTML($html);
  libxml_clear_errors();

  $tags = [];

  // Ambil meta tag og:image (thumbnail)
  foreach ($doc->getElementsByTagName('meta') as $meta) {
      if ($meta->hasAttribute('property')) {
          $property = $meta->getAttribute('property');
          if ($property == 'og:image') {
              $tags['image'] = $meta->getAttribute('content');
          }
          if ($property == 'og:title') {
              $tags['title'] = $meta->getAttribute('content');
          }
          if ($property == 'og:description') {
              $tags['description'] = $meta->getAttribute('content');
          }
      }
  }

  return $tags;
}


// Ambil data testimoni terkait vendor
$testimoniQuery = "SELECT * FROM testimoni WHERE vendor_id = ?";
$testimoni_stmt = $koneksi->prepare($testimoniQuery);
$testimoni_stmt->bind_param("i", $vendor_id);
$testimoni_stmt->execute();
$testimoniResult = $testimoni_stmt->get_result();

// Ambil data FAQ terkait vendor
$faqQuery = "SELECT * FROM faq WHERE vendor_id = ?";
$faq_stmt = $koneksi->prepare($faqQuery);
$faq_stmt->bind_param("i", $vendor_id);
$faq_stmt->execute();
$faqResult = $faq_stmt->get_result();
?>


<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8" />
    <link
      rel="stylesheet"
      href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css"
      integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg=="
      crossorigin="anonymous"
      referrerpolicy="no-referrer"
    />
    <link rel="stylesheet" href="styles.css" />
    <title>Detail vendor</title>
  </head>
  <body>
  <nav>
        <div class="left_nav">
            <div class="logo">
                <img src="./assets/attribute myplanet/Logo My PlanET.png" alt="My PlanET" />
                <a href="../home.php">My PlanET</a>
            </div>
        </div>
        <div class="mid_nav">
            <ul>
                <li><a href="#">Tentang Kami</a></li>
                <li><a href="./cek_transaksi/index.php">Cek Transaksi</a></li>
                <li><a href="#" onclick="openSearchPopup()">Search</a></li>
            </ul>
            <div class="Dropdown2">
                <div class="bandingharga">
                    <button onclick="toggleDropdown()">Banding Harga</button>
                </div>
                <div class="Content-dropdown2" id="dropdownContent2">
                    <a href="./banding_harga_vendor/index.html">Banding Harga Vendor</a>
                    <a href="./banding_harga_product/index.html">Banding Harga Produk</a>
                </div>
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
                    <?php if ($_SESSION['type_account'] === 'User'): ?>
                    <a href="../dashboard_user/index.php">Dashboard</a>
                <?php elseif ($_SESSION['type_account'] === 'Vendor'): ?>
                    <a href="../dashboard_Vendor/index.php">Dashboard</a>
                <?php endif; ?>
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
    <div class="container-detail-vendor">
<!-- Vendor Section -->
<div class="container-head">
    <div class="foto-sampul-vendor">
        <img src="../dashboard_Vendor/<?php echo htmlspecialchars($vendor['logo']); ?>" alt="Sampul Vendor" />
    </div>
    <div class="content-head">
        <div class="profil-vendor">
            <img src="../dashboard_Vendor/<?php echo htmlspecialchars($vendor['logo']); ?>" alt="Logo Vendor" />
        </div>
        <div class="judul-contact">
            <div class="judul">
                <h2><?php echo htmlspecialchars($vendor['company_name']); ?></h2>
            </div>
            <div class="contact">
                <p>Alamat: <?php echo htmlspecialchars($vendor['address']); ?></p>
                <p>Telepon: <?php echo htmlspecialchars($vendor['phone_vendor']); ?></p>
                <p>Email: <?php echo htmlspecialchars($vendor['email']); ?></p>
            </div>
        </div>
    </div>
</div>

      <div class="container-content">
        <div class="content-tentang-kami-vendor">
        <div class="judul-content" id="judul-tentang-kami">
            <h3 id="tentang_kami">Tentang Kami</h3>
          </div>
          <div class="desk-vendor">
            <div class="foto-vendor">
              <!-- Gambar vendor, gunakan logo atau gambar dari database -->
              <img src="../dashboard_Vendor/<?php echo $vendor['logo']; ?>" alt="Logo Vendor" />
            </div>
            <div class="desk-singkat-vendor">
              <div class="desk-1">
                <!-- Deskripsi singkat tentang vendor, bisa diambil dari database -->
                <p><?php echo $vendor['description'] ?? 'Deskripsi belum tersedia.'; ?></p>
              </div>
              <div class="desk-2">
                <!-- Informasi kontak dari vendor -->
                <p>Alamat: <?php echo $vendor['address']; ?></p>
                <p>Telepon: <?php echo $vendor['phone_vendor']; ?></p>
                <p>Email: <?php echo $vendor['email']; ?></p>
              </div>
              <div class="desk-sosmed">
                <!-- Sosial media link, bisa disesuaikan dengan database -->
                <a href="#"><i class="fa-brands fa-facebook"></i></a>
                <a href="#"><i class="fa-brands fa-instagram"></i></a>
                <a href="#"><i class="fa-brands fa-tiktok"></i></a>
              </div>
            </div>
          </div>

        </div>


   
<!-- Produk Vendor -->
<div id="produk_kami" class="content">
    <div class="judul-content">
        <h3>Produk Kami</h3>
    </div>
    <div class="container_product">
        <?php while ($product = $product_result->fetch_assoc()): ?>
        <div class="product-card">
            <div class="product-image">
                <img src="../dashboard_Vendor/<?php echo htmlspecialchars($product['images']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" />
            </div>
            <div class="product-details">
                <h3><?php echo htmlspecialchars($product['name']); ?></h3>
                <p>Rp <?php echo number_format($product['prices'], 2, ',', '.'); ?></p>
            </div>
            <div class="product-actions">
                <a href="../content_detail_product/index.php?product_id=<?php echo $product['product_id']; ?>" class="btn-add-to-cart">Lihat Detail</a>
                <a href="javascript:void(0)" class="btn-buy-now" onclick="addToCartAndGoToCart(<?php echo $product['product_id']; ?>)">Beli Sekarang</a>
            </div>
        </div>
        <?php endwhile; ?>
    </div>
</div>


<script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>

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

    // Kirim data ke server dan cek apakah produk sudah ada di keranjang
    $.ajax({
        url: '../content_detail_product/check_cart.php', // Pastikan URL sesuai
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



<div class="content">
<div class="content-portofolio">
    <div class="judul-content">
        <h3 id="portofolio">Portofolio</h3>
    </div>
    <div class="container-portofolio">
        <?php if ($portofolio_result && $portofolio_result->num_rows > 0): ?>
            <?php while ($portofolio = $portofolio_result->fetch_assoc()): ?>
                <?php 
                $metaTags = getMetaTags($portofolio['link_portofolio']); 
                ?>
                <div class="card-portofolio">
                    <?php if (isset($metaTags['image'])): ?>
                        <div class="foto-portofolio">
                            <img src="<?php echo $metaTags['image']; ?>" alt="Preview Image" />
                        </div>
                    <?php endif; ?>
                    <div class="desk-portofolio">
                        <?php if (isset($metaTags['title'])): ?>
                            <h4><?php echo $metaTags['title']; ?></h4>
                        <?php endif; ?>
                        <?php if (isset($metaTags['description'])): ?>
                            <p><?php echo $metaTags['description']; ?></p>
                        <?php endif; ?>
                        <a href="<?php echo $portofolio['link_portofolio']; ?>" target="_blank">Kunjungi Portofolio</a>
                    </div>
                </div>
            <?php endwhile; ?>
        <?php else: ?>
            <p>Portofolio belum tersedia.</p>
        <?php endif; ?>
    </div>
</div>


</div>

            <div class="content">
              <div class="judul-content">
                <h3>Frequently Asked Questions (FAQ)</h3>
              </div>
              <div class="content-faq">
  <div class="faq-container">
    <?php if ($faqResult && $faqResult->num_rows > 0): ?>
      <?php while ($faq = $faqResult->fetch_assoc()): ?>
        <div class="faq-item">
          <h3 class="faq-question">
            <?php echo $faq['question']; ?>
          </h3>
          <p class="faq-answer">
            <?php echo $faq['answer']; ?>
          </p>
        </div>
      <?php endwhile; ?>
    <?php else: ?>
      <p>FAQ belum tersedia.</p>
    <?php endif; ?>
  </div>
</div>

            </div>


      </div>
    </div>


    
    <!-- Popup Chat -->
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

    <?php include ("../container_content/cart-chat.php")?>

    </div>
<?php include ("../container_content/footer.php")?>


    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script src="https://ajax.googleapis.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
    <script src="../js/dropdown.js"></script>
    <script src="../js/searchPopup.js"></script>
    <script src="../js/slider.js"></script>
    <script class="script-theme">
    document.getElementById("theme-toggle").addEventListener("click", function () {
    document.body.classList.toggle("dark-theme");

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
    <script src="../js/chatfitur.js"></script>
  </body>
</html>
