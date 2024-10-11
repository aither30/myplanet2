document.getElementById("theme-toggle").addEventListener("click", function () {
  document.body.classList.toggle("dark-theme");

  // Ganti ikon
  const icon = this.querySelector("i");
  if (document.body.classList.contains("dark-theme")) {
    icon.classList.remove("fa-circle-half-stroke");
    icon.classList.add("fa-sun"); // Ganti dengan ikon matahari saat dark theme
  } else {
    icon.classList.remove("fa-sun");
    icon.classList.add("fa-circle-half-stroke"); // Ganti kembali ke ikon setengah lingkaran
  }
});
