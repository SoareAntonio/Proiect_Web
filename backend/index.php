<?php

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit;
}

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/controllers/AnimalController.php';
require_once __DIR__ . '/views/JsonView.php';

$db = new Database();
$conn = $db->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    switch ($action) {
        case 'get_animals':
            $controller = new AnimalController($conn);
            $controller->getAnimals();
            break;

        default:
            JsonView::render([
                "status" => "error",
                "message" => "Actiune invalida sau lipsa. Foloseste ?action=get_animals"
            ], 400);
            break;
    }
} finally {
    if ($conn) {
        oci_close($conn);
    }
}