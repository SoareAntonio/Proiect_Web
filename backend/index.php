<?php

require_once 'core/Database.php';

$db = new Database();
$conn = $db->getConnection();

if ($conn) {
    echo "<h2 >Succes! PHP s-a conectat la baza de date Oracle!</h2>";
    
    oci_close($conn);
}
?>