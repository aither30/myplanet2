<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $description = $_POST['description'];
    $filename = $_FILES['file']['name'];
    $target_dir = "uploads/";
    $target_file = $target_dir . basename($filename);

    if (move_uploaded_file($_FILES['file']['tmp_name'], $target_file)) {
        $stmt = $conn->prepare("INSERT INTO images (filename, description) VALUES (?, ?)");
        $stmt->bind_param("ss", $filename, $description);

        if ($stmt->execute()) {
            echo "Image uploaded successfully.";
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
    <input type="file" name="file" required>
    <textarea name="description" placeholder="Enter description" required></textarea>
    <button type="submit">Upload</button>
</form>
