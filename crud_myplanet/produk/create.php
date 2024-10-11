<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $logoVendor = $_FILES['logoVendor']['name'];
    $nama_vendor = $_POST['nama_vendor'];
    $tentang_vendor = $_POST['tentang_vendor'];
    $produk = $_POST['produk'];
    $harga_produk = $_POST['harga_produk'];
    $lokasi_vendor = $_POST['lokasi_vendor'];
    $nophone_vendor = $_POST['nophone_vendor'];
    $email_vendor = $_POST['email_vendor'];
    $faq_vendor = $_POST['faq_vendor'];
    $faq_answer_vendor = $_POST['faq_answer_vendor'];

    $target_dir = "../uploads/";
    $target_file = $target_dir . basename($logoVendor);

    if (move_uploaded_file($_FILES['logoVendor']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO produk (logoVendor, nama_vendor, tentang_vendor, produk, harga_produk, lokasi_vendor, nophone_vendor, email_vendor, faq_vendor, faq_answer_vendor) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("ssssssssss", $logoVendor, $nama_vendor, $tentang_vendor, $produk, $harga_produk, $lokasi_vendor, $nophone_vendor, $email_vendor, $faq_vendor, $faq_answer_vendor);

        if ($stmt->execute()) {
            echo "Vendor added successfully.";
        } else {
            echo "Error: " . $stmt->error;
        }
        $stmt->close();
    } else {
        echo "Error uploading file.";
    }
}
?>

<form action="create.php" method="post" enctype="multipart/form-data">
    <input type="file" name="logoVendor" required>
    <input type="text" name="nama_vendor" placeholder="Nama Vendor" required>
    <textarea name="tentang_vendor" placeholder="Tentang Vendor" required></textarea>
    <input type="text" name="produk" placeholder="Produk" required>
    <input type="text" name="harga_produk" placeholder="Harga Produk" required>
    <input type="text" name="lokasi_vendor" placeholder="Lokasi Vendor" required>
    <input type="text" name="nophone_vendor" placeholder="No. Phone" required>
    <input type="email" name="email_vendor" placeholder="Email Vendor" required>
    <textarea name="faq_vendor" placeholder="FAQ" required></textarea>
    <textarea name="faq_answer_vendor" placeholder="FAQ Answer" required></textarea>
    <button type="submit">Add Vendor</button>
</form>
