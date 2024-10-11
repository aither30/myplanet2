<?php
session_start();
include ("../config/config.php");

if (isset($_GET['id'])) {
    $banner_id = intval($_GET['id']);
    
    // Dapatkan vendor_id dari session
    $username = $_SESSION['username'];
    $query_vendor = "SELECT vendor_id FROM business_account WHERE username = '$username'";
    $result_vendor = $koneksi->query($query_vendor);

    if ($result_vendor->num_rows > 0) {
        $row_vendor = $result_vendor->fetch_assoc();
        $vendor_id = $row_vendor['vendor_id'];

        // Cek apakah iklan milik vendor yang sedang login
        $query_check = "SELECT * FROM banner_ads WHERE banner_id = '$banner_id' AND vendor_id = '$vendor_id'";
        $result_check = $koneksi->query($query_check);

        if ($result_check->num_rows > 0) {
            // Hapus iklan dari database
            $query_delete = "DELETE FROM banner_ads WHERE banner_id = '$banner_id' AND vendor_id = '$vendor_id'";
            if ($koneksi->query($query_delete) === TRUE) {
                // Redirect setelah sukses
                header("Location: kelola_banner2.php?status=deleted");
            } else {
                // Jika gagal menghapus
                header("Location: kelola_banner2.php?status=error");
            }
        } else {
            // Iklan tidak ditemukan atau bukan milik vendor
            header("Location: kelola_banner2.php?status=not_found");
        }
    } else {
        // Vendor tidak ditemukan
        header("Location: kelola_banner2.php?status=vendor_not_found");
    }
} else {
    // ID tidak ada
    header("Location: kelola_banner2.php?status=invalid_request");
}

$koneksi->close();
?>
