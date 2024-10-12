<?php
// Include konfigurasi database
require "../config/config.php";

// Mulai session
session_start();

// Cek apakah form login telah dikirim
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil username dan password dari form
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Periksa apakah username dan password diisi
    if (empty($username) || empty($password)) {
        die("Username dan password harus diisi.");
    }

    // Buat instance MySQLi untuk koneksi ke database
    $mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

    // Cek apakah ada error pada koneksi
    if ($mysqli->connect_error) {
        die("Koneksi gagal: " . $mysqli->connect_error);
    }

    // Siapkan pernyataan SQL untuk mengambil data user termasuk password yang di-hash dan tipe akun
    $stmt = $mysqli->prepare("SELECT account_id, password, type_account FROM account WHERE username = ?");
    if (!$stmt) {
        die("Kesalahan SQL: " . $mysqli->error);
    }

    // Bind parameter dan eksekusi pernyataan
    $stmt->bind_param("s", $username);
    $stmt->execute();

    // Simpan hasil query
    $stmt->store_result();

    // Cek apakah user ada
    if ($stmt->num_rows > 0) {
        // Ambil hasil dari query
        $stmt->bind_result($id, $hashed_password, $type_account);
        $stmt->fetch();

        // Verifikasi password
        if (password_verify($password, $hashed_password)) {
            // Simpan data penting ke dalam session
            $_SESSION['account_id'] = $id;        // Simpan account_id
            $_SESSION['username'] = $username;    // Simpan username
            $_SESSION['type_account'] = $type_account; // Simpan tipe akun (user/vendor)

            // Redirect ke halaman home setelah login berhasil
            header("Location: ../index.php");
            exit;
        } else {
            // Jika password tidak valid
            echo "Password salah.";
        }
    } else {
        // Jika username tidak ditemukan
        echo "Username salah.";
    }

    // Tutup pernyataan dan koneksi
    $stmt->close();
    $mysqli->close();
}
?>
