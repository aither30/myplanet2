$(document).ready(function() {
    // Fungsi untuk membuka popup chat
    function openChatPopup() {
        $('#myChatPopup').show(); // Tampilkan popup chat
        console.log("Popup chat dibuka!"); // Debugging
    }

    // Fungsi untuk menutup popup chat
    function closeChatPopup() {
        $('#myChatPopup').hide(); // Sembunyikan popup chat
        console.log("Popup chat ditutup!"); // Debugging
    }

    // Event listener untuk membuka popup chat saat tombol "Chat" diklik
    $('#openChatBtn').on('click', function() {
        openChatPopup(); // Buka popup chat
    });

    // Event listener untuk menutup popup chat saat tombol "Close" diklik
    $('#closeChatBtn').on('click', function() {
        closeChatPopup(); // Tutup popup chat
    });

    // Fungsi mengirim pesan (dummy untuk saat ini)
    $('#sendMessageBtn').on('click', function() {
        var message = $('#chatInput').val();
        if (message.trim() !== '') {
            alert("Pesan terkirim: " + message); // Sementara tampilkan pesan
            $('#chatInput').val(''); // Kosongkan textarea setelah pesan dikirim
        }
    });
});
