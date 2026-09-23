<?php
require_once('../config/db_connection.php');
include_once('../includes/header.php');

try { //retrieve all customers from the database
    $sql = 'SELECT * FROM customer ORDER BY CustNo';
    $result = $pdo->query($sql);
} catch (PDOException $e) {
    $output = 'Error fetching customers: ' . $e->getMessage();
    echo $output;
    exit();
}


if (isset($_GET['action'])) {// check if an action is set in the URL and display corresponding message
    if ($_GET['action'] == 'added') {
        echo '<div class="ui positive message">
                <i class="close icon"></i>
                <div class="header">Success!</div>
                <p>Customer added successfully.</p>
              </div>';
    } elseif ($_GET['action'] == 'updated') {
        echo '<div class="ui positive message">
                <i class="close icon"></i>
                <div class="header">Success!</div>
                <p>Customer updated successfully.</p>
              </div>';
    } elseif ($_GET['action'] == 'deleted') {
        echo '<div class="ui positive message">
                <i class="close icon"></i>
                <div class="header">Success!</div>
                <p>Customer deleted successfully.</p>
              </div>';
    }
}
?>

<div class="ui segment">
    <h2 class="ui header">
        <i class="users icon"></i>
        <div class="content">
            View All Customers
            <div class="sub header">Manage customer information from this page.</div>
        </div>
    </h2>

    <!-- Add new customer button -->
    <div class="ui padded basic segment">
        <a href="add_customer.php" class="ui primary button">
            <i class="user plus icon"></i> Add New Customer
        </a>
    </div>

    <!-- Display customers in a table -->
    <table class="ui celled table">
        <thead>
            <tr>
                <th>Customer ID</th>
                <th>Name</th>
                <th>Email</th>
                <th>License No</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch()) { ?>
                <tr>
                    <td><?php echo $row['CustNo']; ?></td>
                    <td><?php echo $row['Forename'] . ' ' . $row['Surname']; ?></td>
                    <td><?php echo $row['Email']; ?></td>
                    <td><?php echo $row['LicenceNo']; ?></td>
                    <td>
                        <?php if ($row['Status'] == 'A'): ?>
                            <div class="ui green label">Active</div>
                        <?php else: ?>
                            <div class="ui grey label">Inactive</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <a href="update_customer.php?id=<?php echo $row['CustNo']; ?>" class="ui small blue button">
                            <i class="edit icon"></i> Edit
                        </a>
                        <a href="delete_customer.php?id=<?php echo $row['CustNo']; ?>" class="ui small red button" >
                            <i class="trash icon"></i> Delete
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<script>

</script>

<?php

include_once('../includes/footer.php');
?>