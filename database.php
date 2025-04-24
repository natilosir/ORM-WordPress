<?php

class database {
    private $host; // Database host

    private $db_name; // Database name

    private $username; // Database username

    private $password; // Database password

    private $connection;

    public function __construct() {
        $this->host     = DB_HOST;
        $this->db_name  = DB_NAME;
        $this->username = DB_USER;
        $this->password = DB_PASSWORD;
    }

    public function getConnection() {
        $this->connection = null;

        try {
            $this->connection = new PDO("mysql:host={$this->host};dbname={$this->db_name};charset=utf8mb4", $this->username, $this->password);
            $this->connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch ( PDOException $exception ) {
            echo 'Connection error: ' . $exception->getMessage();
        }

        return $this->connection;
    }
}
