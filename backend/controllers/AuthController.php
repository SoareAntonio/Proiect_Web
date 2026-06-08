<?php
require_once __DIR__ . '/../views/JsonView.php';
require_once __DIR__ . '/../core/config.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthController {
    private $conn;

    private $cheie_secreta = JWT_SECRET;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function login() {

        $date_intrare = json_decode(file_get_contents("php://input"));
        
        if ($date_intrare === null || !isset($date_intrare->username) || !isset($date_intrare->password)) {
            JsonView::render(["status" => "error", "message" => "Completează ambele câmpuri."], 400);
        }

        $username = $date_intrare ->username;
        $password = $date_intrare ->password;

        $sqlAdmin = "SELECT * FROM Administratori WHERE username = :username";
        $stmtAdmin = oci_parse($this->conn, $sqlAdmin);
        oci_bind_by_name($stmtAdmin, ":username", $username);
        oci_execute($stmtAdmin);
        $admin = oci_fetch_assoc($stmtAdmin);
        oci_free_statement($stmtAdmin);

        if ($admin && password_verify($password, $admin['PASSWORD_HASH'])) {
            $this->genereazaSiTrimiteToken($admin['USERNAME'], 'admin');
            return; 
        }

        $sqlUser = "SELECT * FROM Utilizatori WHERE username = :usr";
        $stmtUser = oci_parse($this->conn, $sqlUser);
        oci_bind_by_name($stmtUser, ":usr", $username);
        oci_execute($stmtUser);
        $user = oci_fetch_assoc($stmtUser);
        oci_free_statement($stmtUser);

        if ($user && password_verify($password, $user['PASSWORD_HASH'])) {
            $this->genereazaSiTrimiteToken($user['USERNAME'], 'user');
            return; 
        }

        http_response_code(401);
        echo json_encode(["status" => "error", "message" => "Username sau parolă incorecte!"]);
    }

    private function genereazaSiTrimiteToken($username, $rol) 
    {
        
            $timp_curent = time();

            $payload = [
                "iss" => "zoo_api",                       
                "iat" => $timp_curent,                    
                "exp" => $timp_curent + (60 * 60 * 2),    
                "data" => [
                    "username" => $username,
                    "role" => $rol 
                ]
            ];

            $jwt = \Firebase\JWT\JWT::encode($payload, JWT_SECRET, 'HS256');

            JsonView::render([
                "status" => "success", 
                "message" => "Te-ai logat cu succes!",
                "token" => $jwt ,
                "role" => $rol
            ]);

    }

    public function verificaTokenJWT() {
        $authHeader = '';
        if (isset($_SERVER['HTTP_AUTHORIZATION'])) {
            $authHeader = trim($_SERVER['HTTP_AUTHORIZATION']);
        } elseif (isset($_SERVER['Authorization'])) {
            $authHeader = trim($_SERVER['Authorization']);
        } elseif (function_exists('apache_request_headers')) {
            $requestHeaders = apache_request_headers();
            if (isset($requestHeaders['Authorization'])) {
                $authHeader = trim($requestHeaders['Authorization']);
            }
        }

        if (preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            $token = $matches[1];

            try {
                $decoded = JWT::decode($token, new Key($this->cheie_secreta, 'HS256'));
                return true; 

            } catch (\Exception $e) {
                JsonView::render([
                    "status" => "error", 
                    "message" => "Acces respins! Bilet Token invalid sau expirat. (" . $e->getMessage() . ")"
                ], 401);
                exit; 
            }
        } else {
            JsonView::render([
                "status" => "error", 
                "message" => "Acces interzis! Nu ești autentificat lipsește token-ul."
            ], 401);
            exit; 
        }
    }
}
?>