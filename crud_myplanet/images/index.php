<?php
include './db.php';

$result = $conn->query("SELECT * FROM images");
?>

<table border="1">
    <tr>
        <th>ID</th>
        <th>Filename</th>
        <th>Description</th>
        <th>Uploaded At</th>
        <th>Actions</th>
    </tr>
    <?php while ($row = $result->fetch_assoc()) { ?>
    <tr>
        <td><?php echo $row['id']; ?></td>
        <td><?php echo $row['filename']; ?></td>
        <td><?php echo $row['description']; ?></td>
        <td><?php echo $row['uploaded_at']; ?></td>
        <td>
            <a href="update.php?id=<?php echo $row['id']; ?>">Edit</a>
            <a href="delete.php?id=<?php echo $row['id']; ?>">Delete</a>
        </td>
    </tr>
    <?php } ?>
</table>
