<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // First, get the filename so we can delete the file
    $result = $conn->query("SELECT filename FROM images WHERE id = $id");
    $image = $result->fetch_assoc();
    $filename = $image['filename'];

    // Delete the image file from the server
    unlink("uploads/" . $filename);

    // Delete the image from the database
    $conn->query("DELETE FROM images WHERE id = $id");

    header("Location: index.php");
}
?>
