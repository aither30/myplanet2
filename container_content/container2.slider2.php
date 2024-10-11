<?php
include ("./config/config.php");
// Query untuk mengambil data iklan bertipe 'slider2'
$sql = "SELECT image_url, title, description, button_text, link_url FROM banner_ads WHERE banner_type = 'slider2'";
$result = $koneksi->query($sql);

if ($result->num_rows > 0) {
    // Menampilkan slider konten
    echo '<div class="container2-slider2">
            <div class="slider-container">
                <div class="slider">';

    // Loop melalui hasil query dan tampilkan setiap banner
    while ($row = $result->fetch_assoc()) {
        echo '<div class="slide">
                <div class="promosi-content">
                  <h2 class="promosi-title">' . $row["title"] . '</h2>
                  <p class="promosi-description">' . $row["description"] . '</p>';
                  
        if (!empty($row["button_text"])) {
            echo '<a href="' . $row["link_url"] . '">
                    <button class="promosi-button">' . $row["button_text"] . '</button>
                  </a>';
        }

        echo '</div>
                <div class="promosi-banner">
                  <img src="./banner_ads/' . $row["image_url"] . '" alt="' . $row["title"] . '" />
                </div>
              </div>';
    }

    echo '</div>
            <button class="prev" onclick="prevSlide()">&#10094;</button>
            <button class="next" onclick="nextSlide()">&#10095;</button>
          </div>
          <div class="iklan-banner-promosi3">
            <div class="slide-wrapper3">
              <!-- Tambahkan banner landscape yang sesuai di sini -->';

    // Menampilkan bagian tambahan untuk slider 3
    $sql_slider3 = "SELECT image_url FROM banner_ads WHERE banner_type = 'slider3'";
    $result_slider3 = $koneksi->query($sql_slider3);

    if ($result_slider3->num_rows > 0) {
        while ($row_slider3 = $result_slider3->fetch_assoc()) {
            echo '<div class="slide3-banner-promosi3">
                    <img src="./banner_ads/' . $row_slider3["image_url"] . '" alt="Promosi Landscape" />
                  </div>';
        }
    }

    echo '</div>
            <button class="prev3" onclick="prevSlide3()">&#10094;</button>
            <button class="next3" onclick="nextSlide3()">&#10095;</button>
            <div class="dot-container">
              <span class="dot" onclick="showSlide3(0)"></span>
              <span class="dot" onclick="showSlide3(1)"></span>
              <span class="dot" onclick="showSlide3(2)"></span>
              <span class="dot" onclick="showSlide3(3)"></span>
            </div>
          </div>
        </div>';
} else {
    echo "Tidak ada iklan yang ditemukan.";
}

$koneksi->close();
?>
