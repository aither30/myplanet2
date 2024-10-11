<?php
include ("./config/config.php");
// Query untuk mengambil data iklan bertipe 'landscape'
$sql = "SELECT image_url, title, description, button_text, link_url FROM banner_ads WHERE banner_type = 'landscape'";
$result = $koneksi->query($sql);

if ($result->num_rows > 0) {
    // Menampilkan data iklan
    echo '<div class="container2-slider1">
            <div class="slider-container-landscape">
                <div class="slider-landscape">';
    
    // Loop melalui hasil query dan tampilkan iklan
    while($row = $result->fetch_assoc()) {
        echo '<div class="slide-landscape active">
                <img src="./banner_ads/' . $row["image_url"] . '" alt="' . $row["title"] . '" />
              </div>';
    }

    echo '</div>
            <button class="prev-landscape" onclick="prevSlideLandscape()">&#10094;</button>
            <button class="next-landscape" onclick="nextSlideLandscape()">&#10095;</button>
          </div>
        </div>';
} else {
    echo "Tidak ada iklan yang ditemukan.";
}

$koneksi->close();
?>
