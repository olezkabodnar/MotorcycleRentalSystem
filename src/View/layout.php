<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Motorcycle Rental System</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.css">

</head>  <!--Copied layout from ..\includes folder -->
<body>
    <div class="container">
        <div class="ui left fixed vertical menu">
    <div class="item">
        <h3><i class="motorcycle icon"></i> Motorcycle Rental System</h3>
    </div>
    <a href="../motorcycles/view_motorcycles.php" class="item">
        <i class="motorcycle icon"></i> Browse Motorcycles
    </a>
    <a href="../reservations/view_reservations.php" class="item">
        <i class="calendar alternate icon"></i> Bookings
    </a>
    <a href="/MotorcycleRentalSystem/public/customers" class="item">
        <i class="users icon"></i> Customers
    </a>
    <a href="../reservations/create_reservation.php" class="item">
        <i class="plus circle icon"></i> New Rental
    </a>
</div>
        
        <div class="pusher" style="margin-left: 240px; padding: 10px;">
        <?php include __DIR__ . '/' . $template . '.php'; ?>


        <div class="ui divider"></div>
           
        </div> 
    </div> 
    
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    
    <script src="https://cdn.jsdelivr.net/npm/fomantic-ui@2.9.2/dist/semantic.min.js"></script>
</body>
</html>


