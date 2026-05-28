<?php
require_once __DIR__ . '/../views/JsonView.php';

class AuthController {
    private $conn;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function login() {
        $data = json_decode(file_get_contents("php://input"));
        
        if (!isset($data->username) || !isset($data->password)) {
            JsonView::render(["status" => "error", "message" => "Completează ambele câmpuri."], 400);
        }

        $sql = "SELECT * FROM Administratori WHERE username = :username";
        $stmt = oci_parse($this->conn, $sql);
        oci_bind_by_name($stmt, ":username", $data->username);
        oci_execute($stmt);
        $user = oci_fetch_assoc($stmt);

        $hashed_input_password = hash('sha256', $data->password);

        if ($user && ($hashed_input_password === $user['PASSWORD_HASH'] || $data->password === $user['PASSWORD_HASH'])) {
            $_SESSION['admin_logat'] = true;
            JsonView::render(["status" => "success", "message" => "Te-ai logat cu succes!"]);
        } else {
            JsonView::render(["status" => "error", "message" => "Username sau parolă incorecte!"], 401);
        }
    }
}
?>