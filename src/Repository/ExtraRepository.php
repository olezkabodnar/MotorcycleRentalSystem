<?php

namespace App\Repository;

use App\Model\Extra;

// Repository: all SQL for the extra table (FR6)
class ExtraRepository
{
    private $pdo;

    public function __construct($pdo)
    {
        $this->pdo = $pdo;
    }

    public function findAll()
    {
        $stmt = $this->pdo->query('SELECT * FROM extra ORDER BY ExtraId');

        $extras = [];
        foreach ($stmt->fetchAll() as $row) {
            $extras[] = new Extra($row['Name'], $row['Price'], $row['ChargeType'], $row['ExtraId']);
        }
        return $extras;
    }

    public function findByIds($ids)
    {
        $chosen = [];
        foreach ($this->findAll() as $extra) {
            if (in_array($extra->id, $ids)) {
                $chosen[] = $extra;
            }
        }
        return $chosen;
    }
}
