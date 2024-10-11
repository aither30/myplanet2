<?php
session_start();
include ("../config/config.php");

// Pastikan pengguna sudah login dan username tersedia di session
if (!isset($_SESSION['username'])) {
    header("Location: ../home.php"); // Redirect ke halaman login jika pengguna belum login
    exit(); // Menghentikan eksekusi script lebih lanjut
}

$username = $_SESSION['username']; // Ambil username dari session

// Ambil data pengguna berdasarkan username
$sql = "SELECT * FROM user_account WHERE username = ?";
$stmt = $koneksi->prepare($sql);
$stmt->bind_param("s", $username);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // Ambil data pengguna
} else {
    echo "Pengguna tidak ditemukan."; // Jika tidak ada pengguna yang cocok dengan username
    exit();
}

// Proses update profil
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $phone = $_POST['phone'];
    $gender = $_POST['gender'];
    $usia = $_POST['usia'];
    $institusi_afiliasi = $_POST['institusi_afiliasi'];
    $event_preference = $_POST['event_preference'];
    $budget = $_POST['budget'];
    $country = $_POST['country'];
    $province = $_POST['province'];
    $city = $_POST['city'];
    $district = $_POST['district'];
    $rt = $_POST['rt'];
    $rw = $_POST['rw'];
    $postal_code = $_POST['postal_code'];
    $address = $_POST['address']; // Tambahkan alamat

    // Jika ada gambar profil yang diunggah
    $profile_image = $user['photo']; // Default value dari database
    if (!empty($_FILES['profile_image']['name'])) {
        $profile_image = 'uploads/' . basename($_FILES['profile_image']['name']);
        move_uploaded_file($_FILES['profile_image']['tmp_name'], $profile_image);
    }

    // Jika ada gambar sampul yang diunggah
    $cover_image = $user['cover_image']; // Default value dari database
    if (!empty($_FILES['cover_image']['name'])) {
        $cover_image = 'uploads/' . basename($_FILES['cover_image']['name']);
        move_uploaded_file($_FILES['cover_image']['tmp_name'], $cover_image);
    }

    // Update data di database
    $update_sql = "UPDATE user_account 
                   SET name = ?, email = ?, phone = ?, gender = ?, usia = ?, 
                       institusi_afiliasi = ?, event_preference = ?, budget = ?, 
                       photo = ?, cover_image = ?, country = ?, province = ?, 
                       city = ?, district = ?, rt = ?, rw = ?, postal_code = ?, address = ?
                   WHERE username = ?";
    $stmt = $koneksi->prepare($update_sql);
    $stmt->bind_param("ssssissssssssssssss", $name, $email, $phone, $gender, $usia, 
                      $institusi_afiliasi, $event_preference, $budget, 
                      $profile_image, $cover_image, $country, $province, $city, 
                      $district, $rt, $rw, $postal_code, $address, $username);

                      if ($stmt->execute() === TRUE) {
                        echo "<script>
                            Swal.fire({
                                title: 'Profil berhasil diperbarui!',
                                text: 'Perubahan telah disimpan dengan sukses.',
                                icon: 'success',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    title: 'custom-swal-title',
                                    confirmButton: 'custom-swal-button',
                                    text: 'custom-swal-text'
                                }
                            }).then((result) => {
                                if (result.isConfirmed) {
                                    window.location.reload();
                                }
                            });
                        </script>";
                    } else {
                        echo "<script>
                            Swal.fire({
                                title: 'Gagal memperbarui profil!',
                                text: 'Terjadi kesalahan saat menyimpan perubahan.',
                                icon: 'error',
                                confirmButtonText: 'OK',
                                customClass: {
                                    popup: 'custom-swal-popup',
                                    title: 'custom-swal-title',
                                    confirmButton: 'custom-swal-button',
                                    text: 'custom-swal-text'
                                }
                            });
                        </script>";
                    }
                    
}

// Ambil user_id dari tabel user_account berdasarkan username
$userId = $user['user_id']; // Dari hasil query di atas

// Query untuk mengambil riwayat transaksi lengkap dengan detail produk
$transQuery = "
    SELECT t.transaction_id, t.payment_method, t.amount, t.payment_date, t.status, 
           i.product_name, i.product_quantity, i.total_price, p.jenis_product, p.images, 
           b.company_name, b.logo 
    FROM transaction t
    JOIN invoice i ON t.transaction_id = i.transaction_id
    JOIN product p ON i.product_name = p.name  -- Mengambil berdasarkan nama produk
    JOIN business_account b ON p.vendor_id = b.vendor_id
    WHERE t.user_id = ?
    ORDER BY t.payment_date DESC
";
$stmt = $koneksi->prepare($transQuery);
$stmt->bind_param("i", $userId);
$stmt->execute();
$transResult = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <link rel="stylesheet" href="style.css" />
    <title>Dashboard User</title>
</head>
<body>
    <nav>
        <div class="left_nav">
            <div class="logo">
                <img src="../assets/attribute myplanet/Logo My PlanET.png" alt="My PlanET" />
                <a href="../home.php">My PlanET</a>
            </div>
        </div>
        <div class="mid_nav">
            <div></div>
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

    <!-- Sidebar -->
    <aside>
        <div class="sidebar_menu">
            <button class="active" onclick="showSection('dashboard')">Dashboard</button>
            <button class="active" onclick="showSection('riwayattransaksi')">Riwayat Transaksi</button>
            <button class="active" onclick="showSection('lihatinvoice')">Lihat Invoice</button>
        </div>
    </aside>

    <!-- Main Content -->
    <div class="main_content">
<!-- Dashboard -->
<div id="dashboard" class="content_section">
    <div class="profile-section">
        <!-- Profil Header -->
        <div class="profile-header">
            <div class="cover-image">
                <?php if (!empty($user['cover_image'])): ?>
                    <img src="<?php echo $user['cover_image']; ?>" alt="Sampul Profil" class="cover-photo" />
                <?php else: ?>
                    <img src="default-cover.jpg" alt="Sampul Default" class="cover-photo" />
                <?php endif; ?>
            </div>
            <div class="profile-info">
                <div class="profile-photo">
                    <?php if (!empty($user['photo'])): ?>
                        <img src="<?php echo $user['photo']; ?>" alt="Foto Profil" class="profile-picture" />
                    <?php else: ?>
                        <img src="default-profile.jpg" alt="Foto Default" class="profile-picture" />
                    <?php endif; ?>
                </div>
                <div class="profile-username">
                    <h2><?php echo $user['username']; ?></h2>
                </div>
            </div>
        </div>

        <!-- Profil Details -->
        <div class="profile-details">
            <div class="profile-grid">
                <?php if (!empty($user['name'])): ?>
                    <div class="detail-item">
                        <span class="label">Nama:</span>
                        <span class="value"><?php echo $user['name']; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['email'])): ?>
                    <div class="detail-item">
                        <span class="label">Email:</span>
                        <span class="value"><?php echo $user['email']; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['phone'])): ?>
                    <div class="detail-item">
                        <span class="label">Telepon:</span>
                        <span class="value"><?php echo $user['phone']; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['gender'])): ?>
                    <div class="detail-item">
                        <span class="label">Gender:</span>
                        <span class="value"><?php echo $user['gender']; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['usia'])): ?>
                    <div class="detail-item">
                        <span class="label">Usia:</span>
                        <span class="value"><?php echo $user['usia']; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['institusi_afiliasi'])): ?>
                    <div class="detail-item">
                        <span class="label">Institusi Afiliasi:</span>
                        <span class="value"><?php echo $user['institusi_afiliasi']; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['event_preference'])): ?>
                    <div class="detail-item">
                        <span class="label">Preferensi Event:</span>
                        <span class="value"><?php echo $user['event_preference']; ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['budget'])): ?>
                    <div class="detail-item">
                        <span class="label">Budget:</span>
                        <span class="value">Rp. <?php echo number_format($user['budget'], 2); ?></span>
                    </div>
                <?php endif; ?>

                <?php if (!empty($user['address'])): ?>
                    <div class="detail-item">
                        <span class="label">Alamat:</span>
                        <span class="value"><?php echo nl2br($user['address']); ?></span>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Tombol Edit Profil -->
        <div class="edit-button-container">
            <button class="edit-profile-btn" onclick="showSection('editprofile')">Edit Profil</button>
        </div>
    </div>
</div>


        <!-- Riwayat Transaksi -->
        <div id="riwayattransaksi" class="content_section" style="display: none">
            <h1>Riwayat Transaksi</h1>
            <?php if ($transResult->num_rows > 0): ?>
                <table border="1">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Vendor</th>
                            <th>Pembayaran</th>
                            <th>Status</th>
                            <th>Tanggal</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($transaction = $transResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $transaction['transaction_id']; ?></td>
                                <td>
                                    <strong><?php echo $transaction['product_name']; ?></strong><br>
                                    <small><?php echo $transaction['jenis_product']; ?></small><br>
                                    <img src="../dashboard_Vendor/<?php echo $transaction['images']; ?>" alt="Foto Produk" width="30">
                                </td>
                                <td><?php echo $transaction['product_quantity']; ?></td>
                                <td>Rp <?php echo number_format($transaction['total_price'], 2, ',', '.'); ?></td>
                                <td>
                                    <strong><?php echo $transaction['company_name']; ?></strong><br>
                                    <img src="../dashboard_Vendor/<?php echo $transaction['logo']; ?>" alt="Logo Vendor" width="30">
                                </td>
                                <td>
                                    <strong><?php echo ucfirst($transaction['payment_method']); ?></strong><br>
                                    Rp <?php echo number_format($transaction['amount'], 2, ',', '.'); ?>
                                </td>
                                <td><?php echo ucfirst($transaction['status']); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($transaction['payment_date'])); ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada riwayat transaksi yang ditemukan.</p>
            <?php endif; ?>
        </div>

<!-- Edit Profil -->
<div id="editprofile" class="content_section" style="display: none">
    <h1 class="section-title">Edit Profil</h1>
    <form method="POST" enctype="multipart/form-data" class="form-edit-profile">
        <div class="form-container">
            <!-- Informasi Dasar -->
            <fieldset class="form-section">
                <legend>Informasi Dasar</legend>

                <div class="form-group">
                    <label for="cover_image">Foto Sampul (Opsional)</label>
                    <input type="file" id="cover_image" name="cover_image">
                    <?php if (!empty($user['cover_image'])): ?>
                        <img src="<?php echo $user['cover_image']; ?>" alt="Foto Sampul" class="profile-preview">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="profile_image">Foto Profil (Opsional)</label>
                    <input type="file" id="profile_image" name="profile_image">
                    <?php if (!empty($user['photo'])): ?>
                        <img src="<?php echo $user['photo']; ?>" alt="Foto Profil" class="profile-preview">
                    <?php endif; ?>
                </div>

                <div class="form-group">
                    <label for="name">Nama</label>
                    <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="email">Email</label>
                    <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="phone">Telepon</label>
                    <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="gender">Gender</label>
                    <select id="gender" name="gender" required>
                        <option value="Male" <?php echo ($user['gender'] == 'Male') ? 'selected' : ''; ?>>Male</option>
                        <option value="Female" <?php echo ($user['gender'] == 'Female') ? 'selected' : ''; ?>>Female</option>
                        <option value="Other" <?php echo ($user['gender'] == 'Other') ? 'selected' : ''; ?>>Other</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="usia">Usia</label>
                    <input type="number" id="usia" name="usia" value="<?php echo $user['usia']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="institusi_afiliasi">Institusi Afiliasi</label>
                    <input type="text" id="institusi_afiliasi" name="institusi_afiliasi" value="<?php echo $user['institusi_afiliasi']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="event_preference">Preferensi Event</label>
                    <input type="text" id="event_preference" name="event_preference" value="<?php echo $user['event_preference']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="budget">Budget</label>
                    <input type="number" id="budget" name="budget" value="<?php echo $user['budget']; ?>" required>
                </div>
            </fieldset>

            <!-- Informasi Alamat -->
            <fieldset class="form-section">
                <legend>Informasi Alamat</legend>

                <div class="form-group">
                    <label for="address">Alamat</label>
                    <textarea id="address" name="address" rows="3" required><?php echo $user['address']; ?></textarea>
                </div>

                <div class="form-group">
                    <label for="country">Negara</label>
                    <input type="text" id="country" name="country" value="<?php echo $user['country']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="province">Provinsi</label>
                    <input type="text" id="province" name="province" value="<?php echo $user['province']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="city">Kota</label>
                    <input type="text" id="city" name="city" value="<?php echo $user['city']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="district">Kecamatan</label>
                    <input type="text" id="district" name="district" value="<?php echo $user['district']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="rt">RT</label>
                    <input type="text" id="rt" name="rt" value="<?php echo $user['rt']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="rw">RW</label>
                    <input type="text" id="rw" name="rw" value="<?php echo $user['rw']; ?>" required>
                </div>

                <div class="form-group">
                    <label for="postal_code">Kode Pos</label>
                    <input type="text" id="postal_code" name="postal_code" value="<?php echo $user['postal_code']; ?>" required>
                </div>
            </fieldset>
        </div>
        <button type="submit" class="submit-button">Simpan Perubahan</button>
    </form>
</div>


        <!-- Lihat Invoice -->
        <div id="lihatinvoice" class="content_section" style="display: none;">
            <h1>Daftar Invoice</h1>

            <?php
            // Query untuk mendapatkan semua invoice berdasarkan username
            $invoiceQuery = "SELECT * FROM invoice WHERE user_name = '$username'";
            $invoiceResult = $koneksi->query($invoiceQuery);

            if ($invoiceResult->num_rows > 0): ?>
                <table border="1">
                    <thead>
                        <tr>
                            <th>No. Invoice</th>
                            <th>Produk</th>
                            <th>Jumlah</th>
                            <th>Total Harga</th>
                            <th>Tanggal</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($invoice = $invoiceResult->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo $invoice['invoice_number']; ?></td>
                                <td><?php echo $invoice['product_name']; ?></td>
                                <td><?php echo $invoice['product_quantity']; ?></td>
                                <td>Rp <?php echo number_format($invoice['total_price'], 2, ',', '.'); ?></td>
                                <td><?php echo date('d-m-Y', strtotime($invoice['created_at'])); ?></td>
                                <td>
                                    <?php if ($invoice['pdf_status'] == 'created' && !empty($invoice['pdf_file'])): ?>
                                        <a href="<?php echo $invoice['pdf_file']; ?>" class="download-btn" download>Unduh PDF</a>
                                    <?php elseif ($invoice['pdf_status'] == 'processing'): ?>
                                        <button class="disabled-btn" disabled>Pembuatan PDF Sedang Diproses...</button>
                                    <?php else: ?>
                                        <button class="disabled-btn" disabled>Belum Ada PDF</button>
                                    <?php endif; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p>Tidak ada invoice yang ditemukan.</p>
            <?php endif; ?>
        </div>
    </div>

    <script>
function showSection(sectionId) {
    const sections = document.querySelectorAll(".content_section");
    sections.forEach((section) => {
        section.style.display = "none";
    });

    document.getElementById(sectionId).style.display = "block";

    const buttons = document.querySelectorAll(".sidebar_menu button");
    buttons.forEach((btn) => {
        btn.classList.remove('active');
    });

    event.target.classList.add('active');
}

window.onload = function () {
    showSection("dashboard");
};

    </script>

</body>
</html>
