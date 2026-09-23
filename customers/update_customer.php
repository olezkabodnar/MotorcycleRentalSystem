<?php
// Include database connection
require_once('../config/db_connection.php');

// Include header
include_once('../includes/header.php');

// Check if ID is provided
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo '<div class="ui negative message">
            <div class="header">Error</div>
            <p>No customer ID provided.</p>
          </div>';
    echo '<div class="ui segment">
            <a href="view_customers.php" class="ui button">
                <i class="arrow left icon"></i> Back to customers
            </a>
          </div>';
    include_once('../includes/footer.php');
    exit();
}

$id = $_GET['id'];

// Get the customer details
try {
    $sql = 'SELECT * FROM customer WHERE CustNo = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo '<div class="ui negative message">
                <div class="header">Error</div>
                <p>Customer not found.</p>
              </div>';
        echo '<div class="ui segment">
                <a href="view_customers.php" class="ui button">
                    <i class="arrow left icon"></i> Back to customers
                </a>
              </div>';
        include_once('../includes/footer.php');
        exit();
    }
    
    $customer = $stmt->fetch();
} 
catch (PDOException $e) {
    echo '<div class="ui negative message">
            <div class="header">Error</div>
            <p>Error fetching customer: ' . $e->getMessage() . '</p>
          </div>';
    echo '<div class="ui segment">
            <a href="view_customers.php" class="ui button">
                <i class="arrow left icon"></i> Back to customers
            </a>
          </div>';
    include_once('../includes/footer.php');
    exit();
}

// Process form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get form data
        $forename = trim($_POST['forename']);
        $surname = trim($_POST['surname']);
        $email = trim($_POST['email']);
        $licence = trim($_POST['licence']);
        $status = $_POST['status'];
        
        // Validate input
        if (empty($forename) || empty($surname) || empty($email) || empty($licence)) {
            throw new Exception("All fields are required.");
        }
        
        // Update customer
        $sql = 'UPDATE customer 
                SET Forename = :forename, 
                    Surname = :surname, 
                    Email = :email, 
                    LicenceNo = :licence, 
                    Status = :status 
                WHERE CustNo = :id';
                
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':forename', $forename);
        $stmt->bindValue(':surname', $surname);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':licence', $licence);
        $stmt->bindValue(':status', $status);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
       
        // see row 15-35 in view_customers.php
        // Redirect back to the view customers page with success message
        header('Location: view_customers.php?action=updated');
        exit();
    } 
    catch (PDOException $e) {
        $error = 'Database error: ' . $e->getMessage();
    }
  
}
?>

<div class="ui segment">
    <h2 class="ui header">
        <i class="edit icon"></i>
        <div class="content">
            Update Customer
            <div class="sub header">Update the customer information below.</div>
        </div>
    </h2>

    <?php if (isset($error)): ?>    <!-- Display error message if any -->
        <div class="ui negative message">
            <div class="header">Error</div>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form class="ui form" action="update_customer.php?id=<?php echo $id; ?>" method="post">
        <div class="field">
            <label for="forename">First Name:</label>
            <input type="text" id="forename" name="forename" value="<?php echo ($customer['Forename']); ?>" required>
        </div>

        <div class="field">
            <label for="surname">Last Name:</label>
            <input type="text" id="surname" name="surname" value="<?php echo ($customer['Surname']); ?>" required>
        </div>

        <div class="field">
            <label for="email">Email Address:</label>
            <input type="email" id="email" name="email" value="<?php echo ($customer['Email']); ?>" required>
        </div>

        <div class="field">
            <label for="licence">License Number:</label>
            <input type="number" id="licence" name="licence" value="<?php echo ($customer['LicenceNo']); ?>" required>
        </div>

        <div class="field">
            <label for="status">Status:</label>
            <select id="status" name="status" class="ui dropdown">
                <option value="A" <?php if ($customer['Status'] == 'A') echo 'selected'; ?>>Active</option> 
                <!-- //change customer statuses to active or inactive -->
                <option value="I" <?php if ($customer['Status'] == 'I') echo 'selected'; ?>>Inactive</option> 
            </select>
        </div>

        <div class="ui divider"></div>
        
        <button class="ui primary button" type="submit">
            <i class="save icon"></i> Update Customer
        </button>
        <a href="view_customers.php" class="ui button">
            <i class="cancel icon"></i> Cancel
        </a>
    </form>
</div>

<script>
    // Dropdown form
    $('.ui.dropdown').dropdown();
</script>

<?php
// Include footer
include_once('../includes/footer.php');
?>