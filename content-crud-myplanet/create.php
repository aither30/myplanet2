<?php
include './config/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $tabel = $_POST['tabel'];

    // Ambil data dari form
    if ($tabel == 'account') {
        $username = $_POST['username'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $email = $_POST['email'];
        $type_account = $_POST['type_account'];

        $sql = "INSERT INTO account (username, password, type_account, email)
                VALUES ('$username', '$password', '$type_account', '$email')";

        if ($conn->query($sql) === TRUE) {
            echo "New record created successfully";
        } else {
            echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
}
?>

<form method="post" action="create.php">
    <input type="hidden" name="tabel" value="account">
    <label>Username:</label><input type="text" name="username"><br>
    <label>Password:</label><input type="password" name="password"><br>
    <lab
