<?php
require_once('../config/db_connection.php');

include_once('../includes/header.php');
?>

<div class="ui segment">
    <h2 class="ui header">
        <i class="user plus icon"></i>
        <div class="content">
            Add New Customer
            <div class="sub header">Fill out the form below to add a new customer.</div>
        </div>
    </h2>

    <form class="ui form" action="save_customer.php" method="post">
        <div class="field">
            <label for="forename">First Name:</label>
            <input type="text" id="forename" name="forename" placeholder="Enter first name" required>
        </div>

        <div class="field">
            <label for="surname">Last Name:</label>
            <input type="text" id="surname" name="surname" placeholder="Enter last name" required>
        </div>

        <div class="field">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" placeholder="Enter email address" required>
        </div>

        <div class="field">
            <label for="licence">License Number:</label>
            <input type="number" id="licence" name="licence" placeholder="Enter license number" required>
        </div>

        <div class="field">
            <label for="status">Status:</label>
            <select id="status" name="status" class="ui dropdown">
                <option value="A">Active</option>
                <option value="I">Inactive</option>
            </select>
        </div>

        <div class="ui divider"></div>
        
        <button class="ui primary button" type="submit">
            <i class="save icon"></i> Add Customer
        </button>
        <a href="view_customers.php" class="ui button">
            <i class="cancel icon"></i> Cancel
        </a>
    </form>
</div>

<script>
    $('.ui.dropdown').dropdown();
</script>

<?php
// Include footer
include_once('../includes/footer.php');
?>