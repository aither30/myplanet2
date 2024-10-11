// Fungsi untuk toggle dropdown
function toggleDropdown() {
  document.getElementById("dropdownContent2").classList.toggle("show");
}

// Tutup dropdown jika pengguna mengklik di luar area dropdown
window.onclick = function (event) {
  if (!event.target.matches(".bandingharga button")) {
    var dropdowns = document.getElementsByClassName("Content-dropdown2");
    for (var i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains("show")) {
        openDropdown.classList.remove("show");
      }
    }
  }
};

// Event listener untuk profil dropdown
const profilButton = document.querySelector(".profil button");
const dropdownContent = document.querySelector(".Content-dropdown");

profilButton.addEventListener("click", () => {
  dropdownContent.style.display =
    dropdownContent.style.display === "block" ? "none" : "block";
});
