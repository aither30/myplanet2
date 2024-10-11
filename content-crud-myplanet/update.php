<?php
include './config/config.php';

if (isset($_GET['id']) && isset($_GET['tabel'])) {
    $id = $_GET['id'];
    $tabel = $_GET['tabel'];

    if ($tabel == 'account') {
        $sql = "SELECT * FROM account WHERE account_id = $id";
        $result = $conn->query($sql);
        $row = $result->fetch_assoc();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $username = $_POST['username'];
            $email = $_POST['email'];
            $type_account = $_POST['type_account'];

            $sql = "UPDATE account SET username='$username', email='$email', type_account='$type_account' WHERE account_id=$id";

            if ($conn->query($sql) === TRUE) {
                echo "Record updated successfully";
            } else {
                echo "Error updating record: " . $conn->error;
            }
        }
    }
}
?>

<form method="post" action="">
    <label>Username:</label><input type="text" name="username" value="<?php echo $row['username']; ?>"><br>
    <label>Email:</label><input type="email" name="email" value="<?php echo $row['email']; ?>"><br>
    <label>Type Account:</label>
    <select name="type_account">
        <option value="user" <?php if($row['type_account'] == 'user') echo 'selected'; ?>>User</option>
        <option value="vendor" <?php if($row['type_account'] == 'vendor') echo 'selected'; ?>>Vendor</option>
    </select><br>
    <input type="submit" value="Update">
</form>
