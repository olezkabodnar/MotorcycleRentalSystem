<?php

namespace App\Repository;

use App\Model\Customer;

// Repository: all SQL for the customer table
// REFACTORED: Duplicated Code - SQL was repeated across the legacy customers/*.php pages
class CustomerRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }
 
    public function findAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM customer ORDER BY CustNo');

        $customers = [];
        foreach ($stmt->fetchAll() as $row) {
            $customers[] = new Customer($row);
        }
        return $customers;
    }

    // returns false when there is no such customer
    public function findById($custNo)
    {
        $stmt = $this->pdo->prepare('SELECT * FROM customer WHERE CustNo = :custNo');
        $stmt->execute(['custNo' => $custNo]);

        $row = $stmt->fetch();
        if (!$row) {
            return false;
        }
        return new Customer($row);
    }

    // TODO: create, update, delete
}
