<?php
include ("../config/config.php");


if (isset($_GET['id']) && isset($_GET['tabel'])) {
    $id = $_GET['id'];
    $tabel = $_GET['tabel'];

    $sql = "DELETE FROM $tabel WHERE account_id = $id";

    if ($conn->query($sql) === TRUE) {
        echo "Record deleted successfully";
    } else {
        echo "Error deleting record: " . $conn->error;
    }
}
?>
