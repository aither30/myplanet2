<?php 
session_start();

include ("../config/config.php");


// Ambil username dari session
$username = $_SESSION['username'];

// Query untuk mengambil item keranjang beserta informasi vendor berdasarkan username
$sql = "SELECT c.cart_id, c.quantity, p.name, p.prices, p.stocks, p.images, b.company_name, b.logo
        FROM cart c
        JOIN product p ON c.product_id = p.product_id
        JOIN business_account b ON p.vendor_id = b.vendor_id
        WHERE c.username = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

// Query untuk mengambil informasi user dari tabel user_account
$sql_user = "SELECT name, phone, address, email FROM user_account WHERE username = ?";
$stmt_user = $koneksi->prepare($sql_user);
$stmt_user->bind_param("s", $username);
$stmt_user->execute();
$user_info = $stmt_user->get_result()->fetch_assoc();

// Cek apakah data user tersedia di user_account
if ($user_info) {
    // Jika ada di user_account, simpan informasi ke dalam session
    $_SESSION['user_name'] = !empty($user_info['name']) ? $user_info['name'] : null;
    $_SESSION['user_email'] = !empty($user_info['email']) ? $user_info['email'] : null;
    $_SESSION['user_phone'] = !empty($user_info['phone']) ? $user_info['phone'] : null;
    $_SESSION['user_address'] = !empty($user_info['address']) ? $user_info['address'] : null;
} else {
    // Jika tidak ditemukan di user_account, cek di business_account
    $sql_business = "SELECT company_name AS name, address, email FROM business_account WHERE username = ?";
    $stmt_business = $koneksi->prepare($sql_business);
    $stmt_business->bind_param("s", $username);
    $stmt_business->execute();
    $business_info = $stmt_business->get_result()->fetch_assoc();

    if ($business_info) {
        // Jika ada di business_account, simpan informasi ke dalam session
        $_SESSION['user_name'] = !empty($business_info['name']) ? $business_info['name'] : null;
        $_SESSION['user_email'] = !empty($business_info['email']) ? $business_info['email'] : null;
        $_SESSION['user_phone'] = null; // Tidak ada kolom phone di business_account, jadi kosongkan
        $_SESSION['user_address'] = !empty($business_info['address']) ? $business_info['address'] : null;
    } else {
        // Jika tidak ditemukan di kedua tabel
        echo "Informasi pengguna tidak ditemukan.";
        exit;
    }
}

// Membuat array untuk menyimpan informasi user
$user_info = [];

// Memasukkan data user ke dalam array jika tersedia
if (!empty($_SESSION['user_name'])) {
    $user_info['name'] = $_SESSION['user_name'];
}

if (!empty($_SESSION['user_email'])) {
    $user_info['email'] = $_SESSION['user_email'];
}

if (!empty($_SESSION['user_phone'])) {
    $user_info['phone'] = $_SESSION['user_phone'];
}

if (!empty($_SESSION['user_address'])) {
    $user_info['address'] = $_SESSION['user_address'];
}

// Encode informasi user ke dalam format JSON
$user_info_json = json_encode($user_info);
// Variabel untuk menyimpan total harga dan produk per vendor
$totalHarga = 0;
$vendorProducts = [];

// Mengelompokkan produk berdasarkan vendor
while ($row = $result->fetch_assoc()) {
    $vendorName = $row['company_name'];
    if (!isset($vendorProducts[$vendorName])) {
        $vendorProducts[$vendorName] = [
            'vendor_name' => $row['company_name'],
            'vendor_logo' => $row['logo'],
            'products' => []
        ];
    }
    $vendorProducts[$vendorName]['products'][] = $row;
}

// Menghitung total harga dan mempersiapkan data produk untuk sesi
$_SESSION['cart'] = [];
$_SESSION['total_price'] = 0;

// Periksa apakah $user_info tersedia untuk menghindari kesalahan
if (!empty($user_info)) {
    $_SESSION['user_name'] = isset($user_info['name']) ? $user_info['name'] : null;
    $_SESSION['user_email'] = isset($user_info['email']) ? $user_info['email'] : null;
    $_SESSION['user_phone'] = isset($user_info['phone']) ? $user_info['phone'] : null;
    $_SESSION['user_address'] = isset($user_info['address']) ? $user_info['address'] : null;
}

foreach ($vendorProducts as $vendor) {
    foreach ($vendor['products'] as $product) {
        $_SESSION['cart'][] = [
            'id' => $product['cart_id'],
            'name' => $product['name'],
            'price' => $product['prices'],
            'quantity' => $product['quantity']
        ];
        $_SESSION['total_price'] += $product['prices'] * $product['quantity'];
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Keranjang Belanja</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="stylecart.css">
</head>
<body>
<?php include ("../container_content/nav.php")?>
<div class="cart-container">
    <div class="cart-items">
        <?php if (empty($vendorProducts)): ?>
            <p>Keranjang kosong.</p>
        <?php else: ?>
            <?php foreach ($vendorProducts as $vendor): ?>
                <div class="cart-item">
                    <div class="vendor-info">
                        <img src="../dashboard_vendor/<?= htmlspecialchars($vendor['vendor_logo']); ?>" alt="<?= htmlspecialchars($vendor['vendor_name']); ?>" class="vendor-logo" />
                        <p class="vendor-name"><?= htmlspecialchars($vendor['vendor_name']); ?></p>
                    </div>

                    <?php foreach ($vendor['products'] as $product): ?>
                        <div class="cart-details" id="product-row-<?= $product['cart_id']; ?>">
                            <div class="content-cart-details">
                                <input type="checkbox" class="product-checkbox" data-price="<?= $product['prices']; ?>" data-product-id="<?= $product['cart_id']; ?>" onchange="updateTotal()" />
                                <img src="../dashboard_vendor/<?= htmlspecialchars($product['images']); ?>" alt="<?= htmlspecialchars($product['name']); ?>" class="cart-image" />
                                <div class="info-details">
                                    <div class="info-nama">
                                        <p><?= htmlspecialchars($product['name']); ?></p>
                                    </div>
                                    <div class="info-harga">
                                        <p>Rp. <?= number_format($product['prices'], 2); ?></p>
                                    </div>
                                    <div class="quantity">
                                        <p>Quantity:
                                            <button class="quantity-btn" onclick="updateQuantity(<?= $product['cart_id']; ?>, -1, <?= $product['stocks']; ?>)">-</button>
                                            <span class="quantity-number" id="quantity-<?= $product['cart_id']; ?>"><?= $product['quantity']; ?></span>
                                            <button class="quantity-btn" onclick="updateQuantity(<?= $product['cart_id']; ?>, 1, <?= $product['stocks']; ?>)">+</button>
                                        </p>
                                    </div>
                                    <div class="total-harga-quantity">
                                        <p>Total: Rp. <span id="product-total-<?= $product['cart_id']; ?>"><?= number_format($product['prices'] * $product['quantity'], 2); ?></span></p>
                                    </div>
                                </div>
                            </div>
                            <button class="remove-btn" onclick="removeFromCart(<?= $product['cart_id']; ?>)"><i class="fa-solid fa-trash-can"></i></button>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

    <div class="checkout-info">
    <div class="shipping-info">
    <h3>Informasi Pengiriman</h3>
    <p><strong>Nama:</strong> <?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Nama tidak tersedia'; ?></p>
    <p><strong>Telepon:</strong> <?= isset($_SESSION['user_phone']) ? htmlspecialchars($_SESSION['user_phone']) : 'Telepon tidak tersedia'; ?></p>
    <p><strong>Alamat:</strong> <?= isset($_SESSION['user_address']) ? htmlspecialchars($_SESSION['user_address']) : 'Alamat tidak tersedia'; ?></p>
</div>


        <div class="total-payment">
            <h3>Total Pembayaran:</h3>
            <p id="totalAmount">Rp. 0</p>
            <button class="checkout-btn" id="checkoutBtn" onclick="confirmCheckout()" disabled>Lanjutkan ke Pembayaran</button>
        </div>
    </div>
</div>

<script>
function confirmCheckout() {
    console.log('Fungsi confirmCheckout dipanggil');

    let selectedProducts = [];
    let checkboxes = document.querySelectorAll('.product-checkbox');
    let productListContainer = document.getElementById('selectedProducts');
    productListContainer.innerHTML = ''; // Kosongkan produk yang ada di popup sebelum diisi ulang

    // Ambil produk yang dicentang
    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            let productRow = checkbox.closest('.cart-details');
            let productId = checkbox.getAttribute('data-product-id');
            let productName = productRow.querySelector('.info-nama p').innerText;
            let productPrice = parseFloat(productRow.querySelector('.info-harga p').innerText.replace('Rp. ', '').replace(/,/g, ''));
            let productQuantity = parseInt(productRow.querySelector('.quantity-number').innerText);
            let productTotalPrice = productPrice * productQuantity;
            let productImage = productRow.querySelector('.cart-image').getAttribute('src');

            // Tambahkan produk yang dipilih ke tabel popup
            let productItem = document.createElement('tr');
            productItem.innerHTML = `
                <td><img src="${productImage}" alt="${productName}" style="width: 50px; height: 50px;"></td>
                <td>${productName}</td>
                <td>${productQuantity}</td>
                <td>Rp. ${productPrice.toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
                <td>Rp. ${productTotalPrice.toLocaleString('id-ID', { minimumFractionDigits: 2 })}</td>
            `;
            productListContainer.appendChild(productItem);

            selectedProducts.push({
                id: productId,
                name: productName,
                price: productPrice,
                quantity: productQuantity,
                total: productTotalPrice
            });
        }
    });

    // Tampilkan popup setelah produk dimasukkan
    document.getElementById('checkoutPopup').style.display = 'flex';

    console.log('Produk yang dipilih:', selectedProducts); // Debugging
}

</script>



<div id="checkoutPopup" class="popup-overlay" style="display: none;">
    <div class="popup-content">
        <div class="popup-header">
            <h3>Konfirmasi Pemesanan</h3>
            <span class="close-btn" onclick="closePopup()">&times;</span>
        </div>
        <div class="popup-body">
            <div class="popup-grid">
            <div class="info-section">
    <h4>Informasi Pembeli:</h4>
    <table class="info-table">
        <tr><td><strong>Nama:</strong></td><td><?= isset($_SESSION['user_name']) ? htmlspecialchars($_SESSION['user_name']) : 'Nama tidak tersedia'; ?></td></tr>
        <tr><td><strong>Telepon:</strong></td><td><?= isset($_SESSION['user_phone']) ? htmlspecialchars($_SESSION['user_phone']) : 'Telepon tidak tersedia'; ?></td></tr>
        <tr><td><strong>Alamat:</strong></td><td><?= isset($_SESSION['user_address']) ? htmlspecialchars($_SESSION['user_address']) : 'Alamat tidak tersedia'; ?></td></tr>
        <tr><td><strong>Email:</strong></td><td><?= isset($_SESSION['user_email']) ? htmlspecialchars($_SESSION['user_email']) : 'Email tidak tersedia'; ?></td></tr>
    </table>
</div>
                <div class="product-section">
                    <h4>Produk yang akan dibeli:</h4>
                    <table class="product-table">
                        <thead>
                            <tr>
                                <th>Gambar</th>
                                <th>Nama Produk</th>
                                <th>Quantity</th>
                                <th>Harga</th>
                                <th>Total</th>
                            </tr>
                        </thead>
                        <tbody id="selectedProducts"></tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="popup-footer">
            <button class="confirm-btn" onclick="submitCheckout()">Konfirmasi dan Bayar</button>
            <button class="cancel-btn" onclick="closePopup()">Batal</button>
        </div>
    </div>
</div>

<script>
    function closePopup() {
        document.getElementById('checkoutPopup').style.display = 'none'; // Menyembunyikan popup
    }

    function submitCheckout() {
        console.log('Checkout button clicked');
        let selectedProducts = [];
        let totalAmount = 0;
        let checkboxes = document.querySelectorAll('.product-checkbox');

        checkboxes.forEach(checkbox => {
            if (checkbox.checked) {
                let productRow = checkbox.closest('.cart-details');
                let productId = checkbox.getAttribute('data-product-id');
                let productName = productRow.querySelector('.info-nama p').innerText;
                let productPrice = parseFloat(productRow.querySelector('.info-harga p').innerText.replace('Rp. ', '').replace(/,/g, ''));
                let productQuantity = parseInt(productRow.querySelector('.quantity-number').innerText);
                let productTotalPrice = productPrice * productQuantity;

                selectedProducts.push({
                    id: productId,
                    name: productName,
                    price: productPrice,
                    quantity: productQuantity,
                    total: productTotalPrice
                });

                totalAmount += productTotalPrice;
            }
        });

        let form = document.createElement('form');
        form.method = 'POST';
        form.action = 'payment_page.php';

        let productsInput = document.createElement('input');
        productsInput.type = 'hidden';
        productsInput.name = 'selectedProducts';
        productsInput.value = JSON.stringify(selectedProducts);
        form.appendChild(productsInput);

        let totalInput = document.createElement('input');
        totalInput.type = 'hidden';
        totalInput.name = 'totalAmount';
        totalInput.value = totalAmount;
        form.appendChild(totalInput);

        document.body.appendChild(form);
        form.submit();
    }
</script>




<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script >
  $(document).ready(function () {
    // Fungsi untuk membuka popup chat dan memulai dengan daftar chat
    $("#openChatBtn").on("click", function () {
        console.log('Open chat button clicked');
        $("#myChatPopup").show(); // Tampilkan popup chat
        $("#chatList").show(); // Tampilkan daftar chat
        $("#chatView").hide(); // Sembunyikan tampilan chat
        $("#chatHeaderTitle").text("Chat List").show(); // Setel judul default untuk list card
        $("#closeChatViewBtn").hide(); // Sembunyikan tombol kembali ke list
        $("#closeChatListBtn").show(); // Tampilkan tombol close popup
        $(".chat-footer").hide(); // Sembunyikan form pesan
        loadAccountList(); // Muat daftar akun
    });

    // Fungsi untuk menutup popup chat (kembali ke halaman utama)
    $("#closeChatListBtn").on("click", function () {
        console.log('Close chat list button clicked');
        $("#myChatPopup").hide(); // Sembunyikan seluruh popup chat
        $("#chatHeaderTitle").hide(); // Sembunyikan judul chat
    });

    // Fungsi untuk menutup tampilan chat dan kembali ke daftar chat
    $("#closeChatViewBtn").on("click", function () {
        console.log('Close chat view button clicked');
        $("#chatList").show(); // Tampilkan daftar chat
        $("#chatView").hide(); // Sembunyikan tampilan chat
        $("#chatHeaderTitle").text("Chat List").show(); // Setel kembali judul ke "Chat List"
        $("#closeChatViewBtn").hide(); // Sembunyikan tombol kembali ke list
        $("#closeChatListBtn").show(); // Tampilkan tombol close popup
        $(".chat-footer").hide(); // Sembunyikan form pesan
    });

    // Fungsi untuk menampilkan tampilan chat saat kartu pengguna diklik
    $("#chatListContent").on("click", ".account-card", function () {
        var receiverId = $(this).data("receiver-id");
        var username = $(this).data("username"); // Ambil nama pengguna dari data-username
        console.log('Account card clicked, opening chat with:', username);
        openChatWithUser(receiverId, username); // Buka percakapan dengan pengguna
    });

    // Fungsi untuk memuat daftar akun pengguna/vendor
    function loadAccountList() {
        console.log('Loading account list...');
        $.ajax({
            url: "../system.message/get_chat_list.php",
            success: function (data) {
                console.log('Account list loaded:', data);
                $("#chatListContent").html(data); // Masukkan data daftar chat
            },
            error: function (xhr, status, error) {
                console.error('Error loading account list:', error);
            }
        });
    }

    // Fungsi untuk membuka percakapan dengan pengguna/vendor
    function openChatWithUser(receiverId, username) {
        console.log('Opening chat with user:', username);
        // Sembunyikan daftar chat dan tampilkan chat view
        $("#chatList").hide();
        $("#chatView").show(); // Tampilkan chat view
        $(".chat-footer").show(); // Tampilkan form pesan
        $("#chatHeaderTitle").text(username).show(); // Tampilkan judul dengan nama pengguna
        $("#closeChatListBtn").hide(); // Sembunyikan tombol close popup
        $("#closeChatViewBtn").show(); // Tampilkan tombol kembali ke list

        // Muat pesan dari penerima ini
        loadMessages(receiverId);
    }

    // Fungsi untuk memuat pesan dari penerima
    function loadMessages(receiverId) {
        console.log('Loading messages for receiver ID:', receiverId);
        $.ajax({
            url: "../system.message/get_messages.php",
            data: { receiver_id: receiverId },
            success: function (data) {
                console.log('Messages loaded:', data);
                $("#chatMessages").html(data); // Tampilkan pesan di dalam chat body
            },
            error: function (xhr, status, error) {
                console.error('Error loading messages:', error);
            }
        });
    }

    // Fungsi untuk mengirim pesan
    $("#sendMessageBtn").on("click", function () {
        sendMessage();
    });

    $("#chatInput").on("keydown", function (e) {
        if (e.key === "Enter" && !e.shiftKey) {
            e.preventDefault();
            sendMessage();
        }
    });

    // Fungsi untuk mengirim pesan
    function sendMessage() {
        var message = $("#chatInput").val().trim();
        var receiverId = $("#chatHeaderTitle").data("receiver-id");

        if (message !== "") {
            console.log('Sending message:', message);
            $.ajax({
                type: "POST",
                url: "../system.message/send_message.php",
                data: { message_content: message, receiver_id: receiverId },
                success: function (response) {
                    console.log('Message sent:', response);
                    $("#chatInput").val(""); // Kosongkan input setelah pesan dikirim
                    loadMessages(receiverId); // Muat ulang pesan
                },
                error: function (xhr, status, error) {
                    console.error('Error sending message:', error);
                }
            });
        }
    }
});
</script>


<script>
function updateTotal() {
    let checkboxes = document.querySelectorAll('.product-checkbox');
    let total = 0;
    let isChecked = false;

    checkboxes.forEach(checkbox => {
        if (checkbox.checked) {
            let productRow = checkbox.closest('.cart-details');
            let quantity = parseInt(productRow.querySelector('.quantity-number').innerText);
            let price = parseFloat(checkbox.getAttribute('data-price'));
            total += price * quantity;
            isChecked = true;
            console.log("Produk dicentang: " + checkbox.getAttribute('data-product-id')); // Log produk yang dicentang
        }
    });

    console.log("Total harga: Rp. " + total);
    document.getElementById('totalAmount').innerText = 'Rp. ' + total.toLocaleString('id-ID', { minimumFractionDigits: 2 });
    document.getElementById('checkoutBtn').disabled = !isChecked; 
    console.log("Tombol aktif: " + !document.getElementById('checkoutBtn').disabled); // Log untuk memeriksa status tombol
}


// Fungsi untuk memperbarui quantity dan total harga per produk langsung
function updateQuantity(cartId, change, maxStock) {
    let quantityElement = document.getElementById('quantity-' + cartId);
    let currentQuantity = parseInt(quantityElement.innerText);
    let newQuantity = currentQuantity + change;

    if (newQuantity <= 0) {
    Swal.fire({
        title: 'Hapus Produk?',
        text: "Anda akan menghapus produk ini dari keranjang.",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#3085d6', // Warna untuk tombol konfirmasi
        cancelButtonColor: '#d33', // Warna untuk tombol batal
        confirmButtonText: 'Ya, hapus',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            removeFromCart(cartId); // Hapus produk jika quantity <= 0 dan pengguna mengkonfirmasi
            Swal.fire(
                'Dihapus!',
                'Produk telah dihapus dari keranjang.',
                'success'
            );
        }
    });
}
 else if (newQuantity > maxStock) {
        Swal.fire('Stok Tidak Cukup', `Stok barang hanya tersisa ${maxStock}.`, 'error');
    } else {
        // Update quantity di UI
        quantityElement.innerText = newQuantity;

        // Kirim perubahan quantity ke server
        let xhr = new XMLHttpRequest();
        xhr.open("POST", "update_cart.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onreadystatechange = function () {
            if (xhr.readyState == 4 && xhr.status == 200) {
                // Perbarui total harga per produk
                let pricePerUnit = parseFloat(document.querySelector(`[data-product-id="${cartId}"]`).getAttribute('data-price'));
                let totalPrice = pricePerUnit * newQuantity;
                document.getElementById('product-total-' + cartId).innerText = totalPrice.toLocaleString('id-ID', { minimumFractionDigits: 2 });

                // Perbarui total pembayaran
                updateTotal();
            }
        };
        xhr.send("cart_id=" + cartId + "&quantity=" + newQuantity);
    }
}

// Fungsi untuk menghapus produk dari keranjang
function removeFromCart(cartId) {
    let productRow = document.getElementById('product-row-' + cartId);
    productRow.remove();

    // Kirim permintaan ke server untuk menghapus item
    let xhr = new XMLHttpRequest();
    xhr.open("POST", "remove_cart.php", true);
    xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
    xhr.onreadystatechange = function () {
        if (xhr.readyState == 4 && xhr.status == 200) {
            let response = JSON.parse(xhr.responseText);
            if (response.success) {
                updateTotal(); // Perbarui total setelah produk dihapus
            } else {
                alert('Gagal menghapus produk');
            }
        }
    };
    xhr.send("cart_id=" + cartId);
}

</script>
<script>
        // Mendapatkan data user dari PHP ke JavaScript
        const userInfo = <?= $user_info_json ?>;
        
        // Menampilkan informasi user di console
        console.log("Informasi User:");
        if (userInfo.name) {
            console.log("Nama:", userInfo.name);
        }
        if (userInfo.email) {
            console.log("Email:", userInfo.email);
        }
        if (userInfo.phone) {
            console.log("Telepon:", userInfo.phone);
        }
        if (userInfo.address) {
            console.log("Alamat:", userInfo.address);
        }
    </script>
</body>
</html>
