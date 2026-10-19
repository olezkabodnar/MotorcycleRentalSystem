<?php

namespace App\Service;

// Service Layer: business rules for customers (FR1)
class CustomerService
{
    private $customers;

    public function __construct($customers)
    {
        $this->customers = $customers;
    }

    public function listCustomers()
    {
        return $this->customers->findAll();
    }

    // TODO: delete rule and validation 


}
