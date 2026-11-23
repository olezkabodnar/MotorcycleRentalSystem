<?php

namespace Tests\Unit\Service;

// test double: remembers what the service saved instead of using MySQL
class FakeReservationRepository
{
    public $available = true;
    public $motorcycle = ['RegNo' => '25-KE-7788', 'MBcode' => 'yam70', 'Rate' => 50.00];
    public $reservations = [];
    public $saved = [];
    public $savedExtras = [];
    public $cancelled = [];

    public function findAll()
    {
        return $this->reservations;
    }

    public function findMotorcycle($regNo)
    {
        return $this->motorcycle;
    }

    public function isAvailable($regNo, $start, $end)
    {
        return $this->available;
    }

    public function insert($row, $extras)
    {
        $this->saved[] = $row;
        $this->savedExtras[] = $extras;

        // new reservation id
        return count($this->saved);
    }

    public function findById($id)
    {
        if (!isset($this->reservations[$id])) {
            return false;
        }
        return $this->reservations[$id];
    }

    public function cancel($id)
    {
        $this->cancelled[] = $id;
    }
}
