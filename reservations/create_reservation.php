<?php
require_once('../config/db_connection.php');

include_once('../includes/header.php');

$selectedMotorcycle = isset($_GET['moto']) ? $_GET['moto'] : '';

// Get all active customers
try {
    $sql = "SELECT CustNo, Forename, Surname, Email FROM customer WHERE Status = 'A' ORDER BY Surname, Forename";
    $customersResult = $pdo->query($sql);
    $customers = $customersResult->fetchAll();
    
    if (count($customers) == 0) {
        echo '<div class="ui warning message">
                <div class="header">No Active Customers</div>
                <p>No active customers found. Please add a customer first.</p>
              </div>';
        echo '<div class="ui segment">
                <a href="../customers/add_customer.php" class="ui primary button">
                    <i class="user plus icon"></i> Add Customer
                </a>
              </div>';
        include_once('../includes/footer.php');
        exit();
    }
    
    // Get all available motorcycles
    $sql = "SELECT m.RegNo, m.MBcode, mm.Make, mm.Model, mm.Rate 
            FROM motorcycle m 
            JOIN makemodel mm ON m.MBcode = mm.MBcode
            WHERE m.Status = 'A' 
            ORDER BY mm.Make, mm.Model";
    $motorcyclesResult = $pdo->query($sql);
    $motorcycles = $motorcyclesResult->fetchAll();
    
    if (count($motorcycles) == 0) {
        echo '<div class="ui warning message">
                <div class="header">No Available Motorcycles</div>
                <p>No available motorcycles found.</p>
              </div>';
        echo '<div class="ui segment">
                <a href="../motorcycles/view_motorcycles.php" class="ui button">
                    <i class="arrow left icon"></i> View Motorcycles
                </a>
              </div>';
        include_once('../includes/footer.php');
        exit();
    }
} 
catch (PDOException $e) {
    echo '<div class="ui negative message">
            <div class="header">Error</div>
            <p>Error fetching data: ' . $e->getMessage() . '</p>
          </div>';
    include_once('../includes/footer.php');
    exit();
}

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Get form data
        $custNo = $_POST['customer'];
        $regNo = $_POST['motorcycle'];
        $startDate = $_POST['start_date'];
        $endDate = $_POST['end_date'];
        
        // Validate dates
        $start = new DateTime($startDate);
        $end = new DateTime($endDate);
        $today = new DateTime();
        
        if ($start < $today) {
            throw new Exception("Start date cannot be in the past.");
        }
        
        if ($end <= $start) {
            throw new Exception("End date must be after start date.");
        }
        
        //price calculation
        $sql = "SELECT m.MBcode, mm.Rate FROM motorcycle m JOIN makemodel mm ON m.MBcode = mm.MBcode WHERE m.RegNo = :regNo";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':regNo', $regNo);
        $stmt->execute();
        $moto = $stmt->fetch();

        // calculate the number of days between start and end dates 
        // https://www.uptimia.com/questions/how-to-calculate-the-difference-between-two-dates-in-php
        $interval = $start->diff($end);
        $days = $interval->days;
        
        
        $price = $days * $moto['Rate'];
        
        // if motorcycle is available for the selected dates
        // 1--Check if the start date is within an existing reservation
        // 2--Check if the end date is within an existing reservation
        // 3--Check if the reservation is completely within an existing reservation

        $sql = "SELECT COUNT(*) FROM reservation 
                WHERE RegNo = :regNo 
                AND Status = 'A'
                AND (
                    (StartDate <= :startDate AND EndDate >= :startDate) OR  
                    (StartDate <= :endDate AND EndDate >= :endDate) OR      
                    (StartDate >= :startDate AND EndDate <= :endDate)       
                )";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':regNo', $regNo);
        $stmt->bindValue(':startDate', $startDate);
        $stmt->bindValue(':endDate', $endDate);
        $stmt->execute();
        
        if ($stmt->fetchColumn() > 0) {
            throw new Exception("This motorcycle is not available for the selected dates. Please choose different dates.");
        }
        
        // creating the reservation
        $sql = "INSERT INTO reservation (RegNo, MBcode, CustNo, StartDate, EndDate, Price, Status)
                VALUES (:regNo, :mbCode, :custNo, :startDate, :endDate, :price, 'A')";
        $stmt = $pdo->prepare($sql);
        $stmt->bindValue(':regNo', $regNo);
        $stmt->bindValue(':mbCode', $moto['MBcode']);
        $stmt->bindValue(':custNo', $custNo);
        $stmt->bindValue(':startDate', $startDate);
        $stmt->bindValue(':endDate', $endDate);
        $stmt->bindValue(':price', $price);
        $stmt->execute();
        
        // Redirect to reservations view with success message
        
        header('Location: view_reservations.php?action=added');
        exit();
    }
    catch (Exception $e) {
        $error = $e->getMessage();
    }
}
?>

<div class="ui segment">

    
<h2 class="ui header">
        <i class="calendar plus icon"></i>
        <div class="content">
            Create New Reservation
            <div class="sub header">Fill out the form below to create a new motorcycle reservation.</div>
        </div>
    </h2>

    <?php if (isset($error)): ?>
        <div class="ui negative message">
            <div class="header">Error</div>
            <p><?php echo $error; ?></p>
        </div>
    <?php endif; ?>

    <form class="ui form" action="create_reservation.php" method="post">
        <div class="field">

            <label for="customer">Select Customer:</label>
            <select id="customer" name="customer" class="ui dropdown" required>
                <option value="">Select Customer</option>
                    <!-- //-- Loop through customers and create options for the select dropdown -->
                <?php foreach ($customers as $customer): ?>
                    <option value="<?php echo $customer['CustNo']; ?>">
                        <?php echo $customer['Surname'] . ', ' . $customer['Forename'] . ' (' . $customer['Email'] . ')'; ?>
                    </option>

                <?php endforeach; ?>
            </select>
        </div>

        <div class="field">
   
    <label for="motorcycle">Select Motorcycle:</label>
    
    <!-- Dropdown for selecting a motorcycle -->
    <select id="motorcycle" name="motorcycle" class="ui dropdown" required>
     
        <option value="">Select Motorcycle</option>
        
        <!-- Loop through the list of motorcycles as motorcycle -->
        <?php foreach ($motorcycles as $motorcycle): ?>
            <option 
            
                value="<?php echo $motorcycle['RegNo']; ?>" 
                
                
                <?php 
                // If the current motorcycle matches the selected one, mark it as selected
                if ($selectedMotorcycle == $motorcycle['RegNo'] || (isset($_POST['motorcycle']) && $_POST['motorcycle'] == $motorcycle['RegNo'])) echo 'selected'; 
                ?>>
                
                
                

                <?php echo $motorcycle['Make'] . ' ' . $motorcycle['Model'] . ' (' . $motorcycle['RegNo'] . ') - €' . $motorcycle['Rate'] . '/day'; ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>




        <div class="two fields">
            <div class="field">
                <label for="start_date">Start Date:</label>
                <div class="ui calendar" id="start_calendar">
                    <div class="ui input left icon">
                        <i class="calendar icon"></i>
                        <input type="date" id="start_date" name="start_date" required value="<?= $_POST['start_date'] ?? '' ?>">
                        </div>
                </div>
            </div>




            <div class="field">
                <label for="end_date">End Date:</label>
                <div class="ui calendar" id="end_calendar">
                    <div class="ui input left icon">
                        <i class="calendar icon"></i>
                        <input type="date" id="end_date" name="end_date" required value="<?= $_POST['end_date'] ?? '' ?>">
                    </div>
                </div>
            </div>
        </div>

        <?php
    // Show price calculation after form submission and if no error
    
if ($_SERVER['REQUEST_METHOD'] == 'POST' ) {
    // $price, $days, and $moto['Rate'] are already calculated in form handler above
    ?>
    <div class="ui segment" id="price_calculation">
        <h3 class="ui header">Price Calculation</h3>
        <div class="ui list">
            <div class="item">
                <div class="header">Daily Rate:</div>
                <span id="daily_rate">€<?php echo ($moto['Rate']); ?></span>
            </div>
            <div class="item">
                <div class="header">Number of Days:</div>
                <span id="num_days"><?php echo $days; ?></span>
            </div>
            <div class="item">
                <div class="header">Total Price:</div>
                <div class="ui large label">
                    <i class="euro sign icon"></i> <span id="total_price"><?php echo ($price); ?></span>
                </div>
            </div>
        </div>
    </div>
    <?php
}
?>

        <div class="ui divider"></div>

        <button class="ui primary button" type="submit">
            <i class="save icon"></i> Create Reservation
        </button>
        <a href="view_reservations.php" class="ui button">
            <i class="cancel icon"></i> Cancel
        </a>
    </form>
</div>

<script>
    
</script>

<?php
include_once('../includes/footer.php');
?>