<?php

require_once('../config/db_connection.php');


include_once('../includes/header.php');


$regNo = $_GET['id'];

//  motorcycle details
// --selecting columns from motorcycle and makemodel tables
// --joining motorcycle and makemodel tables which is available and have the same MBcode

try {
    $sql = 'SELECT m.RegNo, m.MBcode, m.Status, mm.Make, mm.Model, mm.Rate 
            FROM motorcycle m 
            JOIN makemodel mm ON m.MBcode = mm.MBcode 
            WHERE m.RegNo = :regNo';
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':regNo', $regNo);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) { // Check if motorcycle exists
        // If not, display an error message and exit
        echo '<div class="ui negative message">
                <div class="header">Error</div>
                <p>Motorcycle not found.</p>
              </div>';
        echo '<div class="ui segment">
                <a href="view_motorcycles.php" class="ui button">
                    <i class="arrow left icon"></i> Back to motorcycles
                </a>
              </div>';
        include_once('../includes/footer.php');
        exit();
    }
    
    $motorcycle = $stmt->fetch();
    //RESERVATION DETAILS
    // Get all reservations for the motorcycle
    // --selecting columns from reservation and customer tables
    // --joining reservation and customer tables which is available and have the same CustNo
    // --checking if the reservation is available
 
    $sql = 'SELECT r.Reservation_Id, r.StartDate, r.EndDate, r.Price, c.Forename, c.Surname 
            FROM reservation r 
            JOIN customer c ON r.CustNo = c.CustNo 
            WHERE r.RegNo = :regNo AND r.Status = "A" 
            ORDER BY r.StartDate'; 
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':regNo', $regNo);
    $stmt->execute();
    
    $reservations = $stmt->fetchAll();
} 
catch (PDOException $e) {
    // If an error occurs, display an error message and exit
    echo '<div class="ui negative message">
            <div class="header">Error</div>
            <p>Error fetching motorcycle details: ' . $e->getMessage() . '</p>
          </div>';
    echo '<div class="ui segment">
            <a href="view_motorcycles.php" class="ui button">
                <i class="arrow left icon"></i> Back to motorcycles
            </a>
          </div>';
    include_once('../includes/footer.php');
    exit();
}










?>

 <!-- MOTORCYCLE DETAILS PAGE -->

<div class="ui segments">
    <div class="ui segment">
        <h2 class="ui header">
            <i class="motorcycle icon"></i>
            <div class="content">
                Motorcycle Details
                <div class="sub header"><?php echo $motorcycle['Make'] . ' ' . $motorcycle['Model']; ?></div>
            </div>
        </h2>
    </div>
    
    <div class="ui segment">
        <div class="ui two column grid">
            <div class="column">
                <div class="ui card fluid">
                    <div class="content">
                        <div class="header">Information</div>
                    </div>
                    <div class="content">
                        <div class="ui list">
                            <div class="item">
                                <div class="header">Registration Number:</div>
                                <?php echo $motorcycle['RegNo']; ?> 
                            </div>
                            <div class="item">
                                <div class="header">Make/Model Code:</div>
                                <?php echo $motorcycle['MBcode']; ?>
                            </div>
                            <div class="item">
                                <div class="header">Daily Rate:</div>
                                <div class="ui label">
                                    <i class="euro sign icon"></i> <?php echo ($motorcycle['Rate'] ); ?>
                                </div>
                            </div>
                            <div class="item">
                                <div class="header">Status:</div>
                                <?php if ($motorcycle['Status'] == 'A'): ?>
                                    <div class="ui green label">Available</div>
                                <?php else: ?>
                                    <div class="ui grey label">Not Available</div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    <div class="extra content">
                        <!-- // button to create a reservation for the motorcycle -->
                        <a href="../reservations/create_reservation.php?moto=<?php echo $regNo; ?>" class="ui green button fluid">
                            <!-- when the user clicks on the button, it will redirect to create_reservation.php page with the id of the motorcycle -->
                            <i class="calendar plus icon"></i> Create Reservation
                        </a>
                    </div>
                </div>
            </div>
            


            <div class="column">/
                <div class="ui segment">
                    <h3 class="ui dividing header">Motorcycle Image</h3>
                       
                        <img src="../img/moped.png" class="ui medium image"> 
            </div>
        </div>
    </div>





    
    <div class="ui segment">
        <h3 class="ui dividing header">
            <i class="calendar alternate outline icon"></i> Upcoming Reservations
        </h3>
        
        <?php if (count($reservations) > 0): ?>
            <!-- // creating a table to display the reservations for the motorcycle if such created -->
            <table class="ui celled table">
                <thead>
                    <tr>
                        <th>Reservation ID</th>
                        <th>Customer</th>
                        <th>Start Date</th>
                        <th>End Date</th>
                        <th>Price</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($reservations as $reservation): ?>
                        <tr>
                            <!-- // displaying the reservation details in the table -->
                            <td><?php echo $reservation['Reservation_Id']; ?></td>
                            <td><?php echo $reservation['Forename'] . ' ' . $reservation['Surname']; ?></td>
                            <td><?php echo $reservation['StartDate']; ?></td>
                            <td><?php echo $reservation['EndDate']; ?></td>
                            <td>€<?php echo $reservation['Price']; ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php else: ?> 
            <!-- //if no reservations are found for the motorcycle, display a message -->
            <div class="ui info message">
                <div class="header">No Reservations</div>
                <p>No upcoming reservations for this motorcycle.</p>
            </div>
        <?php endif; ?>
    </div>
    
    <div class="ui segment">
        <a href="view_motorcycles.php" class="ui button">
            <i class="arrow left icon"></i> Back to Motorcycles
        </a>
    </div>
</div>

<?php

include_once('../includes/footer.php');
?>