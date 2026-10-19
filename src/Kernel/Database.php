<?php

namespace App\Kernel;

use PDO;

// REFACTORED: Hard-coded Credentials in config/db_connection.php
class Database
{
    private $host;
    private $name;
    private $user;
    private $password;

    public function __construct($host, $name, $user, $password)
    {
        $this->host = $host;
        $this->name = $name;
        $this->user = $user;
        $this->password = $password;
    }

    public function connect()
    {
        $dsn = "mysql:host=$this->host;dbname=$this->name;charset=utf8mb4";

        $pdo = new PDO($dsn, $this->user, $this->password);
        $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);

        return $pdo;
    }
}
