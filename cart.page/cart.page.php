<?php
// Ambil keranjang dari localStorage melalui JavaScript
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Keranjang Belanja</title>
  <link rel="stylesheet" href="styles.css">
</head>
<body>
  <h1>Keranjang Belanja</h1>
  <div id="cart-container"></div>
  <button onclick="checkout()">Lanjutkan ke Pembayaran</button>
  
  <script>
    // Ambil data keranjang dari localStorage
    const cart = JSON.parse(localStorage.getItem('cart'));
    
    const cartContainer = document.getElementById('cart-container');
    
    if (cart && cart.length > 0) {
      cart.forEach(item => {
        cartContainer.innerHTML += `<p>${item.name} - Rp. ${item.price}</p>`;
      });
    } else {
      cartContainer.innerHTML = '<p>Keranjang Anda kosong.</p>';
    }
    
    function checkout() {
      alert("Pembayaran tidak tersedia saat ini.");
    }
  </script>
</body>
</html>
