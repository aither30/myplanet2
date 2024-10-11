<?php
session_start();
include ("../config/config.php");

if (isset($_GET['id'])) {
    $banner_id = intval($_GET['id']);
    
    $username = $_SESSION['username'];
    $query_vendor = "SELECT vendor_id FROM business_account WHERE username = '$username'";
    $result_vendor = $koneksi->query($query_vendor);

    if ($result_vendor->num_rows > 0) {
        $row_vendor = $result_vendor->fetch_assoc();
        $vendor_id = $row_vendor['vendor_id'];

        $query_check = "SELECT * FROM banner_ads WHERE banner_id = '$banner_id' AND vendor_id = '$vendor_id'";
        $result_check = $koneksi->query($query_check);

        if ($result_check->num_rows > 0) {
            $query_delete = "DELETE FROM banner_ads WHERE banner_id = '$banner_id' AND vendor_id = '$vendor_id'";
            if ($koneksi->query($query_delete) === TRUE) {
                header("Location: kelola_banner3.php?status=deleted");
            } else {
                header("Location: kelola_banner3.php?status=error");
            }
        } else {
            header("Location: kelola_banner3.php?status=not_found");
        }
    } else {
        header("Location: kelola_banner3.php?status=vendor_not_found");
    }
} else {
    header("Location: kelola_banner3.php?status=invalid_request");
}

$koneksi->close();
?>
