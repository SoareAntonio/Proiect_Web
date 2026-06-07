<?php
require_once __DIR__ . '/../views/JsonView.php';

use \Firebase\JWT\JWT;
use \Firebase\JWT\Key;

class AuthController {
    private $conn;

    private $cheie_secreta = JWT_SECRET;

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

        if ($user && password_verify($data->password, $user['PASSWORD_HASH'])) {
            
            $timp_curent = time();
            $payload = [
                "iss" => "zoo_api",                       
                "iat" => $timp_curent,                    
                "exp" => $timp_curent + (60 * 60 * 2),    
                "admin_id" => $user['ID_ADMIN'],
                "username" => $user['USERNAME']
            ];

            $jwt = JWT::encode($payload, $this->cheie_secreta, 'HS256');

            JsonView::render([
                "status" => "success", 
                "message" => "Te-ai logat cu succes!",
                "token" => $jwt 
            ]);
        } else {
            JsonView::render(["status" => "error", "message" => "Username sau parolă incorecte"], 401);
        }
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