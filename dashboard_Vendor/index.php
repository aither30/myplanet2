<?php
session_start();
include('../config/config.php');

// Pastikan user telah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

$username_vendor = $_SESSION['username'];

// Ambil informasi vendor berdasarkan username
$query_vendor = "SELECT * FROM business_account WHERE username = '$username_vendor'";
$result_vendor = $koneksi->query($query_vendor);
$vendor = $result_vendor->fetch_assoc();

// Fungsi untuk menampilkan section berdasarkan halaman
function display_section($section, $koneksi, $vendor_id) {
    global $vendor, $username_vendor;

    switch ($section) {
        // PROFIL
        case 'profil':
            echo "
            <div class='profile-container'>
                <div class='profile-header'>
                    <img src='" . $vendor['photo'] . "' alt='Foto Sampul' class='cover-photo'>
                    <div class='profile-logo'>
                        <img src='" . $vendor['logo'] . "' alt='Logo Perusahaan' class='logo'>
                    </div>
                </div>
                <div class='profile-info'>
                    <h1>" . $vendor['name_owner'] . "</h1>
                    <p class='company'>" . $vendor['company_name'] . "</p>
                    <p class='address'><i class='fas fa-map-marker-alt'></i> " . $vendor['address'] . ", " . $vendor['city'] . ", " . $vendor['province'] . "</p>
                    <a href='index.php?section=edit_profil' class='edit-profile-btn'>Edit Profil</a>
                </div>
                <div class='profile-details'>
                    <h3>Informasi Kontak</h3>
                    <div class='details-grid'>
                        <div class='details-card'>
                            <p><strong>Telepon Vendor:</strong> " . $vendor['phone_vendor'] . "</p>
                        </div>
                        <div class='details-card'>
                            <p><strong>Email Vendor:</strong> " . $vendor['email'] . "</p>
                        </div>
                        <div class='details-card'>
                            <p><strong>Telepon Pemilik:</strong> " . $vendor['phone_owner'] . "</p>
                        </div>
                        <div class='details-card'>
                            <p><strong>Email Pemilik:</strong> " . $vendor['email_owner'] . "</p>
                        </div>
                        <div class='details-card'>
                            <p><strong>Jenis Bisnis:</strong> " . $vendor['jenis_bisnis'] . "</p>
                        </div>
                        <div class='details-card'>
                            <p><strong>Tanggal Operasional:</strong> " . $vendor['date_operasional'] . "</p>
                        </div>
                    </div>
                </div>
                <div class='profile-extras'>
                    <h3>Informasi Tambahan</h3>
                    <p><strong>Rekening:</strong> " . $vendor['rekening'] . "</p>
                    <p><strong>Alamat Peta:</strong> " . $vendor['map_address'] . "</p>
                    <p><strong>Deskripsi:</strong> " . $vendor['description'] . "</p>
                </div>
            </div>";
            break;

        case 'edit_profil':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $name_owner = $_POST['name_owner'];
                $company_name = $_POST['company_name'];
                $phone_vendor = $_POST['phone_vendor'];
                $email = $_POST['email'];
                $phone_owner = $_POST['phone_owner'];
                $email_owner = $_POST['email_owner'];
                $address = $_POST['address'];
                $jenis_bisnis = $_POST['jenis_bisnis'];
                $date_operasional = $_POST['date_operasional'];
                $rekening = $_POST['rekening'];
                $description = $_POST['description'];

                $logo = $vendor['logo'];
                if (isset($_FILES['logo']) && $_FILES['logo']['error'] == 0) {
                    $logo_filename = basename($_FILES['logo']['name']);
                    $logo = 'uploads/' . $logo_filename;
                    move_uploaded_file($_FILES['logo']['tmp_name'], $logo);
                }

                $photo = $vendor['photo'];
                if (isset($_FILES['photo']) && $_FILES['photo']['error'] == 0) {
                    $photo_filename = basename($_FILES['photo']['name']);
                    $photo = 'uploads/' . $photo_filename;
                    move_uploaded_file($_FILES['photo']['tmp_name'], $photo);
                }

                $query_update = "UPDATE business_account SET name_owner='$name_owner', company_name='$company_name', phone_vendor='$phone_vendor', email='$email', phone_owner='$phone_owner', email_owner='$email_owner', address='$address', jenis_bisnis='$jenis_bisnis', date_operasional='$date_operasional', rekening='$rekening', description='$description', logo='$logo', photo='$photo' WHERE vendor_id=$vendor_id";

                if ($koneksi->query($query_update)) {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Profil Berhasil Diperbarui!',
                            text: 'Data profil Anda telah berhasil disimpan.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?section=profil'; 
                            }
                        });
                    </script>";
                } else {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Gagal Memperbarui Profil',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
            }

            echo "
            <div class='edit-profile-container'>
                <h2>Edit Profil</h2>
                <form method='POST' enctype='multipart/form-data'>
                    <label for='name_owner'>Nama Pemilik:</label>
                    <input type='text' name='name_owner' id='name_owner' value='" . $vendor['name_owner'] . "' required>

                    <label for='company_name'>Nama Perusahaan:</label>
                    <input type='text' name='company_name' id='company_name' value='" . $vendor['company_name'] . "' required>

                    <label for='phone_vendor'>Telepon Vendor:</label>
                    <input type='text' name='phone_vendor' id='phone_vendor' value='" . $vendor['phone_vendor'] . "' required>

                    <label for='email'>Email Vendor:</label>
                    <input type='email' name='email' id='email' value='" . $vendor['email'] . "' required>

                    <label for='phone_owner'>Telepon Pemilik:</label>
                    <input type='text' name='phone_owner' id='phone_owner' value='" . $vendor['phone_owner'] . "' required>

                    <label for='email_owner'>Email Pemilik:</label>
                    <input type='email' name='email_owner' id='email_owner' value='" . $vendor['email_owner'] . "' required>

                    <label for='address'>Alamat:</label>
                    <textarea name='address' id='address' required>" . $vendor['address'] . "</textarea>

                    <label for='jenis_bisnis'>Jenis Bisnis:</label>
                    <input type='text' name='jenis_bisnis' id='jenis_bisnis' value='" . $vendor['jenis_bisnis'] . "' required>

                    <label for='date_operasional'>Tanggal Operasional:</label>
                    <input type='date' name='date_operasional' id='date_operasional' value='" . $vendor['date_operasional'] . "' required>

                    <label for='rekening'>Rekening:</label>
                    <input type='text' name='rekening' id='rekening' value='" . $vendor['rekening'] . "'>

                    <label for='description'>Deskripsi:</label>
                    <textarea name='description' id='description' required>" . $vendor['description'] . "</textarea>

                    <label for='logo'>Logo Perusahaan:</label>
                    <input type='file' name='logo' id='logo'>

                    <label for='photo'>Foto Profil (Sampul):</label>
                    <input type='file' name='photo' id='photo'>

                    <input type='submit' value='Simpan Perubahan'>
                </form>
            </div>";
            break;

        // PRODUK
        case 'product':
            echo "<h2>Daftar Produk</h2>";
            echo "<a href='index.php?section=add_product' class='button-link'>Tambah Produk</a><br><br>";

            $query_product = "SELECT * FROM product WHERE vendor_id = $vendor_id";
            $result_product = $koneksi->query($query_product);

            echo "<table border='1'>
                    <tr>
                        <th>Nama Produk</th>
                        <th>Jenis Produk</th>
                        <th>Harga</th>
                        <th>Stok</th>
                        <th>Deskripsi</th>
                        <th>Spesifikasi</th>
                        <th>Gambar</th>
                        <th>Aksi</th>
                    </tr>";

            while ($row = $result_product->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['name'] . "</td>
                        <td>" . $row['jenis_product'] . "</td>
                        <td>" . $row['prices'] . "</td>
                        <td>" . $row['stocks'] . "</td>
                        <td>" . $row['description'] . "</td>
                        <td>" . $row['spesifikasi'] . "</td>
                        <td><img src='" . $row['images'] . "' alt='Gambar Produk' width='100'></td>
                        <td>
                            <a href='index.php?section=edit_product&id=" . $row['product_id'] . "'>Edit</a> |
                            <a href='index.php?section=delete_product&id=" . $row['product_id'] . "' onclick='return confirm(\"Yakin ingin menghapus produk ini?\")'>Hapus</a>
                        </td>
                      </tr>";
            }
            echo "</table>";
            break;

        case 'add_product':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $jenis_product = $_POST['jenis_product'];
                $name = $_POST['name'];
                $prices = $_POST['prices'];
                $description = $_POST['description'];
                $spesifikasi = $_POST['spesifikasi'];
                $stocks = $_POST['stocks'];

                $images = null;
                if (isset($_FILES['images']) && $_FILES['images']['error'] == 0) {
                    $image_filename = basename($_FILES['images']['name']);
                    $images = 'uploads/' . $image_filename;
                    move_uploaded_file($_FILES['images']['tmp_name'], $images);
                }

                $query_add = "INSERT INTO product (vendor_id, jenis_product, name, prices, description, spesifikasi, images, stocks)
                              VALUES ($vendor_id, '$jenis_product', '$name', '$prices', '$description', '$spesifikasi', '$images', '$stocks')";

                if ($koneksi->query($query_add)) {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Produk Berhasil Ditambahkan!',
                            text: 'Data produk Anda telah berhasil disimpan.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?section=product'; 
                            }
                        });
                    </script>";
                } else {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Gagal Menambahkan Produk',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
            }

            echo "
            <div class='add-product-container'>
                <h2>Tambah Produk Baru</h2>
                <form method='POST' enctype='multipart/form-data'>
                    <label for='jenis_product'>Jenis Produk:</label>
                    <input type='text' name='jenis_product' id='jenis_product' required>

                    <label for='name'>Nama Produk:</label>
                    <input type='text' name='name' id='name' required>

                    <label for='prices'>Harga Produk:</label>
                    <input type='number' name='prices' id='prices' step='0.01' required>

                    <label for='description'>Deskripsi Produk:</label>
                    <textarea name='description' id='description'></textarea>

                    <label for='spesifikasi'>Spesifikasi Produk:</label>
                    <textarea name='spesifikasi' id='spesifikasi'></textarea>

                    <label for='stocks'>Stok Produk:</label>
                    <input type='number' name='stocks' id='stocks' required>

                    <label for='images'>Gambar Produk:</label>
                    <input type='file' name='images' id='images'>

                    <input type='submit' value='Tambah Produk'>
                </form>
            </div>";
            break;

        case 'edit_product':
            $product_id = $_GET['id'];
            $query_product = "SELECT * FROM product WHERE product_id = $product_id";
            $result_product = $koneksi->query($query_product);
            $product = $result_product->fetch_assoc();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $jenis_product = $_POST['jenis_product'];
                $name = $_POST['name'];
                $prices = $_POST['prices'];
                $description = $_POST['description'];
                $spesifikasi = $_POST['spesifikasi'];
                $stocks = $_POST['stocks'];

                $images = $product['images'];
                if (isset($_FILES['images']) && $_FILES['images']['error'] == 0) {
                    $image_filename = basename($_FILES['images']['name']);
                    $images = 'uploads/' . $image_filename;
                    move_uploaded_file($_FILES['images']['tmp_name'], $images);
                }

                $query_update = "UPDATE product SET jenis_product='$jenis_product', name='$name', prices='$prices', description='$description', spesifikasi='$spesifikasi', images='$images', stocks='$stocks' WHERE product_id=$product_id";

                if ($koneksi->query($query_update)) {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Produk Berhasil Diperbarui!',
                            text: 'Data produk Anda telah berhasil diperbarui.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?section=product'; 
                            }
                        });
                    </script>";
                } else {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Gagal Memperbarui Produk',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
            }

            echo "
            <div class='edit-product-container'>
                <h2>Edit Produk</h2>
                <form method='POST' enctype='multipart/form-data'>
                    <label for='jenis_product'>Jenis Produk:</label>
                    <input type='text' name='jenis_product' id='jenis_product' value='" . $product['jenis_product'] . "' required>

                    <label for='name'>Nama Produk:</label>
                    <input type='text' name='name' id='name' value='" . $product['name'] . "' required>

                    <label for='prices'>Harga Produk:</label>
                    <input type='number' name='prices' id='prices' value='" . $product['prices'] . "' step='0.01' required>

                    <label for='description'>Deskripsi Produk:</label>
                    <textarea name='description' id='description'>" . $product['description'] . "</textarea>

                    <label for='spesifikasi'>Spesifikasi Produk:</label>
                    <textarea name='spesifikasi' id='spesifikasi'>" . $product['spesifikasi'] . "</textarea>

                    <label for='stocks'>Stok Produk:</label>
                    <input type='number' name='stocks' id='stocks' value='" . $product['stocks'] . "' required>

                    <label for='images'>Gambar Produk:</label>
                    <input type='file' name='images' id='images'>
                    <p>Gambar saat ini: <img src='" . $product['images'] . "' alt='Gambar Produk' width='100'></p>

                    <input type='submit' value='Simpan Perubahan'>
                </form>
            </div>";
            break;

        case 'delete_product':
            $product_id = $_GET['id'];
            $query_delete = "DELETE FROM product WHERE product_id=$product_id";

            if ($koneksi->query($query_delete)) {
                echo "
                <script>
                    Swal.fire({
                        title: 'Produk Berhasil Dihapus!',
                        text: 'Produk Anda telah dihapus.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php?section=product'; 
                        }
                    });
                </script>";
            } else {
                echo "
                <script>
                    Swal.fire({
                        title: 'Gagal Menghapus Produk',
                        text: 'Terjadi kesalahan, silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
            break;

        // PORTOFOLIO
        case 'portofolio':
            echo "<h2>Daftar Portofolio</h2>";
            echo "<a href='index.php?section=add_portofolio' class='button-link'>Tambah Portofolio</a><br><br>";

            $query_portofolio = "SELECT * FROM portofolio WHERE vendor_id = $vendor_id";
            $result_portofolio = $koneksi->query($query_portofolio);

            echo "<table border='1'>
                    <tr>
                        <th>Deskripsi</th>
                        <th>Link Portofolio</th>
                        <th>Aksi</th>
                    </tr>";

            while ($row = $result_portofolio->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['description'] . "</td>
                        <td><a href='" . $row['link_portofolio'] . "' target='_blank'>" . $row['link_portofolio'] . "</a></td>
                        <td>
                            <a href='index.php?section=edit_portofolio&id=" . $row['portofolio_id'] . "'>Edit</a> |
                            <a href='index.php?section=delete_portofolio&id=" . $row['portofolio_id'] . "' onclick='return confirm(\"Yakin ingin menghapus portofolio ini?\")'>Hapus</a>
                        </td>
                      </tr>";
            }
            echo "</table>";
            break;

        case 'add_portofolio':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $description = $_POST['description'];
                $link_portofolio = $_POST['link_portofolio'];

                $query_add = "INSERT INTO portofolio (vendor_id, description, link_portofolio) VALUES ($vendor_id, '$description', '$link_portofolio')";

                if ($koneksi->query($query_add)) {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Portofolio Berhasil Ditambahkan!',
                            text: 'Data portofolio Anda telah berhasil disimpan.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?section=portofolio'; 
                            }
                        });
                    </script>";
                } else {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Gagal Menambahkan Portofolio',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
            }

            echo "
            <div class='add-product-container'>
                <h2>Tambah Portofolio Baru</h2>
                <form method='POST'>
                    <label for='description'>Deskripsi:</label>
                    <input type='text' name='description' id='description' required>

                    <label for='link_portofolio'>Link Portofolio:</label>
                    <input type='text' name='link_portofolio' id='link_portofolio' required>

                    <input type='submit' value='Tambah Portofolio'>
                </form>
            </div>";
            break;

        case 'edit_portofolio':
            $portofolio_id = $_GET['id'];
            $query_portofolio = "SELECT * FROM portofolio WHERE portofolio_id = $portofolio_id";
            $result_portofolio = $koneksi->query($query_portofolio);
            $portofolio = $result_portofolio->fetch_assoc();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $description = $_POST['description'];
                $link_portofolio = $_POST['link_portofolio'];

                $query_update = "UPDATE portofolio SET description='$description', link_portofolio='$link_portofolio' WHERE portofolio_id=$portofolio_id";

                if ($koneksi->query($query_update)) {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Portofolio Berhasil Diperbarui!',
                            text: 'Data portofolio Anda telah berhasil diperbarui.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?section=portofolio'; 
                            }
                        });
                    </script>";
                } else {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Gagal Memperbarui Portofolio',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
            }

            echo "
            <div class='edit-product-container'>
                <h2>Edit Portofolio</h2>
                <form method='POST'>
                    <label for='description'>Deskripsi:</label>
                    <input type='text' name='description' id='description' value='" . $portofolio['description'] . "' required>

                    <label for='link_portofolio'>Link Portofolio:</label>
                    <input type='text' name='link_portofolio' id='link_portofolio' value='" . $portofolio['link_portofolio'] . "' required>

                    <input type='submit' value='Simpan Perubahan'>
                </form>
            </div>";
            break;

        case 'delete_portofolio':
            $portofolio_id = $_GET['id'];
            $query_delete = "DELETE FROM portofolio WHERE portofolio_id=$portofolio_id";

            if ($koneksi->query($query_delete)) {
                echo "
                <script>
                    Swal.fire({
                        title: 'Portofolio Berhasil Dihapus!',
                        text: 'Data portofolio Anda telah dihapus.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php?section=portofolio'; 
                        }
                    });
                </script>";
            } else {
                echo "
                <script>
                    Swal.fire({
                        title: 'Gagal Menghapus Portofolio',
                        text: 'Terjadi kesalahan, silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
            break;

        // FAQ
        case 'faq':
            echo "<h2>Daftar FAQ</h2>";
            echo "<a href='index.php?section=add_faq' class='button-link'>Tambah FAQ</a><br><br>";

            $query_faq = "SELECT * FROM faq WHERE vendor_id = $vendor_id";
            $result_faq = $koneksi->query($query_faq);

            echo "<table border='1'>
                    <tr>
                        <th>Pertanyaan</th>
                        <th>Jawaban</th>
                        <th>Aksi</th>
                    </tr>";

            while ($row = $result_faq->fetch_assoc()) {
                echo "<tr>
                        <td>" . $row['question'] . "</td>
                        <td>" . $row['answer'] . "</td>
                        <td>
                            <a href='index.php?section=edit_faq&id=" . $row['faq_id'] . "'>Edit</a> |
                            <a href='index.php?section=delete_faq&id=" . $row['faq_id'] . "' onclick='return confirm(\"Yakin ingin menghapus FAQ ini?\")'>Hapus</a>
                        </td>
                      </tr>";
            }
            echo "</table>";
            break;

        case 'add_faq':
            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $question = $_POST['question'];
                $answer = $_POST['answer'];

                $query_add = "INSERT INTO faq (vendor_id, question, answer) VALUES ($vendor_id, '$question', '$answer')";

                if ($koneksi->query($query_add)) {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'FAQ Berhasil Ditambahkan!',
                            text: 'Data FAQ Anda telah berhasil disimpan.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?section=faq'; 
                            }
                        });
                    </script>";
                } else {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Gagal Menambahkan FAQ',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
            }

            echo "
            <div class='add-product-container'>
                <h2>Tambah FAQ Baru</h2>
                <form method='POST'>
                    <label for='question'>Pertanyaan:</label>
                    <input type='text' name='question' id='question' required>

                    <label for='answer'>Jawaban:</label>
                    <textarea name='answer' id='answer' required></textarea>

                    <input type='submit' value='Tambah FAQ'>
                </form>
            </div>";
            break;

        case 'edit_faq':
            $faq_id = $_GET['id'];
            $query_faq = "SELECT * FROM faq WHERE faq_id = $faq_id";
            $result_faq = $koneksi->query($query_faq);
            $faq = $result_faq->fetch_assoc();

            if ($_SERVER['REQUEST_METHOD'] == 'POST') {
                $question = $_POST['question'];
                $answer = $_POST['answer'];

                $query_update = "UPDATE faq SET question='$question', answer='$answer' WHERE faq_id=$faq_id";

                if ($koneksi->query($query_update)) {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'FAQ Berhasil Diperbarui!',
                            text: 'Data FAQ Anda telah berhasil diperbarui.',
                            icon: 'success',
                            confirmButtonText: 'OK'
                        }).then((result) => {
                            if (result.isConfirmed) {
                                window.location.href = 'index.php?section=faq'; 
                            }
                        });
                    </script>";
                } else {
                    echo "
                    <script>
                        Swal.fire({
                            title: 'Gagal Memperbarui FAQ',
                            text: 'Terjadi kesalahan, silakan coba lagi.',
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    </script>";
                }
            }

            echo "
            <div class='edit-product-container'>
                <h2>Edit FAQ</h2>
                <form method='POST'>
                    <label for='question'>Pertanyaan:</label>
                    <input type='text' name='question' id='question' value='" . $faq['question'] . "' required>

                    <label for='answer'>Jawaban:</label>
                    <textarea name='answer' id='answer' required>" . $faq['answer'] . "</textarea>

                    <input type='submit' value='Simpan Perubahan'>
                </form>
            </div>";
            break;

        case 'delete_faq':
            $faq_id = $_GET['id'];
            $query_delete = "DELETE FROM faq WHERE faq_id=$faq_id";

            if ($koneksi->query($query_delete)) {
                echo "
                <script>
                    Swal.fire({
                        title: 'FAQ Berhasil Dihapus!',
                        text: 'Data FAQ Anda telah dihapus.',
                        icon: 'success',
                        confirmButtonText: 'OK'
                    }).then((result) => {
                        if (result.isConfirmed) {
                            window.location.href = 'index.php?section=faq'; 
                        }
                    });
                </script>";
            } else {
                echo "
                <script>
                    Swal.fire({
                        title: 'Gagal Menghapus FAQ',
                        text: 'Terjadi kesalahan, silakan coba lagi.',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                </script>";
            }
            break;

        // HASIL PENJUALAN
        case 'sales':
            echo "<h2>Hasil Penjualan</h2>";

            $query_sales = "SELECT i.invoice_id, i.product_name, i.total_price, i.user_name as buyer, i.created_at as payment_date FROM invoice i JOIN product p ON i.product_name = p.name WHERE p.vendor_id = $vendor_id";
            $result_sales = $koneksi->query($query_sales);

            if ($result_sales->num_rows > 0) {
                echo "<table border='1'>
                        <tr>
                            <th>ID Invoice</th>
                            <th>Nama Produk</th>
                            <th>Total Harga</th>
                            <th>Pembeli</th>
                            <th>Tanggal Transaksi</th>
                        </tr>";

                while ($row = $result_sales->fetch_assoc()) {
                    echo "<tr>
                            <td>" . $row['invoice_id'] . "</td>
                            <td>" . $row['product_name'] . "</td>
                            <td>Rp " . number_format($row['total_price'], 0, ',', '.') . "</td>
                            <td>" . $row['buyer'] . "</td>
                            <td>" . date('d-m-Y', strtotime($row['payment_date'])) . "</td>
                          </tr>";
                }
                echo "</table>";
            } else {
                echo "<p>Tidak ada penjualan yang tercatat.</p>";
            }
            break;
    }
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="styles.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.6.0/css/all.min.css" integrity="sha512-Kc323vGBEqzTmouAECnVceyQqyqdsSiqLQISBL29aUW4U/M7pSPA/gEUZQqv1cwx4OnYxTxve5UMg5GT6L4JJg==" crossorigin="anonymous" referrerpolicy="no-referrer" />
    <script src="//cdn.jsdelivr.net/npm/sweetalert2@11"></script> <!-- SweetAlert2 -->
    <title>Dashboard Vendor</title>
</head>
<body>
    <?php include ("../container_content/nav.php") ?>
    <div class="sidebar">
        <ul>
            <li><a href="index.php?section=profil">Profil</a></li>
            <li><a href="index.php?section=product">Produk</a></li>
            <li><a href="index.php?section=sales">Hasil Penjualan</a></li>
            <li><a href="index.php?section=invoice">Daftar Invoice</a></li>
            <li><a href="index.php?section=transaction">Daftar Transaksi</a></li>
            <li><a href="index.php?section=portofolio">Portofolio</a></li>
            <li><a href="index.php?section=faq">FAQ</a></li>
        </ul>
    </div>
    
    <div class="content">
        <?php
        $section = isset($_GET['section']) ? $_GET['section'] : 'profil';
        display_section($section, $koneksi, $vendor['vendor_id']);
        ?>
    </div>
</body>
</html>
