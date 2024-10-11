<?php
include './config/config.php';
include './header.php';
include './read.php'; // Include read.php once
?>

<div class="container">
    <?php
function displayTable($tabel) {
    // Display header
    echo "<h2>Data " . str_replace('_', ' ', ucfirst($tabel)) . "</h2>";
    
    // Call the function to handle data fetching for the table
    getTableData($tabel);
}

displayTable('account');
displayTable('business_account');
displayTable('user_account');
displayTable('product');
displayTable('transaction');
displayTable('detail_pesanan');
?>
</div>

<?php
include './footer.php';
?>
