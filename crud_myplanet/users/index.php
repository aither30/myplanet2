<?php
include './db.php';

$result = $conn->query("SELECT * FROM users");
?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Name</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['user_name']; ?></td>
        <td><?php echo $row['name']; ?></td>
        <td>
            <a href="./users/update.php?id=<?php echo $row['id']; ?>">Edit</a>
            <a href="./users/delete.php?id=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
