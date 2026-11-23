<?php

namespace App\Repository;

use App\Model\Reservation;

// Repository: all SQL for the reservation table
// REFACTORED: Duplicated Code - reservation SQL was spread across the legacy reservations/*.php pages
class ReservationRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    // newest first, with customer and motorcycle names for the list page
    public function findAll()
    {
        $sql = 'SELECT r.*, c.Forename, c.Surname, mm.Make, mm.Model
                FROM reservation r
                LEFT JOIN customer c ON r.CustNo = c.CustNo
                LEFT JOIN makemodel mm ON r.MBcode = mm.MBcode
                ORDER BY r.StartDate DESC, r.Reservation_Id DESC';
        $stmt = $this->pdo->query($sql);

        $reservations = [];
        foreach ($stmt->fetchAll() as $row) {
            $reservations[] = new Reservation($row);
        }
        return $reservations;
    }

    // TODO: findMotorcycle, isAvailable, insert, findById, cancel
}
