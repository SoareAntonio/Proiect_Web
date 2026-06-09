<?php
require_once __DIR__ . '/../views/JsonView.php';
require_once __DIR__ . '/../core/config.php';

use Firebase\JWT\JWT;
use Firebase\JWT\Key;

class AuthController {
    private $conn;
    private $cheie_secreta = JWT_SECRET;

    public function __construct($dbConnection) {
        $this->conn = $dbConnection;
    }

    public function login() {
        $date_intrare = json_decode(file_get_contents("php://input"));

        if ($date_intrare === null || !isset($date_intrare->username) || !isset($date_intrare->password)) {
            JsonView::render([
                "status" => "error",
                "message" => "Completează username-ul și parola."
            ], 400);
        }

        $username = trim($date_intrare->username);
        $password = $date_intrare->password;
        $remember = isset($date_intrare->remember) && $date_intrare->remember === true;

        // Login administrator
        $sqlAdmin = "SELECT username, password_hash FROM Administratori WHERE username = :username";
        $stmtAdmin = oci_parse($this->conn, $sqlAdmin);
        oci_bind_by_name($stmtAdmin, ":username", $username);
        oci_execute($stmtAdmin);

        $admin = oci_fetch_assoc($stmtAdmin);
        oci_free_statement($stmtAdmin);

        if ($admin && password_verify($password, $admin['PASSWORD_HASH'])) {
            $this->genereazaSiTrimiteToken($admin['USERNAME'], 'admin', $remember);
            return;
        }

        // Login utilizator normal
        $sqlUser = "SELECT id_utilizator, username, email, password_hash
                    FROM Utilizatori
                    WHERE username = :username OR email = :email";

        $stmtUser = oci_parse($this->conn, $sqlUser);
        oci_bind_by_name($stmtUser, ":username", $username);
        oci_bind_by_name($stmtUser, ":email", $username);
        oci_execute($stmtUser);

        $user = oci_fetch_assoc($stmtUser);
        oci_free_statement($stmtUser);

        if ($user && password_verify($password, $user['PASSWORD_HASH'])) {
            $this->genereazaSiTrimiteToken($user['USERNAME'], 'user', $remember);
            return;
        }

        JsonView::render([
            "status" => "error",
            "message" => "Username/email sau parolă incorecte."
        ], 401);
    }

    private function genereazaSiTrimiteToken($username, $rol, $remember = false) {
        $timp_curent = time();
        $timp_valabilitate = $remember ? (60 * 60 * 24 * 30) : (60 * 60 * 2);

        $payload = [
            "iss" => "zoo_api",
            "iat" => $timp_curent,
            "exp" => $timp_curent + $timp_valabilitate,
            "data" => [
                "username" => $username,
                "role" => $rol
            ]
        ];

        $jwt = JWT::encode($payload, $this->cheie_secreta, 'HS256');

        JsonView::render([
            "status" => "success",
            "message" => "Te-ai logat cu succes!",
            "token" => $jwt,
            "role" => $rol,
            "remember" => $remember
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

        if (!preg_match('/Bearer\s(\S+)/', $authHeader, $matches)) {
            JsonView::render([
                "status" => "error",
                "message" => "Acces interzis! Lipsește token-ul."
            ], 401);
        }

        $token = $matches[1];

        try {
            JWT::decode($token, new Key($this->cheie_secreta, 'HS256'));
            return true;
        } catch (Exception $e) {
            JsonView::render([
                "status" => "error",
                "message" => "Token invalid sau expirat: " . $e->getMessage()
            ], 401);
        }
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"));

        if (
            $data === null ||
            !isset($data->username) ||
            !isset($data->email) ||
            !isset($data->password) ||
            !isset($data->confirmPassword)
        ) {
            JsonView::render([
                "status" => "error",
                "message" => "Completează toate câmpurile."
            ], 400);
        }

        $username = trim($data->username);
        $email = trim($data->email);
        $password = $data->password;
        $confirmPassword = $data->confirmPassword;

        if (strlen($username) < 3) {
            JsonView::render([
                "status" => "error",
                "message" => "Username-ul trebuie să aibă minimum 3 caractere."
            ], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            JsonView::render([
                "status" => "error",
                "message" => "Adresa de email nu este validă."
            ], 400);
        }

        if (strlen($password) < 6) {
            JsonView::render([
                "status" => "error",
                "message" => "Parola trebuie să aibă minimum 6 caractere."
            ], 400);
        }

        if ($password !== $confirmPassword) {
            JsonView::render([
                "status" => "error",
                "message" => "Parolele nu coincid."
            ], 400);
        }

        $checkSql = "SELECT COUNT(*) AS TOTAL
                     FROM Utilizatori
                     WHERE username = :username OR email = :email";

        $checkStmt = oci_parse($this->conn, $checkSql);
        oci_bind_by_name($checkStmt, ":username", $username);
        oci_bind_by_name($checkStmt, ":email", $email);
        oci_execute($checkStmt);

        $existing = oci_fetch_assoc($checkStmt);
        oci_free_statement($checkStmt);

        if ((int)$existing['TOTAL'] > 0) {
            JsonView::render([
                "status" => "error",
                "message" => "Username-ul sau emailul există deja."
            ], 409);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $insertSql = "INSERT INTO Utilizatori (username, password_hash, email)
                      VALUES (:username, :password_hash, :email)";

        $insertStmt = oci_parse($this->conn, $insertSql);
        oci_bind_by_name($insertStmt, ":username", $username);
        oci_bind_by_name($insertStmt, ":password_hash", $passwordHash);
        oci_bind_by_name($insertStmt, ":email", $email);

        $success = oci_execute($insertStmt, OCI_COMMIT_ON_SUCCESS);

        if (!$success) {
            $e = oci_error($insertStmt);

            JsonView::render([
                "status" => "error",
                "message" => "Eroare la crearea contului: " . $e['message']
            ], 500);
        }

        oci_free_statement($insertStmt);

        JsonView::render([
            "status" => "success",
            "message" => "Contul a fost creat cu succes. Te poți autentifica."
        ], 201);
    }
}
?>