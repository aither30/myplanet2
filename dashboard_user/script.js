document.addEventListener("DOMContentLoaded", function () {
    // Ambil elemen menu di sidebar
    const profileMenu = document.querySelector(
      "aside .menu li a[href='#profil']"
    );
    const homeMenu = document.querySelector(
      "aside .menu li a[href='#home']"
    );
    const transactionMenu = document.querySelector(
      "aside .menu li a[href='#transaksi']"
    );

    // Ambil elemen section di main content
    const profileSection = document.querySelector("#profil");
    const homeSection = document.querySelector("#home");
    const transactionSection = document.querySelector("#transaksi");

    // Function untuk sembunyikan semua section
    function hideAllSections() {
      homeSection.style.display = "none";
      profileSection.style.display = "none";
      transactionSection.style.display = "none";
    }

    // Event listener untuk klik menu profil
    profileMenu.addEventListener("click", function (e) {
      e.preventDefault(); // Mencegah aksi default anchor
      hideAllSections(); // Sembunyikan semua section
      profileSection.style.display = "block"; // Tampilkan section profil
    });

    // Event listener untuk klik menu home
    homeMenu.addEventListener("click", function (e) {
      e.preventDefault();
      hideAllSections();
      homeSection.style.display = "block"; // Tampilkan section home
    });

    // Event listener untuk klik menu transaksi
    transactionMenu.addEventListener("click", function (e) {
      e.preventDefault();
      hideAllSections();
      transactionSection.style.display = "block"; // Tampilkan section transaksi
    });

    // Secara default tampilkan section home
    hideAllSections();
    homeSection.style.display = "block";
  });// Simple logout functionality
document.querySelector('.btn-logout').addEventListener('click', function() {
    alert('You have logged out!');
    // Here you can implement actual logout logic, like redirecting to login page
    window.location.href = 'login.html';
});
