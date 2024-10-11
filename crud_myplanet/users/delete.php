<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // Delete the user from the database
    $conn->query("DELETE FROM users WHERE id = $id");

    header("Location: index.php");
}
?>
