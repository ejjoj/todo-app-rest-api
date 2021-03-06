<?php

class Nav {
    private $conn;
    private $table = 'nav';

    public $id;
    public $name;
    public $path;
    public $exact;


    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTableName() {
        return $this->table;
    }

    public function msg($success, $status, $body, $extra = []) {
        return array_merge([
            'success' => $success,
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