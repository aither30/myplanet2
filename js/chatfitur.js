$(document).ready(function () {
  // Fungsi untuk membuka popup chat dan memulai dengan daftar chat
  $("#openChatBtn").on("click", function () {
    $("#myChatPopup").show(); // Tampilkan popup chat
    $("#chatList").show(); // Tampilkan daftar chat
    $("#chatView").hide(); // Sembunyikan tampilan chat
    $("#chatHeaderTitle").text("Chat List").show(); // Setel judul default untuk list card
    $("#closeChatViewBtn").hide(); // Sembunyikan tombol kembali ke list
    $("#closeChatListBtn").show(); // Tampilkan tombol close popup
    $(".chat-footer").hide(); // Sembunyikan form pesan
    loadAccountList(); // Muat daftar akun
  });

  // Fungsi untuk menutup popup chat (kembali ke halaman utama)
  $("#closeChatListBtn").on("click", function () {
    $("#myChatPopup").hide(); // Sembunyikan seluruh popup chat
    $("#chatHeaderTitle").hide(); // Sembunyikan judul chat
  });

  // Fungsi untuk menutup tampilan chat dan kembali ke daftar chat
  $("#closeChatViewBtn").on("click", function () {
    $("#chatList").show(); // Tampilkan daftar chat
    $("#chatView").hide(); // Sembunyikan tampilan chat
    $("#chatHeaderTitle").text("Chat List").show(); // Setel kembali judul ke "Chat List"
    $("#closeChatViewBtn").hide(); // Sembunyikan tombol kembali ke list
    $("#closeChatListBtn").show(); // Tampilkan tombol close popup
    $(".chat-footer").hide(); // Sembunyikan form pesan
  });

  // Fungsi untuk menampilkan tampilan chat saat kartu pengguna diklik
  $("#chatListContent").on("click", ".account-card", function () {
    var receiverId = $(this).data("receiver-id");
    var username = $(this).data("username"); // Ambil nama pengguna dari data-username
    openChatWithUser(receiverId, username); // Buka percakapan dengan pengguna
  });

  // Fungsi untuk memuat daftar akun pengguna/vendor
  function loadAccountList() {
    $.ajax({
      url: "../system.message/get_chat_list.php",
      success: function (data) {
        $("#chatListContent").html(data); // Masukkan data daftar chat
      },
    });
  }

  // Fungsi untuk membuka percakapan dengan pengguna/vendor
  function openChatWithUser(receiverId, username) {
    // Sembunyikan daftar chat dan tampilkan chat view
    $("#chatList").hide();
    $("#chatView").show(); // Tampilkan chat view
    $(".chat-footer").show(); // Tampilkan form pesan
    $("#chatHeaderTitle").text(username).show(); // Tampilkan judul dengan nama pengguna
    $("#closeChatListBtn").hide(); // Sembunyikan tombol close popup
    $("#closeChatViewBtn").show(); // Tampilkan tombol kembali ke list

    // Muat pesan dari penerima ini
    loadMessages(receiverId);
  }

  // Fungsi untuk memuat pesan dari penerima
  function loadMessages(receiverId) {
    $.ajax({
      url: "../system.message/get_messages.php",
      data: { receiver_id: receiverId },
      success: function (data) {
        $("#chatMessages").html(data); // Tampilkan pesan di dalam chat body
      },
    });
  }

  // Fungsi untuk mengirim pesan
  $("#sendMessageBtn").on("click", function () {
    sendMessage();
  });

  $("#chatInput").on("keydown", function (e) {
    if (e.key === "Enter" && !e.shiftKey) {
      e.preventDefault();
      sendMessage();
    }
  });

  // Fungsi untuk mengirim pesan
  function sendMessage() {
    var message = $("#chatInput").val().trim();
    var receiverId = $("#chatHeaderTitle").data("receiver-id");

    if (message !== "") {
      $.ajax({
        type: "POST",
        url: "../system.message/send_message.php",
        data: { message_content: message, receiver_id: receiverId },
        success: function (response) {
          $("#chatInput").val(""); // Kosongkan input setelah pesan dikirim
          loadMessages(receiverId); // Muat ulang pesan
        },
      });
    }
  }
});
