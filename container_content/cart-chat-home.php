<div id="cart-popup" style="display: none;">
                <h3>Produk di Keranjang</h3>
                <div id="cart-items"></div>
                <button onclick="goToCartPage()">Lihat Keranjang</button>
                <script>function goToCartPage() {
                    window.location.href = './content_detail_product/cart.page.php'; // Redirect ke halaman keranjang
}</script>
                <button class="close-btn" onclick="closeCartPopup()"><i class="fa-regular fa-circle-xmark"></i></button>
            </div>

            
    <div class="chat-popup" id="myChatPopup" style="display: none;">
        <div class="chat-header">
            <span id="chatHeaderTitle">Chat</span>
            <button id="closeChatListBtn" class="close-chat-btn"><i class="fa-regular fa-circle-xmark"></i></button>
            <button id="closeChatViewBtn" class="close-chat-btn" style="display: none;"><i class="fa-regular fa-circle-xmark"></i></button>
        </div>
        <div class="chat-body">
            <div id="chatList" class="chat-list">
                <div id="chatListContent"></div>
            </div>
            <div id="chatView" class="chat-view" style="display: none;">
                <div id="chatMessages"></div>
            </div>
        </div>
        <div class="chat-footer">
            <textarea id="chatInput" placeholder="Type a message..."></textarea>
            <button id="sendMessageBtn">Send</button>
        </div>
    </div>
    <script>
            function displayCartItems() {
        $.ajax({
            url: './content_detail_product/get_cart_items_home.php',
            type: 'GET',
            success: function(response) {
                document.getElementById('cart-items').innerHTML = response;
                document.getElementById('cart-popup').style.display = 'block';
                isCartPopupOpen = true;
            }
        });
    }

    function closeCartPopup() {
        document.getElementById('cart-popup').style.display = 'none';
        isCartPopupOpen = false;
    }

    document.getElementById('openCartBtn').addEventListener('click', function() {
        displayCartItems();
    });
    </script>

    <script>
        function updateQuantity(cartId, change, price) {
    $.ajax({
        url: './content_detail_product/update_cart_home.php',
        type: 'POST',
        data: {
            cart_id: cartId,
            quantity_change: change
        },
        success: function(response) {
            let res = JSON.parse(response);
            if (res.status === 'success') {
                let newQuantity = res.new_quantity;
                document.getElementById('quantity-' + cartId).innerText = newQuantity;
                document.getElementById('total-' + cartId).innerText = 'Rp. ' + (newQuantity * price).toLocaleString('id-ID', {minimumFractionDigits: 2});
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
                text: 'Gagal memperbarui kuantitas',
                icon: 'error',
                confirmButtonText: 'OK'
            });
        }
    });
}

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

    </script>
    