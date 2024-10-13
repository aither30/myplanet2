<?php

require "../config/config.php";

if (empty($_POST["username"]) || empty($_POST["email"]) || empty($_POST["password"]) || empty($_POST["password_confirmation"]) || empty($_POST["type_account"])) {
    die("All fields are required.");
}

if (preg_match('/[\s\/\*\{\}\[\]]/', $_POST["username"])) {
    die("Username cannot contain spaces or special characters like /, *, {, }.");
}


if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL)) {
    die("A valid email is required.");
}

if (strlen($_POST["password"]) <= 8 || strlen($_POST["password"]) > 25) {
    die("Password must be more than 8 characters and less than or equal to 25 characters.");
}


if ($_POST["password"] !== $_POST["password_confirmation"]) {
    die("Password and confirmation password do not match.");
}

// Hash password
$password = password_hash($_POST["password"], PASSWORD_DEFAULT);

// Koneksi ke database
$mysqli = new mysqli($db_host, $db_username, $db_password, $db_name);

// Cek koneksi
if ($mysqli->connect_error) {
    die("Connection failed: " . $mysqli->connect_error);
}

// Cek apakah email sudah ada di database
$check_email_sql = "SELECT * FROM account WHERE email = ?";
$stmt = $mysqli->prepare($check_email_sql);
$stmt->bind_param("s", $_POST['email']); // Menggunakan bind_param untuk email
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    die("Email already exists. Please use a different email.");
}

// Prepare statement untuk tabel account
$stmt = $mysqli->prepare("INSERT INTO account (username, email, password, type_account) VALUES (?, ?, ?, ?)");
if (!$stmt) {
    die("SQL error: " . $mysqli->error);
}

// Bind parameter, hanya 4 parameter yang diperlukan
$stmt->bind_param("ssss", $_POST["username"], $_POST["email"], $password, $_POST["type_account"]);

// Eksekusi statement
if ($stmt->execute()) {
    echo "Registration successful.";
} else {
    die("Error inserting into account: " . $stmt->error);
}

// Tutup statement dan koneksi
$stmt->close();
$mysqli->close();

?>
