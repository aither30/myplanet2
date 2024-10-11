document.addEventListener("DOMContentLoaded", function () {
    let selectedProducts = [];
    const maxProducts = 3; // Maksimal produk yang bisa dipilih

    const productCountElement = document.getElementById("product_count");
    const productListElement = document.getElementById("productList");
    const cancelVendorElement = document.getElementById("cancelVendor");
    const compareButtonElement = document.getElementById("compareBtn");
    const contentContainerCompareVendor = document.getElementById("contentContainerCompareVendor");

    // Fungsi untuk memperbarui tampilan daftar produk yang dipilih
    function updateSelectedProducts() {
        productListElement.innerHTML = "";
        cancelVendorElement.innerHTML = "";
        contentContainerCompareVendor.innerHTML = ""; // Bersihkan produk yang dibandingkan

        selectedProducts.forEach((product, index) => {
            productListElement.innerHTML += `<p>${product.name}</p>`;
            cancelVendorElement.innerHTML += `<button class="removeProduct" data-index="${index}">x</button>`;

            // Tampilkan card lengkap produk yang dipilih di container perbandingan
            contentContainerCompareVendor.innerHTML += `
                <div class="compare_product_card">
                    <img src="${product.image}" alt="${product.name}" />
                    <h3>${product.name}</h3>
                    <p>Harga: Rp ${parseInt(product.price).toLocaleString()}</p>
                    <p>Deskripsi: ${product.description}</p>
                </div>
            `;
        });

        productCountElement.textContent = `${selectedProducts.length} Produk yang dipilih`;

        // Tampilkan atau sembunyikan tombol "Bandingkan" berdasarkan apakah ada produk yang dipilih
        if (selectedProducts.length > 0) {
            compareButtonElement.style.display = "block";
        } else {
            compareButtonElement.style.display = "none";
        }
    }

    // Menangani klik pada tombol hapus produk yang dipilih
    cancelVendorElement.addEventListener("click", function (e) {
        if (e.target.classList.contains("removeProduct")) {
            const index = e.target.getAttribute("data-index");
            selectedProducts.splice(index, 1);
            updateSelectedProducts();
        }
    });

    // Menangani tombol Bandingkan
    compareButtonElement.addEventListener("click", function () {
        if (selectedProducts.length > 0) {
            alert("Bandingkan Produk: \n" + selectedProducts.map((p) => p.name).join(", "));
        } else {
            alert("Pilih minimal satu produk untuk dibandingkan.");
        }
    });

    // Menangani pencarian produk
    document.getElementById("searchInputCompare").addEventListener("input", function () {
        const searchQuery = this.value.toLowerCase();

        productListElement.innerHTML = ""; // Bersihkan hasil pencarian sebelumnya

        products.forEach(product => {
            if (product.name.toLowerCase().includes(searchQuery)) {
                productListElement.innerHTML += `
                    <div class="product_card" data-product-id="${product.product_id}" data-product-name="${product.name}" data-product-price="${product.prices}" data-product-description="${product.description}" data-product-image="${product.images}">
                        <p>${product.name}</p>
                        <p>Harga: Rp ${parseInt(product.prices).toLocaleString()}</p>
                        <button class="compare_button">Bandingkan</button>
                    </div>
                `;
            }
        });

        // Menangani klik pada tombol bandingkan di hasil pencarian
        document.querySelectorAll(".compare_button").forEach((button) => {
            button.addEventListener("click", function () {
                const productCard = this.closest(".product_card");
                const productId = productCard.getAttribute("data-product-id");
                const productName = productCard.getAttribute("data-product-name");
                const productPrice = productCard.getAttribute("data-product-price");
                const productDescription = productCard.getAttribute("data-product-description");
                const productImage = productCard.getAttribute("data-product-image");

                if (selectedProducts.length < maxProducts) {
                    selectedProducts.push({
                        id: productId,
                        name: productName,
                        price: productPrice,
                        description: productDescription,
                        image: productImage,
                    });
                    updateSelectedProducts();
                } else {
                    alert("Anda hanya dapat memilih maksimal 3 produk.");
                }
            });
        });
    });

    // Toggle vendor comparison details
    document.getElementById("toggleButtonVendor").addEventListener("click", function () {
        var detailVendorCompare = document.querySelector("#detailVendorCompare");

        // Toggle visibility of the product comparison section
        if (detailVendorCompare.style.display === "none" || detailVendorCompare.style.display === "") {
            detailVendorCompare.style.display = "block";
        } else {
            detailVendorCompare.style.display = "none";
        }
    });
});
