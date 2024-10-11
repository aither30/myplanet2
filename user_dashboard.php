<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>User Dashboard</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 0;
        }

        .container {
            width: 80%;
            margin: 20px auto;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .user-dashboard {
            display: flex;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding: 15px;
            border-bottom: 1px solid #ddd;
        }

        .user-info {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }

        .user-info div {
            padding: 10px;
            border-radius: 5px;
            background-color: #f9f9f9;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            transition: background-color 0.3s ease;
        }

        .user-info div:hover {
            background-color: #f0f0f0;
        }

        .user-info strong {
            color: #333;
        }

        .photo img {
            border-radius: 50%;
            max-width: 100px;
            height: auto;
            display: block;
        }

        hr {
            border: none;
            height: 1px;
            background-color: #eee;
            margin: 20px 0;
        }

        .no-data {
            text-align: center;
            padding: 20px;
            font-size: 18px;
            color: #777;
        }

        .budget, .type_account {
            font-weight: bold;
            color: #007bff;
        }
    </style>
</head>
<body>

<div class="container">
    <?php
include "./config/config.php";
    $sql = "SELECT * FROM user_account";
    $result = $koneksi->query($sql);

    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            ?>
            <div class="user-dashboard">
                <div class="user-info">
                    <div class="user-id"><strong>User ID:</strong> <?php echo $row['user_id']; ?></div>
                    <div class="username"><strong>Username:</strong> <?php echo $row['username']; ?></div>
                    <div class="name"><strong>Name:</strong> <?php echo $row['name']; ?></div>
                    <div class="phone"><strong>Phone:</strong> <?php echo $row['phone']; ?></div>
                    <div class="email"><strong>Email:</strong> <?php echo $row['email']; ?></div>
                    <div class="address"><strong>Address:</strong> <?php echo $row['address']; ?></div>
                    <div class="gender"><strong>Gender:</strong> <?php echo $row['gender']; ?></div>
                    <div class="usia"><strong>Usia:</strong> <?php echo $row['usia']; ?></div>
                    <div class="institusi_afiliasi"><strong>Institusi Afiliasi:</strong> <?php echo $row['institusi_afiliasi']; ?></div>
                    <div class="photo"><strong>Photo:</strong> <img src="<?php echo $row['photo']; ?>" alt="User Photo"></div>
                    <div class="event_preference"><strong>Event Preference:</strong> <?php echo $row['event_preference']; ?></div>
                    <div class="date_event"><strong>Date Event:</strong> <?php echo $row['date_event']; ?></div>
                    <div class="budget"><strong>Budget:</strong> <?php echo $row['budget']; ?></div>
                    <div class="type_account"><strong>Type Account:</strong> <?php echo $row['type_account']; ?></div>
                </div>
            </div>
            <hr>
            <?php
        }
    } else {
        echo "<div class='no-data'>No user data found.</div>";
    }

    // Close the database connection
    $koneksi->close();
    ?>
</div>

</body>
</html>
