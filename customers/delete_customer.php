<?php
require_once('../config/db_connection.php');

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

// Check if the customer has any reservations before deleting
try {
    $sql = 'SELECT COUNT(*) FROM reservation WHERE CustNo = :id';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    
    $reservationCount = $stmt->fetchColumn();
    
    if ($reservationCount > 0) {
        echo '<div class="ui negative message">
                <div class="header">Cannot Delete Customer</div>
                <p>This customer has ' . $reservationCount . ' reservation(s). 
                You must first delete these reservations or assign them to another customer.</p>
              </div>';
        echo '<div class="ui segment">
                <a href="view_customers.php" class="ui button">
                    <i class="arrow left icon"></i> Back to customers
                </a>
              </div>';
        include_once('../includes/footer.php');
        exit();
    }
    
    // Get customer details for confirmation
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
            <p>Error checking customer: ' . $e->getMessage() . '</p>
          </div>';
    echo '<div class="ui segment">
            <a href="view_customers.php" class="ui button">
                <i class="arrow left icon"></i> Back to customers
            </a>
          </div>';
    include_once('../includes/footer.php');
    exit();
}

// Process confirmation
if (isset($_POST['confirm_delete']) && $_POST['confirm_delete'] == 'yes') {
    try {
        // Delete the customer
        $sql = 'DELETE FROM customer WHERE CustNo = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        // Redirect with success message
        header('Location: view_customers.php?action=deleted');
        exit();
    } 
    catch (PDOException $e) {
        echo '<div class="ui negative message">
                <div class="header">Error</div>
                <p>Error deleting customer: ' . $e->getMessage() . '</p>
              </div>';
    }
}
?>

<div class="ui segment">
    <h2 class="ui header">
        <i class="trash icon"></i>
        <div class="content">
            Delete Customer
            <div class="sub header">Are you sure you want to delete the following customer?</div>
        </div>
    </h2>

    <div class="ui warning message">
        <div class="header">Warning!</div>
        <p>This action cannot be undone. Please confirm that you want to delete this customer.</p>
    </div>

    <div class="ui segment">
        <h3 class="ui header">Customer Details</h3>
        <div class="ui list">
            <div class="item">
                <div class="header">Customer ID:</div>
                <?php echo $customer['CustNo']; ?>
            </div>
            <div class="item">
                <div class="header">Name:</div>
                <?php echo $customer['Forename'] . ' ' . $customer['Surname']; ?>
            </div>
            <div class="item">
                <div class="header">Email:</div>
                <?php echo $customer['Email']; ?>
            </div>
            <div class="item">
                <div class="header">License No:</div>
                <?php echo $customer['LicenceNo']; ?>
            </div>
        </div>
    </div>

    <form class="ui form" action="delete_customer.php?id=<?php echo $id; ?>" method="post">
        <input type="hidden" name="confirm_delete" value="yes">
        <button class="ui negative button" type="submit">
            <i class="trash icon"></i> Yes, Delete Customer
        </button>
        <a href="view_customers.php" class="ui button">
            <i class="cancel icon"></i> No, Cancel
        </a>
    </form>
</div>

<?php
include_once('../includes/footer.php');
?>