<?php

namespace App\Model;

class Customer
{
    public $id;
    public $forename;
    public $surname;
    public $email;
    public $licenceNo;
    public $status;

    public function __construct($row)
    {
        $this->id = $row['CustNo'];
        $this->forename = $row['Forename'];
        $this->surname = $row['Surname'];
        $this->email = $row['Email'];
        $this->licenceNo = $row['LicenceNo'];
        $this->status = $row['Status'];
    }

    public function fullName()
    {
        return $this->forename . ' ' . $this->surname;
    }

    public function isActive()
    {
        return $this->status == 'A';
    }
}
