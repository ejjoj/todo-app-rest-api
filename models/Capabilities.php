<?php

class Capabilities {
    private $conn;
    private $table;

    public $id;
    public $name;
    public $desc;


    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTableName() {
        return $this->table;
    }

    public function msg($succes, $status, $body, $extra = []) {
        return array_merge([
            'succes' => $succes,
            'status' => $status,
            'body' => $body
        ], $extra);
    }

    public function read() {
        $query = 'SELECT * FROM ' . $this->getTableName();

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}