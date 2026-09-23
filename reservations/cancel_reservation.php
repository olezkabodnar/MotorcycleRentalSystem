<?php
require_once('../config/db_connection.php');
include_once('../includes/header.php');

if (!isset($_GET['id']) || empty($_GET['id'])) {// Check if ID is provided
    echo '<div class="ui negative message">   
            <div class="header">Error</div>
            <p>No reservation ID provided.</p>
          </div>';
    echo '<div class="ui segment">
            <a href="view_reservations.php" class="ui button">
                <i class="arrow left icon"></i> Back to reservations
            </a>
          </div>';
    include_once('../includes/footer.php');
    exit();
}

$id = $_GET['id'];

// Get the reservation details from reservation table and join with motorcycle, makemodel, and customer tables
// to get the motorcycle and customer details
try {
    $sql = 'SELECT r.Reservation_Id, r.StartDate, r.EndDate, r.Price, 
            m.RegNo, mm.Make, mm.Model,
            c.Forename, c.Surname
            FROM reservation r 
            JOIN motorcycle m ON r.RegNo = m.RegNo
            JOIN makemodel mm ON r.MBcode = mm.MBcode
            JOIN customer c ON r.CustNo = c.CustNo
            WHERE r.Reservation_Id = :id AND r.Status = "A"';


    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':id', $id);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        echo '<div class="ui negative message">
                <div class="header">Error</div>
                <p>Reservation not found or already cancelled.</p>
              </div>';
        echo '<div class="ui segment">
                <a href="view_reservations.php" class="ui button">
                    <i class="arrow left icon"></i> Back to reservations
                </a>
              </div>';
        include_once('../includes/footer.php');
        exit();
    }
    
    $reservation = $stmt->fetch();
} 
catch (PDOException $e) {
    echo '<div class="ui negative message">
            <div class="header">Error</div>
            <p>Error fetching reservation: ' . $e->getMessage() . '</p>
          </div>';
    echo '<div class="ui segment">
            <a href="view_reservations.php" class="ui button">
                <i class="arrow left icon"></i> Back to reservations
            </a>
          </div>';
    include_once('../includes/footer.php');
    exit();
}

// Process confirmation
if (isset($_POST['confirm_cancel']) && $_POST['confirm_cancel'] == 'yes') {
    try {
        // Update the reservation status to cancelled
        $sql = 'UPDATE reservation SET Status = "C" WHERE Reservation_Id = :id';
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':id', $id);
        $stmt->execute();
        
        // Redirect with success message
        header('Location: view_reservations.php?action=cancelled');
        exit();
    } 
    catch (PDOException $e) {
        echo '<div class="ui negative message">
                <div class="header">Error</div>
                <p>Error cancelling reservation: ' . $e->getMessage() . '</p>
              </div>';
    }
}
?>

<div class="ui segment">
    <h2 class="ui header">
        <i class="ban icon"></i>
        <div class="content">
            Cancel Reservation
            <div class="sub header">Are you sure you want to cancel the following reservation?</div>
        </div>
    </h2>

    <div class="ui warning message">
        <div class="header">Warning!</div>
        <p>This action will cancel the reservation. Please confirm your decision.</p>
    </div>

    <div class="ui segment">
        <h3 class="ui header">Reservation Details</h3>
        <div class="ui divided list">
            <div class="item">
                <div class="header">Reservation ID:</div>
                <?php echo $reservation['Reservation_Id']; ?>
            </div>
            <div class="item">
                <div class="header">Customer:</div>
                <?php echo $reservation['Forename'] . ' ' . $reservation['Surname']; ?>
            </div>
            <div class="item">
                <div class="header">Motorcycle:</div>
                <?php echo $reservation['Make'] . ' ' . $reservation['Model'] . ' (' . $reservation['RegNo'] . ')'; ?>
            </div>
            <div class="item">
                <div class="header">Start Date:</div>
                <?php echo $reservation['StartDate']; ?>
            </div>
            <div class="item">
                <div class="header">End Date:</div>
                <?php echo $reservation['StartDate']; ?>
            </div>
            <div class="item">
                <div class="header">Price:</div>
                <div class="ui label">
                    <i class="euro sign icon"></i> <?php echo ($reservation['Price']); ?>
                </div>
            </div>
        </div>
    </div>

    <form class="ui form" action="cancel_reservation.php?id=<?php echo $id; ?>" method="post">

        <input type="hidden" name="confirm_cancel" value="yes">
        <button class="ui negative button" type="submit">
            <i class="ban icon"></i> Yes, Cancel Reservation
        </button>
        <a href="view_reservations.php" class="ui button">
            <i class="cancel icon"></i> No, Go Back
        </a>
    </form>
</div>

<?php
// Include footer
include_once('../includes/footer.php');
?>