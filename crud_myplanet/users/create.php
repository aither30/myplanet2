<?php
include '../db.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_name = $_POST['user_name'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);
    $name = $_POST['name'];

    $stmt = $conn->prepare("INSERT INTO users (user_name, password, name) VALUES (?, ?, ?)");
    $stmt->bind_param("sss", $user_name, $password, $name);

    if ($stmt->execute()) {
        echo "User created successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<form action="create.php" method="post">
    <input type="text" name="user_name" placeholder="Username" required>
    <input type="password" name="password" placeholder="Password" required>
    <input type="text" name="name" placeholder="Name" required>
    <button type="submit">Create User</button>
</form>
