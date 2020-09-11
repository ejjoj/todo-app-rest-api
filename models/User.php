<?php

class User {
    // DB Stuff
    private $conn;
    private $table = 'users';

    //User properties

    public $user_id;
    public $user_name;
    public $user_pass;
    public $user_email;
    public $user_list_id;
    public $joined_at;
    public $user_perm;

    //Constructor with DB

    public function __construct($db) {
        $this->conn = $db;
    }

    public function getTableName() {
        return $this->table;
    }

    // Get users

    public function read() {
        $query = 'SELECT * FROM ' . $this->table;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function read_one() {
        $query = 'SELECT * FROM ' . $this->table . ' WHERE `user_id`= ' . $this->user_id;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }

    public function create() {
        $this->user_name = htmlspecialchars(strip_tags($this->user_name));
        $this->user_pass = htmlspecialchars(strip_tags($this->user_pass));
        $this->joined_at = htmlspecialchars(strip_tags($this->joined_at));

        $query = 'INSERT INTO ' . $this->table . '(`user_name`, `user_pass`, `joined_at`) VALUES ("'. $this->user_name .'", "' . $this->user_pass . '", "' . $this->joined_at . '")';

        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function validateDate($date, $format = 'Y-m-d H:i:s') {
        $d = DateTime::createFromFormat($format, $date);

        return $d && $d->format($format) === $date;
    }

    public function update_list_id() {
        $query = 'UPDATE ' . $this->table . ' SET `user_list_id`= ' . $this->user_list_id . ' WHERE `user_id`= ' . $this->user_id;

        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function update_pass() {
        $query = 'UPDATE ' . $this->table . ' SET `user_pass`= "' . $this->user_pass . '" WHERE `user_id`= ' . $this->user_id;
        $stmt = $this->conn->prepare($query);

        if ($stmt->execute()) {
            return true;
        } else {
            return false;
        }
    }

    public function get_user_permission() {
        $query = 'SELECT `user_name`, `user_perm` FROM ' . $this->table . ' WHERE `user_id`= ' . $this->user_id;

        $stmt = $this->conn->prepare($query);
        $stmt->execute();

        return $stmt;
    }
}
