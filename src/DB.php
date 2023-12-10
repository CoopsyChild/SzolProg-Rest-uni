<?php
namespace szolprog\API;

use PDO;

class DB {
    private string $host = 'localhost';
    private string $db = 'drinkstock_app';
    private string $user ='root';
    private string $pass='';

    public function connect(): PDO
    {
        return new PDO('mysql:host=' . $this->host . ';dbname=' . $this->db, $this->user, $this->pass);
    }
}