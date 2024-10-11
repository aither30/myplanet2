<?php
session_start(); // Memulai session

include ("../config/config.php");

// Cek apakah pengguna sudah login dan parameter `receiver_id` tersedia
if (!isset($_SESSION['account_id'])) {
    header("Location: login.php");
    exit();
}

// Debugging: Menampilkan session di console.log browser
echo "<script>console.log('Session Account ID: " . $_SESSION['account_id'] . "');</script>";
echo "<script>console.log('Session Username: " . $_SESSION['username'] . "');</script>";

if (!isset($_GET['receiver_id']) || !is_numeric($_GET['receiver_id'])) {
    echo "Penerima tidak valid!";
    exit();
}

$receiver_id = $_GET['receiver_id'];
$sender_id = $_SESSION['account_id'];

// Cek apakah receiver_id valid
$check_receiver = $koneksi->query("SELECT * FROM account WHERE account_id = $receiver_id");

// Tambahkan pengecekan apakah query berhasil dan mengembalikan data
if ($check_receiver && $check_receiver->num_rows > 0) {
    $receiver = $check_receiver->fetch_assoc();
} else {
    // Jika penerima tidak ditemukan, tampilkan pesan error yang sesuai
    echo "Penerima tidak ditemukan!";
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chat dengan <?php echo isset($receiver['username']) ? htmlspecialchars($receiver['username']) : 'Pengguna Tidak Ditemukan'; ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
        }
        h1 {
            text-align: center;
        }
        .message-list {
            max-width: 600px;
            margin: 20px auto;
            padding: 10px;
            background-color: #fff;
            border: 1px solid #ddd;
        }
        .message {
            padding: 10px;
            margin: 5px 0;
            border-radius: 5px;
        }
        .message-sent {
            background-color: #e1ffc7;
            text-align: right;
        }
        .message-received {
            background-color: #f1f1f1;
            text-align: left;
        }
        form {
            display: flex;
            justify-content: center;
            margin-top: 20px;
        }
        textarea {
            width: 80%;
            height: 50px;
        }
        button {
            padding: 10px;
        }
    </style>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script>
        // Fungsi untuk mengirim pesan melalui AJAX tanpa refresh
        $(document).ready(function() {
            $('#chat-form').submit(function(e) {
                e.preventDefault(); // Mencegah refresh halaman
                var message = $('textarea[name="message_content"]').val().trim(); // Hapus spasi dan baris kosong
                if (message === '' || message.replace(/\n/g, '') === '') {
                    $('textarea[name="message_content"]').val(''); // Kosongkan jika hanya berisi baris kosong
                    return; // Jangan kirim pesan jika kosong
                }

                var receiver_id = <?php echo $receiver_id; ?>;
                $.ajax({
                    type: "POST",
                    url: "send_message.php", // Script untuk mengirim pesan
                    data: {
                        message_content: message,
                        receiver_id: receiver_id
                    },
                    success: function(response) {
                        $('textarea[name="message_content"]').val(''); // Kosongkan textarea setelah pesan terkirim
                        loadMessages(); // Panggil fungsi untuk memperbarui daftar pesan
                    }
                });
            });

            // Fungsi untuk mengambil pesan secara periodik tanpa refresh
            function loadMessages() {
                $.ajax({
                    url: "get_messages.php", // Script untuk mengambil pesan
                    data: {
                        receiver_id: <?php echo $receiver_id; ?>
                    },
                    success: function(data) {
                        $('.message-list').html(data); // Masukkan data ke dalam div message-list
                    }
                });
            }

            // Ambil pesan baru setiap 2 detik
            setInterval(loadMessages, 2000);
        });
    </script>
</head>
<body>
    
    <h1>Chat dengan <?php echo isset($receiver['username']) ? htmlspecialchars($receiver['username']) : 'Pengguna Tidak Ditemukan'; ?></h1>

    <!-- Tampilkan pesan antara pengguna login dan penerima -->
    <div class="message-list">
        <!-- Pesan akan dimuat melalui AJAX -->
    </div>

    <!-- Form untuk kirim pesan -->
    <form id="chat-form" method="POST" action="">
        <textarea name="message_content" placeholder="Ketik pesan..."></textarea>
        <button type="submit" name="send_message">Kirim</button>
    </form>
</body>
</html>

<?php
$koneksi->close();
?>
