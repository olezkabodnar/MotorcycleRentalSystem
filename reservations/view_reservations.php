<?php
require_once('../config/db_connection.php');

include_once('../includes/header.php');

if (isset($_GET['action'])) {// Check if an action is set in the URL and display corresponding message
    // Display success messages based on the action performed

    if ($_GET['action'] == 'added') {
        echo '<div class="ui positive message">
                <i class="close icon"></i>
                <div class="header">Success!</div>
                <p>Reservation added successfully.</p>
              </div>';
    } elseif ($_GET['action'] == 'cancelled') {
        echo '<div class="ui positive message">
                <i class="close icon"></i>
                <div class="header">Success!</div>
                <p>Reservation cancelled successfully.</p>
              </div>';
    }
}

// Get all reservations with related information
// --selecting columns from reservation table
// --selecting columns from motorcycle and makemodel tables
// --selecting columns from customer table
// --joining reservation and motorcycle tables which is available and have the same RegNo
// --joining reservation and makemodel tables which is available and have the same MBcode
// --joining reservation and customer tables which is available and have the same CustNo

try {
    $sql = 'SELECT r.Reservation_Id, r.StartDate, r.EndDate, r.Price, r.Status, 
            m.RegNo, mm.Make, mm.Model,         
            c.CustNo, c.Forename, c.Surname  
            FROM reservation r 
            JOIN motorcycle m ON r.RegNo = m.RegNo 
            JOIN makemodel mm ON r.MBcode = mm.MBcode 
            JOIN customer c ON r.CustNo = c.CustNo 
            WHERE r.Status = "A" 
            ORDER BY r.StartDate DESC'; //selecting all the reservations which is available and ordering it by start date in descending order

    $result = $pdo->query($sql);

} catch (PDOException $e) {
    $output = 'Error fetching reservations: ' . $e->getMessage();
    echo '<div class="ui negative message">
            <div class="header">Error</div>
            <p>' . $output . '</p>
          </div>';
    include_once('../includes/footer.php');
    exit();
}
?>

<div class="ui segment">
    <h2 class="ui header">
        <i class="calendar alternate icon"></i>
        <div class="content">
            View All Reservations
            <div class="sub header">Manage motorcycle reservations from this page.</div>
        </div>
    </h2>

    <!-- Add new reservation button -->
    <div class="ui padded basic segment">
        <a href="create_reservation.php" class="ui primary button">
            <i class="plus circle icon"></i> Create New Reservation
        </a>
    </div>

    <!-- Display reservations in a table -->
    <table class="ui celled table">
        <thead>
            <tr>
                <th>ID</th>
                <th>Customer</th>
                <th>Motorcycle</th>
                <th>Start Date</th>
                <th>End Date</th>
                <th>Price</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch()) { ?>
                <tr>
                    <td><?php echo $row['Reservation_Id']; ?></td>
                    <td><?php echo $row['Forename'] . ' ' . $row['Surname']; ?></td>
                    <td><?php echo $row['Make'] . ' ' . $row['Model'] . ' (' . $row['RegNo'] . ')'; ?></td>
                    <td><?php echo $row['StartDate']; ?></td>
                    <td><?php echo $row['EndDate']; ?></td>
                    <td>
                        <div class="ui label">
                            <?php echo $row['Price']; ?>
                        </div>
                    </td>
                    <td>
                        <?php if ($row['Status'] == 'A'): ?>
                            <div class="ui green label">Active</div>
                        <?php else: ?>
                            <div class="ui grey label">Cancelled</div>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($row['Status'] == 'A'): ?>
                            <a href="cancel_reservation.php?id=<?php echo $row['Reservation_Id']; ?>" 
                               class="ui small red button" 
                               >
                                <i class="ban icon"></i> Cancel
                            </a>
                        <?php else: ?>
                            <span class="ui small disabled button">
                                <i class="ban icon"></i> Cancelled
                            </span>
                        <?php endif; ?>
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