<?php
include "./db.php";
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="styless.css">
    <title>MyPlanET Dashboard</title>
</head>
<body>
    <div class="hero">
    <h1>MyPlanET Dashboard</h1>
    </div>
    <div class="container" >
        <div class="sidebar" >
        <p id="dataUser">Data User</p>
        <p id="dataProduk" >Data Produk</p>
        <p id="dataImages" >Data Images</p>
    </div>
    <div class="content">
        <div class="container_users" id="user">
            <?php include "./users/index.php"; ?>
        </div>
        <div class="produk" id="produk">
            <?php include "./produk/index.php";?>
        </div>
        <div class="images" id="images">
            <?php include "./images/index.php";?>
        </div>
    </div>
    </div>
    <script>
// Fungsi untuk merubah gaya user
// Fungsi untuk merubah gaya user
function changeStyleUser() {
    var user = document.getElementById('user');
    var produk = document.getElementById('produk');

    // Menyembunyikan produk jika tampil
    if (produk.style.display === 'flex') {
        produk.style.display = 'none';
    }

    if(images.style.display === 'flex'){
        images.style.display = 'none';
    }

    // Menampilkan user
    user.style.display = 'flex';

    // Menyimpan status ke localStorage
    localStorage.setItem('currentTable', 'user');
}

document.getElementById('dataUser').addEventListener('click', changeStyleUser);

function changeStyleProduk() {
    var user = document.getElementById('user');
    var produk = document.getElementById('produk');
    var images = document.getElementById('images');

    if (user.style.display === 'flex') {
        user.style.display = 'none';
    }
    
    if(images.style.display === 'flex'){
            images.style.display = 'none';
        }

    produk.style.display = 'flex';

    localStorage.setItem('currentTable', 'produk');
}

document.getElementById('dataProduk').addEventListener('click', changeStyleProduk);

function changeStyleImages(){
    var user = document.getElementById('user');
    var produk = document.getElementById('produk');
    var images = document.getElementById('images');
    
    if (user.style.display === 'flex') {
        user.style.display = 'none';
    }

    if(produk.style.display === 'flex'){
        produk.style.display = 'none';
    }

    images.style.display = 'flex';
}

document.getElementById('dataImages').addEventListener('click', changeStyleImages);

function restoreState() {
    var currentTable = localStorage.getItem('currentTable');
    if (currentTable === 'user') {
        document.getElementById('user').style.display = 'flex';
        document.getElementById('produk').style.display = 'none';
        document.getElementById('images').style.display = 'none';
    } else if (currentTable === 'produk') {
        document.getElementById('produk').style.display = 'flex';
        document.getElementById('user').style.display = 'none';
        document.getElementById('images').style.display = 'none';
    } else if (currentTable === 'images'){
        document.getElementById('images').style.display = 'flex';
        document.getElementById('user').style.display = 'none';
        document.getElementById('produk').style.display = 'none';
    }else {
        // Default state jika tidak ada data di localStorage
        document.getElementById('user').style.display = 'none';
        document.getElementById('produk').style.display = 'none';
        document.getElementById('images').style.display = 'none';
    }
}

// Memulihkan status saat halaman dimuat
document.addEventListener('DOMContentLoaded', restoreState);

    </script>
</body>
</html>
