<?php
session_start();
include ("../config/config.php");

// Inisialisasi variabel
$iklan_berhasil = false;
$pesan_error = "";
$stok_iklan_slider3 = 0;
$jumlah_iklan_terupload = 0;
$result = null;

// Ambil username dari session
$username = $_SESSION['username'];

// Cari vendor_id berdasarkan username
$query_vendor = "SELECT vendor_id FROM business_account WHERE username = '$username'";
$result_vendor = $koneksi->query($query_vendor);

if ($result_vendor && $result_vendor->num_rows > 0) {
    $row_vendor = $result_vendor->fetch_assoc();
    $vendor_id = $row_vendor['vendor_id']; 

    // Ambil jumlah total stok iklan slider3 dari semua paket aktif yang dimiliki oleh vendor
    $query_paket = "SELECT SUM(p.jumlah_iklan_slider3) AS total_slider3
                    FROM pembelian_paket_ads ppa
                    JOIN paket_ads p ON ppa.paket_id = p.paket_id
                    WHERE ppa.vendor_id = '$vendor_id' AND ppa.status_pembayaran = 'paid' AND ppa.status_iklan = 'active'";
    $result_paket = $koneksi->query($query_paket);
    
    if ($result_paket && $result_paket->num_rows > 0) {
        $row_paket = $result_paket->fetch_assoc();
        $stok_iklan_slider3 = $row_paket['total_slider3'];
    }

    // Hitung jumlah iklan slider3 yang sudah diupload oleh vendor
    $query_jumlah_iklan = "SELECT COUNT(*) as jumlah_iklan FROM banner_ads WHERE vendor_id = '$vendor_id' AND banner_type = 'slider3'";
    $result_jumlah_iklan = $koneksi->query($query_jumlah_iklan);
    
    if ($result_jumlah_iklan && $result_jumlah_iklan->num_rows > 0) {
        $row_jumlah_iklan = $result_jumlah_iklan->fetch_assoc();
        $jumlah_iklan_terupload = $row_jumlah_iklan['jumlah_iklan'];
    }

    // Proses penambahan iklan jika form disubmit dan slot tersedia
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && $jumlah_iklan_terupload < $stok_iklan_slider3) {
        $target_dir = "uploads/"; 
        $original_file_name = basename($_FILES["image"]["name"]);
        $imageFileType = strtolower(pathinfo($original_file_name, PATHINFO_EXTENSION));

        $new_file_name = pathinfo($original_file_name, PATHINFO_FILENAME) . "_" . $vendor_id . "_" . time() . "." . $imageFileType;
        $target_file = $target_dir . $new_file_name;

        $uploadOk = 1;

        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if ($check !== false) {
            $uploadOk = 1;
        } else {
            $pesan_error = "File bukan gambar.";
            $uploadOk = 0;
        }

        if ($_FILES["image"]["size"] > 5000000) {
            $pesan_error = "Maaf, ukuran file terlalu besar.";
            $uploadOk = 0;
        }

        if ($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg" && $imageFileType != "gif") {
            $pesan_error = "Maaf, hanya format JPG, JPEG, PNG & GIF yang diizinkan.";
            $uploadOk = 0;
        }

        if ($uploadOk == 0) {
            $pesan_error = "Maaf, gambar gagal diunggah. " . $pesan_error;
        } else {
            if (move_uploaded_file($_FILES["image"]["tmp_name"], $target_file)) {
                $image_url = $target_file;
                $title = $_POST['title'];
                $description = $_POST['description'];
                $button_text = $_POST['button_text'];
                $link_url = $_POST['link_url'];

                $sql = "INSERT INTO banner_ads (vendor_id, banner_type, image_url, title, description, button_text, link_url) 
                        VALUES ('$vendor_id', 'slider3', '$image_url', '$title', '$description', '$button_text', '$link_url')";

                if ($koneksi->query($sql) === TRUE) {
                    $iklan_berhasil = true;
                    $jumlah_iklan_terupload++; 
                } else {
                    $pesan_error = "Error: " . $sql . "<br>" . $koneksi->error;
                }
            } else {
                $pesan_error = "Maaf, terjadi kesalahan saat mengunggah file Anda.";
            }
        }
    }

    $sql = "SELECT * FROM banner_ads WHERE banner_type = 'slider3' AND vendor_id = '$vendor_id'";
    $result = $koneksi->query($sql);
} else {
    $pesan_error = "Vendor tidak ditemukan untuk username: " . $username;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Iklan Banner 3 (Slider 3)</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <link rel="stylesheet" href="../css/style.nav.css">
    <link rel="stylesheet" href="style.kelola.iklan.3.css ">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <script>
        function confirmDelete(bannerId) {
            Swal.fire({
                title: 'Apakah Anda yakin?',
                text: "Anda tidak akan bisa mengembalikan data ini!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Ya, hapus!',
                cancelButtonText: 'Batal'
            }).then((result) => {
                if (result.isConfirmed) {
                    window.location.href = `delete_banner3.php?id=${bannerId}`;
                }
            })
        }

        function showForm() {
            document.getElementById('form-container').style.display = 'block'; 
            document.getElementById('content-section').style.display = 'none'; 
            document.getElementById('tambah-iklan-btn').style.display = 'none'; 
        }

        function hideForm() {
            document.getElementById('form-container').style.display = 'none'; 
            document.getElementById('content-section').style.display = 'block'; 
            document.getElementById('tambah-iklan-btn').style.display = 'block'; 
        }
    </script>
</head>
<body>
<?php include ("../container_content/nav.php")?>

<div class="container">
    <h2>Kelola Iklan Banner 3 (Slider 3)</h2>

    <div class="info">
        <p>Anda memiliki <strong><?= $stok_iklan_slider3 ?></strong> slot untuk iklan slider3.</p>
        <p>Anda telah menggunakan <strong id="jumlah-terupload"><?= $jumlah_iklan_terupload ?></strong> dari total slot yang tersedia.</p>
    </div>

    <div id="form-container" class="form-container hidden" style="display: none;">
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
            <button type="button" class="btn" onclick="hideForm()">Kembali</button>
        </form>
    </div>

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
            if ($result && $result->num_rows > 0) {
                while($row = $result->fetch_assoc()) {
                    echo "<tr>
                            <td>{$row['banner_id']}</td>
                            <td><img src='{$row['image_url']}' alt='Iklan'></td>
                            <td>{$row['title']}</td>
                            <td>{$row['description']}</td>
                            <td><a href='{$row['link_url']}' target='_blank'>Buka Link</a></td>
                            <td class='action-icons'>
                                <a href='edit_banner3.php?id={$row['banner_id']}'><i class='fa-regular fa-pen-to-square'></i></a> | 
                                <a href='javascript:void(0)' onclick='confirmDelete({$row['banner_id']})'><i class='fa-solid fa-trash-can'></i></a>
                            </td>
                        </tr>";
                }
            } else {
                echo "<tr><td colspan='6'>Tidak ada data iklan ditemukan.</td></tr>";
            }
            ?>
        </table>

    <button class="btn" id="tambah-iklan-btn" onclick="showForm()" <?= ($jumlah_iklan_terupload >= $stok_iklan_slider3) ? 'style="display:none;"' : ''; ?>>Tambah Iklan</button>

    </div>

    <a href="index.php" class="btn-back">Kembali ke Halaman Utama</a>

</div>

<?php
if ($iklan_berhasil) {
    echo "<script>
            Swal.fire({
                title: 'Berhasil!',
                text: 'Iklan baru berhasil ditambahkan.',
                icon: 'success',
                confirmButtonText: 'OK'
            });
          </script>";
} elseif (!empty($pesan_error)) {
    echo "<script>
            Swal.fire({
                title: 'Gagal!',
                text: '$pesan_error',
                icon: 'error',
                confirmButtonText: 'OK'
            });
          </script>";
}
?>

</body>
</html>

<?php $koneksi->close(); ?>
