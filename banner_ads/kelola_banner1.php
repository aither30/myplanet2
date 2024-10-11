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

    // Ambil jumlah stok iklan dari paket iklan vendor
    $query_paket = "SELECT p.jumlah_iklan_landscape 
                    FROM pembelian_paket_ads ppa
                    JOIN paket_ads p ON ppa.paket_id = p.paket_id
                    WHERE ppa.vendor_id = '$vendor_id' AND ppa.status_pembayaran = 'paid' AND ppa.status_iklan = 'active'";
    $result_paket = $koneksi->query($query_paket);
    $stok_iklan_landscape = 0;

    if ($result_paket->num_rows > 0) {
        $row_paket = $result_paket->fetch_assoc();
        $stok_iklan_landscape = $row_paket['jumlah_iklan_landscape']; // Jumlah stok iklan landscape dari paket
    }

    // Hitung jumlah iklan landscape yang sudah diupload oleh vendor
    $query_jumlah_iklan = "SELECT COUNT(*) as jumlah_iklan FROM banner_ads WHERE vendor_id = '$vendor_id' AND banner_type = 'landscape'";
    $result_jumlah_iklan = $koneksi->query($query_jumlah_iklan);
    $row_jumlah_iklan = $result_jumlah_iklan->fetch_assoc();
    $jumlah_iklan_terupload = $row_jumlah_iklan['jumlah_iklan']; // Jumlah iklan yang sudah diupload

    // Proses penambahan iklan jika form disubmit
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $jumlah_iklan_terupload < $stok_iklan_landscape) {
        // Proses upload gambar
        $target_dir = "uploads/"; // Folder tempat menyimpan gambar
        $original_file_name = basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

        // Generate nama file unik dengan menambahkan vendor_id dan timestamp
        $new_file_name = pathinfo($original_file_name, PATHINFO_FILENAME) . "_" . $vendor_id . "_" . time() . "." . $imageFileType;
        $target_file = $target_dir . $new_file_name;

        $uploadOk = 1;

        // Cek apakah file benar-benar gambar
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $pesan_error = "File bukan gambar.";
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
                        VALUES ('$vendor_id', 'landscape', '$image_url', '$title', '$description', '$button_text', '$link_url')";

                if ($koneksi->query($sql) === TRUE) {
                    $iklan_berhasil = true; // Menandakan bahwa iklan berhasil ditambahkan
                    $jumlah_iklan_terupload++; // Tambahkan jumlah iklan yang terupload
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

// Mengambil semua data banner dengan banner_type = 'landscape'
$sql = "SELECT * FROM banner_ads WHERE banner_type = 'landscape' AND vendor_id = '$vendor_id'";
$result = $koneksi->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Iklan Banner 1 (Landscape)</title>
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style_kelolabanner1.css"> <!-- Link ke file CSS -->
    <script>
        function showForm() {
            document.getElementById("form-container").style.display = "block";
            document.getElementById("content-section").style.display = "none";
        }

        function hideForm() {
            document.getElementById("form-container").style.display = "none";
            document.getElementById("content-section").style.display = "block";
        }

        function updateSlotInfo(jumlahTerupload) {
            document.getElementById('jumlah-terupload').innerText = jumlahTerupload;
            if (jumlahTerupload >= <?= $stok_iklan_landscape ?>) {
                document.getElementById('tambah-iklan-btn').style.display = 'none';
            }
        }

        function showSuccessAlert() {
            Swal.fire({
                title: 'Berhasil!',
                text: 'Iklan baru berhasil ditambahkan.',
                icon: 'success',
                confirmButtonText: 'OK'
            }).then(() => {
                hideForm();
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

        function confirmDelete(bannerId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak dapat mengembalikan data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#d33',
                cancelButtonColor: '#3085d6',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = 'delete_banner.php?id=' + bannerId;
                }
            });
        }
    </script>
</head>

<body>

<div class="container">
    <h2>Kelola Iklan Banner 1 (Landscape)</h2>

    <!-- Informasi slot iklan landscape -->
    <div class="info">
        <p>Anda memiliki <strong><?= $stok_iklan_landscape ?></strong> slot untuk iklan landscape.</p>
        <p>Anda telah menggunakan <strong id="jumlah-terupload"><?= $jumlah_iklan_terupload ?></strong> dari total slot yang tersedia.</p>
    </div>

    <!-- Form untuk tambah iklan -->
    <div id="form-container" class="hidden">
        <form method="POST" enctype="multipart/form-data">
            <label>Unggah Gambar:</label>
            <input type="file" name="image" required>

            <label>Judul Iklan:</label>
            <input type="text" name="title" required>

            <label>Deskripsi:</label>
            <textarea name="description" required></textarea>

            <label>Tombol (Jika Ada):</label>
            <input type="text" name="button_text">

            <label>Link URL:</label>
            <input type="text" name="link_url">

            <button type="submit" class="btn">Tambah Iklan</button>
            <button type="button" class="btn" onclick="hideForm()">Kembali ke Daftar Iklan</button>
        </form>
    </div>

    <!-- Daftar iklan -->
    <div id="content-section" class="table-container">
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
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['banner_id']}</td>
                            <td><img src='{$row['image_url']}' width='100'></td>
                            <td>{$row['title']}</td>
                            <td>{$row['description']}</td>
                            <td><a href='{$row['link_url']}' target='_blank'>Buka Link</a></td>
                            <td class='action-buttons'>
                                <a href='edit_banner.php?id={$row['banner_id']}'>Edit</a> | 
                                <a href='javascript:void(0);' onclick='confirmDelete({$row['banner_id']})'>Hapus</a>
                            </td>
                          </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Tidak ada data iklan ditemukan.</td></tr>";
            }
            ?>
        </table>
        <button class="btn" id="tambah-iklan-btn" onclick="showForm()" <?= ($jumlah_iklan_terupload >= $stok_iklan_landscape) ? 'style="display:none;"' : ''; ?>>Tambah Iklan</button>
        <a href="index.php" class="btn-back">Kembali ke Halaman Utama</a>
    </div>
</div>

<?php
if ($iklan_berhasil) {
    echo "<script>showSuccessAlert();</script>";
} elseif (!empty($pesan_error)) {
    echo "<script>showErrorAlert('$pesan_error');</script>";
}
?>

</body>
</html>

<?php $koneksi->close(); ?>
