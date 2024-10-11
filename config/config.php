<?php 
$db_host = "localhost";
$db_username = "root";
$db_password = "";
$db_name = "myplanet_db";
$koneksi = mysqli_connect($db_host, $db_username, $db_password, $db_name);

if(!$koneksi){
    die("connection failed: " . mysqli_connect_error());
}


?>