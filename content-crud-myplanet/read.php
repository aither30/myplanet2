<?php
// read.php

// Function to get table data
function getTableData($tabel) {
    // Sanitize input
    $allowed_tables = ['account', 'business_account', 'user_account', 'product', 'transaction', 'detail_pesanan'];
    
    if (in_array($tabel, $allowed_tables)) {
        // Database connection
        $conn = new mysqli('localhost', 'root', '', 'myplanet_db');

        if ($conn->connect_error) {
            die("Connection failed: " . $conn->connect_error);
        }

        // Query to fetch data
        $query = "SELECT * FROM $tabel";
        $result = $conn->query($query);

        if ($result && $result->num_rows > 0) {
            // Start table
            echo "<table border='1' cellpadding='10' cellspacing='0' style='width: 100%; border-collapse: collapse;'>";
            
            // Table headers
            echo "<thead><tr>";
            $fields = $result->fetch_fields();
            foreach ($fields as $field) {
                echo "<th>" . htmlspecialchars($field->name) . "</th>";
            }
            // Tambahkan header untuk aksi (Edit dan Delete)
            echo "<th>Actions</th>";
            echo "</tr></thead>";
            
            // Table rows
            echo "<tbody>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                foreach ($row as $key => $value) {
                    echo "<td>" . htmlspecialchars($value) . "</td>";
                }
                
                $id = $row['id']; 
                
                echo "<td>";
                echo "<a href='edit.php?tabel=$tabel&id=$id'>Edit</a> | ";
                echo "<a href='delete.php?tabel=$tabel&id=$id' onclick='return confirm(\"Are you sure you want to delete this item?\");'>Delete</a>";
                echo "</td>";
                
                echo "</tr>";
            }
            echo "</tbody>";
            
            // End table
            echo "</table>";
        } else {
            echo "<p>Tidak ada data pada $tabel.</p>";
        }

        $conn->close();
    } else {
        echo "<p>Invalid table specified.</p>";
    }
}
?>
