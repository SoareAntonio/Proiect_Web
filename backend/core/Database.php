<?php

require_once 'config.php';

class Database {

    private $username = DB_USER;
    private $password = DB_PASS; 
    private $connection = DB_HOST;
    public $conn;

    public function getConnection() {
        $this->conn = null;

        // Suprimăm avertismentele cu @ pentru a returna noi un JSON curat în caz de eroare
        $this->conn = @oci_connect($this->username, $this->password, $this->connection, 'AL32UTF8');

        if (!$this->conn) {
            $e = oci_error();
            header('Content-Type: application/json');
            echo json_encode(["status" => "error", "message" => "Eroare DB: " . $e['message']]);
            exit;
        }

        return $this->conn;
    }
}
?>