<?php
include '../db.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];
    $result = $conn->query("SELECT * FROM images WHERE id = $id");
    $image = $result->fetch_assoc();
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'];
    $description = $_POST['description'];

    $stmt = $conn->prepare("UPDATE images SET description = ? WHERE id = ?");
    $stmt->bind_param("si", $description, $id);

    if ($stmt->execute()) {
        echo "Image updated successfully.";
    } else {
        echo "Error: " . $stmt->error;
    }
    $stmt->close();
}
?>

<form action="update.php?id=<?php echo $image['id']; ?>" method="post">
    <input type="hidden" name="id" value="<?php echo $image['id']; ?>">
    <textarea name="description"><?php echo $image['description']; ?></textarea>
    <button type="submit">Update</button>
</form>
