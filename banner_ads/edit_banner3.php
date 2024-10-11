<?php
include ("../config/config.php");

$success = false; // Variabel untuk melacak apakah pembaruan berhasil

// Mengambil ID banner dari URL, dan pastikan ID valid
if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Query untuk mengambil data banner berdasarkan ID
    $sql = "SELECT * FROM banner_ads WHERE banner_id = $id";
    $result = $koneksi->query($sql);

    // Periksa apakah data ditemukan
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc(); // Ambil data sebagai array
    } else {
        echo "<script>
                alert('Data banner tidak ditemukan.');
                window.location.href = 'kelola_banner3.php';
              </script>";
        exit;
    }
} else {
    echo "<script>
            alert('ID tidak ditemukan.');
            window.location.href = 'kelola_banner3.php';
          </script>";
    exit;
}

// Memperbarui data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image_url = $_POST['image_url'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $button_text = $_POST['button_text'];
    $link_url = $_POST['link_url'];

    // Query untuk memperbarui data banner di database
    $sql = "UPDATE banner_ads SET 
            image_url = '$image_url', 
            title = '$title', 
            description = '$description', 
            button_text = '$button_text', 
            link_url = '$link_url' 
            WHERE banner_id = $id";

    if ($koneksi->query($sql) === TRUE) {
        $success = true; // Tandai bahwa pembaruan berhasil
    } else {
        $success = false; // Tandai bahwa pembaruan gagal
        $error_message = $koneksi->error; // Simpan pesan error
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Iklan</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f6f9;
        }
        .container {
            max-width: 800px;
            margin: 50px auto;
            padding: 20px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
        }
        h2 {
            text-align: center;
            font-size: 28px;
            margin-bottom: 30px;
            color: #333;
        }
        form {
            max-width: 600px;
            margin: 0 auto;
        }
        label {
            font-size: 16px;
            margin-bottom: 8px;
            display: block;
            color: #333;
        }
        input[type="text"], textarea {
            width: 100%;
            padding: 10px;
            margin-bottom: 20px;
            border: 1px solid #ddd;
            border-radius: 4px;
            font-size: 16px;
        }
        textarea {
            resize: none;
            height: 120px;
        }
        button {
            display: inline-block;
            padding: 12px 30px;
            font-size: 16px;
            color: white;
            background-color: #4CAF50;
            border: none;
            border-radius: 50px;
            cursor: pointer;
            transition: background-color 0.3s;
            margin-top: 20px;
            width: 100%;
        }
        button:hover {
            background-color: #45a049;
        }
        .back-button {
            text-align: center;
            margin-top: 20px;
        }
        .back-button a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #333;
            color: white;
            border-radius: 50px;
            text-decoration: none;
            margin-top: 10px;
        }
        .back-button a:hover {
            background-color: #555;
        }
    </style>
</head>
<body>

<div class="container">
    <h2>Edit Iklan Banner</h2>

    <form method="POST">
        <label>URL Gambar:</label>
        <input type="text" name="image_url" value="<?php echo $row['image_url']; ?>" required>
        
        <label>Judul Iklan:</label>
        <input type="text" name="title" value="<?php echo $row['title']; ?>" required>
        
        <label>Deskripsi:</label>
        <textarea name="description" required><?php echo $row['description']; ?></textarea>
        
        <label>Tombol:</label>
        <input type="text" name="button_text" value="<?php echo $row['button_text']; ?>">
        
        <label>Link URL:</label>
        <input type="text" name="link_url" value="<?php echo $row['link_url']; ?>" required>
        
        <button type="submit">Perbarui Iklan</button>
    </form>

    <div class="back-button">
        <a href="kelola_banner3.php">Kembali</a>
    </div>
</div>

<?php
// Tampilkan SweetAlert tergantung hasil pembaruan
if ($success) {
    echo "<script>
        Swal.fire({
            title: 'Berhasil!',
            text: 'Data iklan berhasil diperbarui.',
            icon: 'success',
            confirmButtonText: 'OK'
        }).then((result) => {
            if (result.isConfirmed) {
                window.location.href = 'kelola_banner3.php';
            }
        });
    </script>";
} elseif (isset($error_message)) {
    echo "<script>
        Swal.fire({
            title: 'Gagal!',
            text: 'Error: $error_message',
            icon: 'error',
            confirmButtonText: 'OK'
        });
    </script>";
}
?>

</body>
</html>

<?php $koneksi->close(); ?>
