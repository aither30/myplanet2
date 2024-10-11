<?php
include ("../config/config.php");
// Menghapus data berdasarkan ID
$id = $_GET['id'];
$sql = "DELETE FROM banner_ads WHERE banner_id = $id";

if ($koneksi->query($sql) === TRUE) {
    echo "Iklan berhasil dihapus.";
} else {
    echo "Error: " . $koneksi->error;
}

$koneksi->close();
header('Location: kelola_banner1.php'); // Mengarahkan kembali ke halaman kelola banner
?>
