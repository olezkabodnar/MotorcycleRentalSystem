<?php
require_once('../config/db_connection.php');

include_once('../includes/header.php');

//retrieve motorcycles with make and model information
try {
    // --selecting columns from motorcycle and makemodel tables
    // --joining motorcycle and makemodel tables which is available and have the same MBcode
    $sql = 'SELECT m.RegNo, m.Status, mm.Make, mm.Model, mm.Rate 
            FROM motorcycle m  
            JOIN makemodel mm ON m.MBcode = mm.MBcode 
            WHERE m.Status = "A" 
            ORDER BY m.RegNo';
    $result = $pdo->query($sql);
} catch (PDOException $e) {
    $output = 'Error fetching motorcycles: ' . $e->getMessage();
    echo '<div class="ui negative message">
            <div class="header">Error</div>
            <p>' . $output . '</p>
          </div>';
    include_once('../includes/footer.php');
    exit();
}
?>
<!-- display motorcycles in a table -->
<div class="ui segment">
    <h2 class="ui header">
        <i class="motorcycle icon"></i>
        <div class="content">
            View All Motorcycles
            <div class="sub header">View information about available motorcycles for rental.</div>
        </div>
    </h2>

 
    <table class="ui celled table">
        <thead>
            <tr>
                <th>Registration No</th>
                <th>Make</th>
                <th>Model</th>
                <th>Daily Rate</th>
                <th>Status</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch()) { ?>
                <tr>
                    <td><?php echo $row['RegNo']; ?></td>
                    <td><?php echo $row['Make']; ?></td>
                    <td><?php echo $row['Model']; ?></td>
                    <td>€<?php echo ($row['Rate']); ?></td>
                    <!-- /formatting the rate to 2 decimal places -->
                    <td>
                        <?php if ($row['Status'] == 'A'): ?>

                            <div class="ui green label">Available</div>

                        <?php else: ?> 
                            <!-- depending on the status of the motorcycle, it will show available or not available -->

                            <div class="ui grey label">Not Available</div>

                        <?php endif; ?>
                    </td>

                    <td>
                        <a href="motorcycle_details.php?id=<?php echo $row['RegNo']; ?>" class="ui small blue button">
                            <!-- when the user clicks on the button, it will redirect to motorcycle_details.php page with the id of the motorcycle -->
                            <i class="eye icon"></i> View Details
                        </a>
                    </td>
                </tr>
            <?php } ?>
        </tbody>
    </table>
</div>

<?php
include_once('../includes/footer.php');
?>