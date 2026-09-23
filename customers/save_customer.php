<?php
require_once('../config/db_connection.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {// checks if the form was submitted
    try {
        $forename = ($_POST['forename']);
        $surname = ($_POST['surname']);
        $email = ($_POST['email']);
        $licence = ($_POST['licence']);
        $status = $_POST['status'];

        if (empty($forename) || empty($surname) || empty($email) || empty($licence)) {
            throw new Exception("All fields are required.");//Validation check
        }

        $sql = "INSERT INTO customer (Forename, Surname, Email, LicenceNo, Status) 
                VALUES (:forename, :surname, :email, :licence, :status)";//
        
        $stmt = $pdo->prepare($sql);
        
        $stmt->bindValue(':forename', $forename);//
        $stmt->bindValue(':surname', $surname);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':licence', $licence);
        $stmt->bindValue(':status', $status);
        
        $stmt->execute();
        
        // Redirect back to the view customers page with success message
        // see row 15-35 in view_customers.php
        header('Location: view_customers.php?action=added');
        exit();
    } 
    catch (PDOException $e) {
    
        $error = 'Error: ' . $e->getMessage();
        include_once('../includes/header.php');
        echo '<div class="ui negative message">  
                <div class="header">Error</div>
                <p>' . $error . '</p>
              </div>';
        echo '<div class="ui segment">
                <a href="add_customer.php" class="ui button">
                    <i class="arrow left icon"></i> Go back to form
                </a>
              </div>';
        include_once('../includes/footer.php');
        exit();
    }
} 
else {
    //https://www.w3schools.com/php/func_network_header.asp HTTP header function
    // Redirect to the form page if accessed directly
    header('Location: add_customer.php');
    exit();
}
?>