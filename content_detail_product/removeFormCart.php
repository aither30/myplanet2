function removeFromCart(cartId) {
    Swal.fire({
        title: 'Apakah Anda yakin?',
        text: "Anda tidak dapat mengembalikan produk ini ke keranjang!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: 'Ya, hapus!',
        cancelButtonText: 'Batal'
    }).then((result) => {
        if (result.isConfirmed) {
            $.ajax({
                url: './content_detail_product/remove_cart_home.php',
                type: 'POST',
                data: { cart_id: cartId },
                success: function(response) {
                    let res = JSON.parse(response);
                    if (res.status === 'success') {
                        // Hapus elemen produk dari halaman tanpa reload
                        document.getElementById('cart-item-' + cartId).remove();

                        Swal.fire(
                            'Terhapus!',
                            'Produk telah dihapus dari keranjang.',
                            'success'
                        );
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: res.message,
                            icon: 'error',
                            confirmButtonText: 'OK'
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        title: 'Error!',
                        text: 'Gagal menghapus produk dari keranjang',
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                }
            });
        }
    });
}
