<?php

namespace Tests\Unit\Service;

use App\Model\Customer;

// test double: always finds the same test customer
class FakeCustomerRepository
{
    public function findById($custNo)
    {
        return new Customer([
            'CustNo' => $custNo,
            'Forename' => 'Test',
            'Surname' => 'Customer',
            'Email' => 'test@example.com',
            'LicenceNo' => 1234,
            'Status' => 'A',
        ]);
    }
}
