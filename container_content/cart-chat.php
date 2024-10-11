<div id="cart-popup" style="display: none;">
                <h3>Produk di Keranjang</h3>
                <div id="cart-items"></div>
                <button onclick="goToCartPage()">Lihat Keranjang</button>
                <script>function goToCartPage() {
                    window.location.href = '../content_detail_product/cart.page.php'; // Redirect ke halaman keranjang
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
            url: '../content_detail_product/get_cart_items.php',
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