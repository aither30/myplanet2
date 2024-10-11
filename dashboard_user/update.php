<?php
session_start();
include ("../config/config.php");

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    header("Location: login.php"); // Redirect ke halaman login jika belum login
    exit();
}

$username = $_SESSION['username'];

// Ambil data pengguna berdasarkan username
$sql = "SELECT * FROM user_account WHERE username = '$username'";
$result = $koneksi->query($sql);

if ($result->num_rows > 0) {
    $user = $result->fetch_assoc(); // Ambil data pengguna
} else {
    echo "Pengguna tidak ditemukan.";
    exit();
}

// Proses update data jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'];
    $phone = $_POST['phone'];
    $email = $_POST['email'];
    $gender = $_POST['gender'];
    $usia = $_POST['usia'];
    $institusi_afiliasi = $_POST['institusi_afiliasi'];
    $event_preference = $_POST['event_preference'];
    $budget = $_POST['budget'];

    // Query update
    $sql = "UPDATE user_account SET 
            name='$name', phone='$phone', email='$email', gender='$gender', usia='$usia', 
            institusi_afiliasi='$institusi_afiliasi', event_preference='$event_preference', 
            budget='$budget' WHERE username='$username'";

    if ($koneksi->query($sql) === TRUE) {
        echo "Profil berhasil diperbarui.";
        header("Location: index.php"); // Redirect ke dashboard setelah update
        exit();
    } else {
        echo "Error: " . $koneksi->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Profil</title>
    <style>
        .form-container {
            max-width: 600px;
            margin: 50px auto;
            padding: 20px;
            border-radius: 12px;
            background-color: #f4f4f4;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .form-container h2 {
            text-align: center;
            margin-bottom: 20px;
        }

        .form-container label {
            font-weight: bold;
            margin-top: 10px;
            display: block;
        }

        .form-container input[type="text"], 
        .form-container input[type="email"], 
        .form-container select, 
        .form-container textarea, 
        .form-container input[type="number"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ccc;
        }

        .form-container button {
            display: block;
            width: 100%;
            padding: 10px;
            background-color: #007BFF;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
        }

        .form-container button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<div class="form-container">
    <h2>Update Profil</h2>
    <form method="POST">
        <label for="name">Nama Lengkap</label>
        <input type="text" id="name" name="name" value="<?php echo $user['name']; ?>" required>

        <label for="phone">Nomor Telepon</label>
        <input type="text" id="phone" name="phone" value="<?php echo $user['phone']; ?>" required>

        <label for="email">Email</label>
        <input type="email" id="email" name="email" value="<?php echo $user['email']; ?>" required>

        <label for="gender">Jenis Kelamin</label>
        <select id="gender" name="gender">
            <option value="Male" <?php if ($user['gender'] == 'Male') echo 'selected'; ?>>Male</option>
            <option value="Female" <?php if ($user['gender'] == 'Female') echo 'selected'; ?>>Female</option>
            <option value="Other" <?php if ($user['gender'] == 'Other') echo 'selected'; ?>>Other</option>
        </select>

        <label for="usia">Usia</label>
        <input type="number" id="usia" name="usia" value="<?php echo $user['usia']; ?>">

        <label for="institusi_afiliasi">Institusi Afiliasi</label>
        <input type="text" id="institusi_afiliasi" name="institusi_afiliasi" value="<?php echo $user['institusi_afiliasi']; ?>">

        <label for="event_preference">Preferensi Event</label>
        <textarea id="event_preference" name="event_preference"><?php echo $user['event_preference']; ?></textarea>

        <label for="budget">Budget</label>
        <input type="number" id="budget" name="budget" value="<?php echo $user['budget']; ?>" step="0.01">
        
        <button type="submit">Update</button>
    </form>
</div>

</body>
</html>
