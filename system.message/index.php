<?php
session_start(); // Memulai session

include ("../config/config.php");

// Pastikan session account_id dan username sudah ada
if (!isset($_SESSION['account_id']) || !isset($_SESSION['username'])) {
    // Arahkan ke halaman login jika session belum ada
    header("Location: login.php");
    exit();
}

// Ambil session username dan account_id
$account_id = $_SESSION['account_id'];
$username = $_SESSION['username'];

// Debugging menggunakan console.log di browser
echo "<script>console.log('Session Account ID: " . $account_id . "');</script>";
echo "<script>console.log('Session Username: " . $username . "');</script>";
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dan Daftar User/Vendor</title>
    <!-- Memuat file CSS eksternal -->
    <link rel="stylesheet" href="styles.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
$(document).ready(function() {
    var currentReceiverId = null; // Variable untuk melacak receiver yang sedang aktif
    var scrollAtBottom = false; // Variabel untuk melacak apakah pengguna sudah berada di bawah

    // Fungsi untuk memuat daftar chat (otomatis memperbarui setiap 1 detik)
    function loadChatList() {
        $.ajax({
            url: "get_chat_list.php", // Mengambil daftar user dengan pesan terbaru
            success: function(data) {
                $('.account-list').html(data); // Perbarui konten di bagian account-list
                
                // Setelah daftar card diperbarui, cek apakah ada chat yang sedang dibuka
                if (currentReceiverId !== null) {
                    loadMessages(currentReceiverId); // Muat ulang pesan jika sedang di chat tertentu

                    // Tandai card yang sedang aktif
                    $('.account-card').removeClass('active'); // Hapus class 'active' dari semua card
                    $('.account-card[data-receiver-id="' + currentReceiverId + '"]').addClass('active'); // Tambahkan class 'active' ke card yang sesuai
                }
            }
        });
    }

    // Fungsi untuk memuat pesan ketika pengguna mengklik user/vendor
    $('.account-list').on('click', '.account-card', function() {
        var receiver_id = $(this).data('receiver-id');
        var receiver_name = $(this).find('h3').text(); // Ambil nama penerima dari h3
        currentReceiverId = receiver_id; // Set receiver_id aktif
        scrollAtBottom = false; // Reset status scroll ketika membuka chat baru

        // Tandai card yang sedang aktif
        $('.account-card').removeClass('active'); // Hapus class 'active' dari semua card
        $(this).addClass('active'); // Tambahkan class 'active' ke card yang dipilih

        loadMessages(receiver_id);
        // Perbarui header chat dengan nama penerima
        $('.chat-header').text(receiver_name);
    });

    // Fungsi untuk memuat dan menampilkan pesan
    function loadMessages(receiver_id) {
        $.ajax({
            url: "get_messages.php", // Script untuk mengambil pesan
            data: {
                receiver_id: receiver_id
            },
            success: function(data) {
                var chatArea = $('.chat-area');
                var isScrolledToBottom = chatArea.scrollTop() + chatArea.innerHeight() >= chatArea[0].scrollHeight;

                $('.chat-area').html(data); // Masukkan data ke dalam div chat-area

                // Set event untuk pengiriman pesan setelah pesan dimuat
                setupSendMessage(receiver_id);

                // Hanya scroll otomatis jika pengguna berada di bagian bawah atau ketika pesan baru dikirim
                if (isScrolledToBottom || scrollAtBottom) {
                    scrollToLatestMessage();
                }
            }
        });
    }

    // Fungsi untuk menggulir ke pesan terbaru setelah pesan dikirim
    function scrollToLatestMessage() {
        var chatArea = $('.chat-area');
        chatArea.scrollTop(chatArea[0].scrollHeight); // Gulir ke bagian paling bawah
    }

    // Fungsi untuk mengirim pesan
    function sendMessage(receiver_id, message) {
        if (message.trim() !== '') {
            $.ajax({
                type: "POST",
                url: "send_message.php",
                data: {
                    message_content: message,
                    receiver_id: receiver_id
                },
                success: function(response) {
                    $('textarea[name="message_content"]').val(''); // Kosongkan textarea setelah pesan terkirim
                    scrollAtBottom = true; // Tandai bahwa harus scroll setelah pesan dikirim
                    loadMessages(receiver_id); // Muat ulang pesan dan gulir ke pesan terbaru
                }
            });
        }
    }

    // Fungsi untuk setup event submit form dan keydown Enter
    function setupSendMessage(receiver_id) {
        // Event listener untuk textarea dengan keydown
        $('textarea[name="message_content"]').off('keydown').on('keydown', function(e) {
            if (e.key === 'Enter') {
                if (e.shiftKey) {
                    // Shift + Enter: Tambahkan baris baru
                    var textarea = $(this);
                    var cursorPos = textarea.prop('selectionStart');
                    var text = textarea.val();
                    textarea.val(text.substring(0, cursorPos) +  text.substring(cursorPos));
                    e.stopPropagation();
                } else {
                    // Enter tanpa Shift: Kirim pesan
                    e.preventDefault(); // Mencegah newline
                    var message = $(this).val().trim();
                    sendMessage(receiver_id, message); // Panggil fungsi kirim pesan
                }
            }
        });

        // Tombol submit juga mengirimkan pesan saat di-klik
        $('#chat-form').off('submit').on('submit', function(e) {
            e.preventDefault(); // Mencegah refresh halaman
            var message = $('textarea[name="message_content"]').val().trim();
            sendMessage(receiver_id, message); // Panggil fungsi kirim pesan
        });

        // Tombol kirim (ikon submit) juga mengirim pesan
        $('button[name="send_message"]').off('click').on('click', function(e) {
            e.preventDefault(); // Mencegah refresh halaman
            var message = $('textarea[name="message_content"]').val().trim();
            sendMessage(receiver_id, message); // Panggil fungsi kirim pesan
        });
    }

    // Muat ulang daftar chat setiap 5 detik
    setInterval(loadChatList, 1000); // 1000 ms = 1 detik

    // Panggil fungsi memuat daftar chat pertama kali
    loadChatList();
});



    </script>
</head>
<body>
<nav>
    <div class="left_nav">
        <div class="logo">
            <img src="./assets/attribute myplanet/Logo My PlanEt.png" alt="My PlanET" />
            <a href="../home.php">My PlanET</a>
        </div>
    </div>
    <div class="right_nav">
        <?php if (isset($_SESSION['loggedin']) && $_SESSION['loggedin'] === true): ?>
            <div class="Dropdown">
                <div class="profil">
                    <button><?php echo $_SESSION['username']; ?></button>
                </div>
                <div class="Content-dropdown">
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
<div class="container">
    <!-- Sidebar/Aside untuk daftar user/vendor -->
    <aside>
        <div class="account-list">
            <!-- Daftar user/vendor akan dimuat secara otomatis menggunakan AJAX -->
        </div>
    </aside>

    <!-- Bagian utama untuk chat -->
    <main>
        <div class="chat-header">
            Pilih user/vendor untuk mulai chat
        </div>
        <div class="chat-area">
            <h1>Pilih user/vendor untuk mulai chat</h1>
        </div>

        <!-- Form untuk kirim pesan -->
        <form id="chat-form" method="POST" action="">
            <textarea name="message_content" placeholder="Ketik pesan..."></textarea>
            <button type="submit" name="send_message">➤</button>
        </form>
    </main>
</div>

<?php
$koneksi->close();
?>

</body>
</html>
