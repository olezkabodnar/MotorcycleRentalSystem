<?php

namespace App\Repository;

use App\Model\Reservation;
use Exception;

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

    public function findMotorcycle($regNo)
    {
        $sql = 'SELECT m.RegNo, m.MBcode, m.Status, mm.Make, mm.Model, mm.Rate
                FROM motorcycle m
                JOIN makemodel mm ON m.MBcode = mm.MBcode
                WHERE m.RegNo = :regNo';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute(['regNo' => $regNo]);

        return $stmt->fetch();
    }

    // REFACTORED: Long Method - the legacy overlap check needed three OR conditions inside create_reservation.php
    public function isAvailable($regNo, $start, $end)
    {
        $sql = "SELECT COUNT(*) FROM reservation
                WHERE RegNo = :regNo
                AND Status = 'A'
                AND StartDate < :endDate
                AND EndDate > :startDate";
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute([
            'regNo' => $regNo,
            'startDate' => $start->format('Y-m-d'),
            'endDate' => $end->format('Y-m-d'),
        ]);

        return $stmt->fetchColumn() == 0;
    }

    public function insert($row, $extras)
    {
        $this->pdo->beginTransaction();

        try {
            $sql = 'INSERT INTO reservation (RegNo, MBcode, CustNo, StartDate, EndDate, Price, Status)
                    VALUES (:regNo, :mbCode, :custNo, :startDate, :endDate, :price, :status)';
            $stmt = $this->pdo->prepare($sql);
            $stmt->execute([
                'regNo' => $row['RegNo'],
                'mbCode' => $row['MBcode'],
                'custNo' => $row['CustNo'],
                'startDate' => $row['StartDate'],
                'endDate' => $row['EndDate'],
                'price' => $row['Price'],
                'status' => $row['Status'],
            ]);
            $id = (int) $this->pdo->lastInsertId();

            $stmt = $this->pdo->prepare('INSERT INTO reservation_extra (Reservation_Id, ExtraId, Price) VALUES (:id, :extraId, :price)');
            foreach ($extras as $extra) {
                $stmt->execute(['id' => $id, 'extraId' => $extra->id, 'price' => $extra->price]);
            }

            $this->pdo->commit();
        } catch (Exception $e) {
            $this->pdo->rollBack();
            throw $e;
        }

        return $id;
    }

    // TODO: findById, cancel
}
