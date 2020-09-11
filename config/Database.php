<?php

class Database {
    //DB Paramas
    private $db_host = 'localhost';
    private $db_name = 'todoapp';
    private $username = 'root';
    private $password = '';
    private $conn;

    //DB Connect

    public function connect() {
        $this->conn = null;

        try {
            $this->conn = new PDO(
                'mysql:host=' . $this->db_host . ';dbname=' . $this->db_name,
                $this->username,
                $this->password
            );

            $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch(PDOException $e) {
            echo 'Connection error: ' + $e->getMessage();
        }

        return $this->conn;
    }
}
