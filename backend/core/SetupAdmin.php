<?php

die("Acces respins! Acest script a fost folosit pentru crearea inițială a adminului și este dezactivat.");

require_once __DIR__ . '/../core/config.php';
require_once 'Database.php';

$db = new Database();
$conn = $db->getConnection();

$parola_corecta = 'admin123';
$parola_hash = password_hash($parola_corecta, PASSWORD_DEFAULT);

$sql = "UPDATE Administratori SET password_hash = :hash WHERE username = 'adminZoo'";
$stmt = oci_parse($conn, $sql);
oci_bind_by_name($stmt, ":hash", $parola_hash);

if (oci_execute($stmt)) {
    echo "<p>Parola pentru contul 'admin' a fost criptată cu succes în baza de date.</p>";
} else {
    $e = oci_error($stmt);
    echo "Eroare: " . $e['message'];
}

oci_free_statement($stmt);
oci_close($conn);
?>