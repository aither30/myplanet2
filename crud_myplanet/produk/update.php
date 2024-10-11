<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM produk WHERE id = $id");
    $vendor = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $nama_vendor = $_POST['nama_vendor'];
    $tentang_vendor = $_POST['tentang_vendor'];
    $produk = $_POST['produk'];
    $harga_produk = $_POST['harga_produk'];
    $lokasi_vendor = $_POST['lokasi_vendor'];
    $nophone_vendor = $_POST['nophone_vendor'];
    $email_vendor = $_POST['email_vendor'];
    $faq_vendor = $_POST['faq_vendor'];
    $faq_answer_vendor = $_POST['faq_answer_vendor'];

    $stmt = $conn->prepare("UPDATE produk SET nama_vendor = ?, tentang_vendor = ?, produk = ?, harga_produk = ?, lokasi_vendor = ?, nophone_vendor = ?, email_vendor = ?, faq_vendor = ?, faq_answer_vendor = ? WHERE id = ?");
    $stmt->bind_param("sssssssssi", $nama_vendor, $tentang_vendor, $produk, $harga_produk, $lokasi_vendor, $nophone_vendor, $email_vendor, $faq_vendor, $faq_answer_vendor, $id);

    if ($stmt->execute()) {
        echo "Vendor updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<form action="update.php?id=<?php echo $vendor['id']; ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $vendor['id']; ?>">
    <input type="text" name="nama_vendor" value="<?php echo $vendor['nama_vendor']; ?>" required>
    <textarea name="tentang_vendor" required><?php echo $vendor['tentang_vendor']; ?></textarea>
    <input type="text" name="produk" value="<?php echo $vendor['produk']; ?>" required>
    <input type="text" name="harga_produk" value="<?php echo $vendor['harga_produk']; ?>" required>
    <input type="text" name="lokasi_vendor" value="<?php echo $vendor['lokasi_vendor']; ?>" required>
    <input type="text" name="nophone_vendor" value="<?php echo $vendor['nophone_vendor']; ?>" required>
    <input type="email" name="email_vendor" value="<?php echo $vendor['email_vendor']; ?>" required>
    <textarea name="faq_vendor" required><?php echo $vendor['faq_vendor']; ?></textarea>
    <textarea name="faq_answer_vendor" required><?php echo $vendor['faq_answer_vendor']; ?></textarea>
    <button type="submit">Update Vendor</button>
</form>
