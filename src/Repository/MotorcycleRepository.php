<?php

namespace App\Repository;

use App\Model\Motorcycle;

// Repository: all SQL for the motorcycle and makemodel tables
class MotorcycleRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $sql = 'SELECT m.RegNo, m.MBcode, m.Status, mm.Make, mm.Model, mm.Rate
                FROM motorcycle m
                JOIN makemodel mm ON m.MBcode = mm.MBcode
                ORDER BY mm.Make, mm.Model, m.RegNo';
        $stmt = $this->pdo->query($sql);

        $motorcycles = [];
        foreach ($stmt->fetchAll() as $row) {
            $motorcycles[] = new Motorcycle($row);
        }
        return $motorcycles;
    }
}
