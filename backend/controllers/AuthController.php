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
            JsonView::render([
                "status" => "error",
                "message" => "Completează username-ul și parola."
            ], 400);
        }

        $username = trim($data->username);
        $password = $data->password;
        $remember = isset($data->remember) && $data->remember === true;

        if (session_status() === PHP_SESSION_ACTIVE) {
            session_regenerate_id(true);
        }

        // 1. Încercăm login ca administrator
        $adminSql = "SELECT username, password_hash FROM Administratori WHERE username = :username";
        $adminStmt = oci_parse($this->conn, $adminSql);
        oci_bind_by_name($adminStmt, ":username", $username);
        oci_execute($adminStmt);

        $admin = oci_fetch_assoc($adminStmt);

        if ($admin) {
            $hashedInputPassword = hash('sha256', $password);

            if ($hashedInputPassword === $admin['PASSWORD_HASH'] || $password === $admin['PASSWORD_HASH']) {
                $_SESSION['admin_logat'] = true;
                $_SESSION['admin_username'] = $admin['USERNAME'];
                $_SESSION['user_role'] = 'admin';

                JsonView::render([
                    "status" => "success",
                    "message" => "Autentificare administrator reușită.",
                    "role" => "admin",
                    "remember" => $remember
                ]);
            }
        }

        // 2. Dacă nu e admin, încercăm login ca utilizator normal
        $userSql = "SELECT id_utilizator, username, email, password_hash, rol
                    FROM Utilizatori
                    WHERE username = :username OR email = :email";

        $userStmt = oci_parse($this->conn, $userSql);
        oci_bind_by_name($userStmt, ":username", $username);
        oci_bind_by_name($userStmt, ":email", $username);
        oci_execute($userStmt);

        $user = oci_fetch_assoc($userStmt);

        if ($user && password_verify($password, $user['PASSWORD_HASH'])) {
            $_SESSION['user_logat'] = true;
            $_SESSION['user_id'] = $user['ID_UTILIZATOR'];
            $_SESSION['username'] = $user['USERNAME'];
            $_SESSION['user_role'] = $user['ROL'];

            JsonView::render([
                "status" => "success",
                "message" => "Autentificare utilizator reușită.",
                "role" => $user['ROL'],
                "username" => $user['USERNAME'],
                "remember" => $remember
            ]);
        }

        JsonView::render([
            "status" => "error",
            "message" => "Username/email sau parolă incorecte."
        ], 401);
    }

    public function register() {
        $data = json_decode(file_get_contents("php://input"));

        if (!isset($data->username) || !isset($data->email) || !isset($data->password) || !isset($data->confirmPassword)) {
            JsonView::render(["status" => "error", "message" => "Completează toate câmpurile."], 400);
        }

        $username = trim($data->username);
        $email = trim($data->email);
        $password = $data->password;
        $confirmPassword = $data->confirmPassword;

        if (strlen($username) < 3) {
            JsonView::render(["status" => "error", "message" => "Username-ul trebuie să aibă minimum 3 caractere."], 400);
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            JsonView::render(["status" => "error", "message" => "Adresa de email nu este validă."], 400);
        }

        if (strlen($password) < 6) {
            JsonView::render(["status" => "error", "message" => "Parola trebuie să aibă minimum 6 caractere."], 400);
        }

        if ($password !== $confirmPassword) {
            JsonView::render(["status" => "error", "message" => "Parolele nu coincid."], 400);
        }

        $checkSql = "SELECT COUNT(*) AS TOTAL FROM Utilizatori WHERE username = :username OR email = :email";
        $checkStmt = oci_parse($this->conn, $checkSql);
        oci_bind_by_name($checkStmt, ":username", $username);
        oci_bind_by_name($checkStmt, ":email", $email);
        oci_execute($checkStmt);
        $existing = oci_fetch_assoc($checkStmt);

        if ((int)$existing['TOTAL'] > 0) {
            JsonView::render(["status" => "error", "message" => "Username-ul sau emailul există deja."], 409);
        }

        $passwordHash = password_hash($password, PASSWORD_DEFAULT);

        $insertSql = "
            INSERT INTO Utilizatori (id_utilizator, username, email, password_hash, rol, data_creare)
            VALUES (secv_utilizatori.NEXTVAL, :username, :email, :password_hash, 'vizitator', SYSDATE)
        ";

        $insertStmt = oci_parse($this->conn, $insertSql);
        oci_bind_by_name($insertStmt, ":username", $username);
        oci_bind_by_name($insertStmt, ":email", $email);
        oci_bind_by_name($insertStmt, ":password_hash", $passwordHash);

        $success = oci_execute($insertStmt, OCI_COMMIT_ON_SUCCESS);

        if (!$success) {
            $e = oci_error($insertStmt);
            JsonView::render(["status" => "error", "message" => "Eroare la crearea contului: " . $e['message']], 500);
        }

        JsonView::render([
            "status" => "success",
            "message" => "Contul a fost creat cu succes. Te poți autentifica."
        ], 201);
    }
}
?>
