// Slider Landscape
let currentSlideLandscape = 0;
const slidesLandscape = document.querySelectorAll(".slide-landscape");
const slideWrapperLandscape = document.querySelector(".slider-landscape");

function showSlideLandscape(index) {
    slideWrapperLandscape.style.transform = `translateX(${-index * 100}%)`;
    currentSlideLandscape = index;
}

function nextSlideLandscape() {
    currentSlideLandscape = (currentSlideLandscape + 1) % slidesLandscape.length;
    showSlideLandscape(currentSlideLandscape);
}

function prevSlideLandscape() {
    currentSlideLandscape = (currentSlideLandscape - 1 + slidesLandscape.length) % slidesLandscape.length;
    showSlideLandscape(currentSlideLandscape);
}

// Inisialisasi slider landscape
showSlideLandscape(currentSlideLandscape);

// Auto-slide setiap 5 detik untuk landscape slider
setInterval(nextSlideLandscape, 5000);

// Slider utama
let slideIndex = 0;

function showSlide(index) {
    const slides = document.querySelectorAll(".slide");
    slideIndex = index >= slides.length ? 0 : (index < 0 ? slides.length - 1 : index);

    const slider = document.querySelector(".slider");
    slider.style.transform = `translateX(-${slideIndex * 100}%)`;
}

function nextSlide() {
    showSlide(slideIndex + 1);
}

function prevSlide() {
    showSlide(slideIndex - 1);
}

// Auto-slide untuk slider utama setiap 3 detik
function autoSlide() {
    nextSlide();
    setTimeout(autoSlide, 3000);
}

document.addEventListener("DOMContentLoaded", function () {
    showSlide(slideIndex);
    autoSlide();
});

// Slider banner promosi
let currentSlide3 = 0;
const slides3 = document.querySelectorAll(".slide3-banner-promosi3");
const slideWrapper3 = document.querySelector(".slide-wrapper3");
const dots3 = document.querySelectorAll(".iklan-banner-promosi3 .dot");

function showSlide3(index) {
    slideWrapper3.style.transform = `translateX(${-index * 100}%)`;
    dots3.forEach((dot, idx) => {
        dot.classList.toggle("active", idx === index);
    });
    currentSlide3 = index;
}

function nextSlide3() {
    showSlide3((currentSlide3 + 1) % slides3.length);
}

function prevSlide3() {
    showSlide3((currentSlide3 - 1 + slides3.length) % slides3.length);
}

// Inisialisasi slider promosi
showSlide3(currentSlide3);

// Auto-slide untuk slider promosi setiap 5 detik
setInterval(nextSlide3, 5000);
