<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM users WHERE id = $id");
    $user = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $user_name = $_POST['user_name'];
    $name = $_POST['name'];
    $password = password_hash($_POST['password'], PASSWORD_BCRYPT);

    $stmt = $conn->prepare("UPDATE users SET user_name = ?, name = ?, password = ? WHERE id = ?");
    $stmt->bind_param("sssi", $user_name, $name, $password, $id);

    if ($stmt->execute()) {
        echo "User updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<form action="update.php?id=<?php echo $user['id']; ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
    <input type="text" name="user_name" value="<?php echo $user['user_name']; ?>" required>
    <input type="text" name="name" value="<?php echo $user['name']; ?>" required>
    <input type="password" name="password" placeholder="New Password">
    <button type="submit">Update User</button>
</form>
