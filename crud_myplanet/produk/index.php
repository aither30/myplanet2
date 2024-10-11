<?php
include './db.php';

$result = $conn->query("SELECT * FROM produk");
?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Logo Vendor</th>
        <th>Nama Vendor</th>
        <th>Produk</th>
        <th>Harga Produk</th>
        <th>Lokasi Vendor</th>
        <th>No. Phone</th>
        <th>Email Vendor</th>
        <th>FAQ</th>
        <th>FAQ Answer</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><img src="../uploads/<?php echo $row['logoVendor']; ?>" width="50"></td>
        <td><?php echo $row['nama_vendor']; ?></td>
        <td><?php echo $row['produk']; ?></td>
        <td><?php echo $row['harga_produk']; ?></td>
        <td><?php echo $row['lokasi_vendor']; ?></td>
        <td><?php echo $row['nophone_vendor']; ?></td>
        <td><?php echo $row['email_vendor']; ?></td>
        <td><?php echo $row['faq_vendor']; ?></td>
        <td><?php echo $row['faq_answer_vendor']; ?></td>
        <td>
            <a href="update.php?id=<?php echo $row['id']; ?>">Edit</a>
            <a href="delete.php?id=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
