    <!-- Popup Chat -->
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