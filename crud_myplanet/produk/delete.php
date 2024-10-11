<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // First, get the logo filename so we can delete the file
    $result = $conn->query("SELECT logoVendor FROM produk WHERE id = $id");
    $vendor = $result->fetch_assoc();
    $logoVendor = $vendor['logoVendor'];

    // Delete the logo file from the server
    unlink("../uploads/" . $logoVendor);

    // Delete the vendor from the database
    $conn->query("DELETE FROM produk WHERE id = $id");

    header("Location: index.php");
}
?>
