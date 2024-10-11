<?php
session_start(); // Memulai session

include ("../config/config.php");

// Variabel untuk melacak apakah iklan berhasil ditambahkan
$iklan_berhasil = false;
$pesan_error = "";

// Ambil username dari session
$username = $_SESSION['username'];

// Cari vendor_id berdasarkan username
$query_vendor = "SELECT vendor_id FROM business_account WHERE username = '$username'";
$result_vendor = $koneksi->query($query_vendor);

if ($result_vendor->num_rows > 0) {
    $row_vendor = $result_vendor->fetch_assoc();
    $vendor_id = $row_vendor['vendor_id']; // Dapatkan vendor_id

    // Menambahkan data baru jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Proses upload gambar
        $target_dir = "uploads/"; // Folder tempat menyimpan gambar
        $target_file = $target_dir . basename($_FILES["image"]["name"]);
        $uploadOk = 1;
        $imageFileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));

        // Cek apakah file benar-benar gambar
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $pesan_error = "File bukan gambar.";
            $uploadOk = 0;
        }

        // Cek jika file sudah ada
        if (file_exists($target_file)) {
            $pesan_error = "Maaf, file sudah ada.";
            $uploadOk = 0;
        }

        // Batasi ukuran file (maksimum 5MB)
        if ($_FILES["image"]["size"] > 5000000) {
            $pesan_error = "Maaf, ukuran file terlalu besar.";
            $uploadOk = 0;
        }

        // Batasi tipe file yang diizinkan
        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $pesan_error = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
            $uploadOk = 0;
        }

        // Cek apakah uploadOk bernilai 0 (error)
        if ($uploadOk == 0) {
            $pesan_error = "Maaf, gambar gagal diunggah. " . $pesan_error;
        } else {
            // Jika semua pengecekan lolos, lakukan upload file
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                // Simpan path gambar yang diupload
                $image_url = $target_file;
                $title = $_POST['title'];
                $description = $_POST['description'];
                $button_text = $_POST['button_text'];
                $link_url = $_POST['link_url'];

                // Masukkan data ke database dengan vendor_id yang ditemukan dari username
                $sql = "INSERT INTO banner_ads (vendor_id, banner_type, image_url, title, description, button_text, link_url) 
                        VALUES ('$vendor_id', 'slider3', '$image_url', '$title', '$description', '$button_text', '$link_url')";

                if ($koneksi->query($sql) === TRUE) {
                    $iklan_berhasil = true; // Menandakan bahwa iklan berhasil ditambahkan
                } else {
                    $pesan_error = "Error: " . $sql . "<br>" . $koneksi->error;
                }
            } else {
                $pesan_error = "Maaf, terjadi kesalahan saat mengunggah file Anda.";
            }
        }
    }
} else {
    $pesan_error = "Vendor tidak ditemukan untuk username: " . $username;
}

// Mengambil semua data banner dengan banner_type = 'slider3'
$sql = "SELECT * FROM banner_ads WHERE banner_type = 'slider3'";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Iklan Banner 3 (Slider 3)</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <style>
        body { font-family: Arial, sans-serif; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid black; padding: 10px; text-align: center; }
        .btn { padding: 10px 15px; background-color: #4CAF50; color: white; border: none; cursor: pointer; }
        .btn:hover { background-color: #45a049; }
        .form-container {
            display: none; /* Form akan tersembunyi pada awalnya */
            margin-top: 20px;
        }
        #content-section {
            display: block; /* Bagian konten defaultnya terlihat */
        }
    </style>
    <script>
        function showForm() {
            document.getElementById("form-container").style.display = "block"; // Menampilkan form
            document.getElementById("content-section").style.display = "none"; // Menyembunyikan konten lainnya
        }

        // SweetAlert untuk menampilkan pesan sukses atau error
        function showSuccessAlert() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Iklan baru berhasil ditambahkan.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
        }

        function showErrorAlert(message) {
            Swal.fire({
                title: 'Gagal!',
                text: message,
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    </script>
</head>
<body>

<h2>Kelola Iklan Banner 3 (Slider 3)</h2>

<!-- Tombol untuk memunculkan form -->
<button class="btn" onclick="showForm()">Tambah Iklan</button>

<!-- Section untuk menampilkan form tambah iklan -->
<div id="form-container" class="form-container">
    <form method="POST" enctype="multipart/form-data">
        <label>Unggah Gambar:</label><br>
        <input type="file" name="image" required><br><br>
        
        <label>Judul Iklan:</label><br>
        <input type="text" name="title"><br><br>
        
        <label>Deskripsi:</label><br>
        <textarea name="description"></textarea><br><br>
        
        <label>Tombol (Jika Ada):</label><br>
        <input type="text" name="button_text"><br><br>
        
        <label>Link URL:</label><br>
        <input type="text" name="link_url"><br><br>
        
        <button type="submit" class="btn">Tambah Iklan</button>
    </form>
</div>

<!-- Section untuk menampilkan daftar iklan -->
<div id="content-section">
    <br><br>

    <!-- Tabel untuk menampilkan daftar iklan -->
    <table>
        <tr>
            <th>ID</th>
            <th>Gambar</th>
            <th>Judul</th>
            <th>Deskripsi</th>
            <th>Link</th>
            <th>Aksi</th>
        </tr>
        <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                echo "<tr>
                        <td>{$row['banner_id']}</td>
                        <td><img src='{$row['image_url']}' width='100'></td>
                        <td>{$row['title']}</td>
                        <td>{$row['description']}</td>
                        <td><a href='{$row['link_url']}' target='_blank'>Buka Link</a></td>
                        <td>
                            <a href='edit_banner.php?id={$row['banner_id']}'>Edit</a> | 
                            <a href='delete_banner.php?id={$row['banner_id']}'>Hapus</a>
                        </td>
                    </tr>";
            }
        } else {
            echo "<tr><td colspan='6'>Tidak ada data iklan ditemukan.</td></tr>";
        }
        ?>
    </table>
</div>

<a href="kelola_banner3.php"><button class="btn">Kembali ke Halaman Utama</button></a>

<?php
// Jika iklan berhasil ditambahkan, tampilkan SweetAlert
if ($iklan_berhasil) {
    echo "<script>showSuccessAlert();</script>";
} elseif (!empty($pesan_error)) {
    // Tampilkan pesan error jika terjadi
    echo "<script>showErrorAlert('$pesan_error');</script>";
}
?>

</body>
</html>

<?php $koneksi->close(); ?>
