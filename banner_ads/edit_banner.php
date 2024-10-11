<?php
include ("../config/config.php");

// Mengambil data banner berdasarkan ID
$id = $_GET['id'];
$sql = "SELECT * FROM banner_ads WHERE banner_id = $id";
$result = $koneksi->query($sql);
$row = $result->fetch_assoc();

// Memperbarui data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $image_url = $_POST['image_url'];
    $title = $_POST['title'];
    $description = $_POST['description'];
    $button_text = $_POST['button_text'];
    $link_url = $_POST['link_url'];

    $sql = "UPDATE banner_ads SET 
            image_url = '$image_url', 
            title = '$title', 
            description = '$description', 
            button_text = '$button_text', 
            link_url = '$link_url' 
            WHERE banner_id = $id";

    if ($koneksi->query($sql) === TRUE) {
        echo "Data iklan berhasil diperbarui.";
    } else {
        echo "Error: " . $sql . "<br>" . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Iklan</title>
</head>
<body>

<h2>Edit Iklan Banner</h2>

<form method="POST">
    <label>URL Gambar:</label><br>
    <input type="text" name="image_url" value="<?php echo $row['image_url']; ?>" required><br><br>
    
    <label>Judul Iklan:</label><br>
    <input type="text" name="title" value="<?php echo $row['title']; ?>"><br><br>
    
    <label>Deskripsi:</label><br>
    <textarea name="description"><?php echo $row['description']; ?></textarea><br><br>
    
    <label>Tombol:</label><br>
    <input type="text" name="button_text" value="<?php echo $row['button_text']; ?>"><br><br>
    
    <label>Link URL:</label><br>
    <input type="text" name="link_url" value="<?php echo $row['link_url']; ?>"><br><br>
    
    <button type="submit">Perbarui Iklan</button>
</form>

<a href="kelola_banner1.php"><button>Kembali</button></a>

</body>
</html>

<?php $koneksi->close(); ?>
