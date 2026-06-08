    <?php
session_start();

header("Access-Control-Allow-Origin: *");
header("Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type, Authorization");

if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    exit(0);
}

require_once __DIR__ . '/core/Database.php';
require_once __DIR__ . '/controllers/AnimalController.php';
require_once __DIR__ . '/controllers/ImportExportController.php';
require_once __DIR__ . '/controllers/AuthController.php';
require_once __DIR__ . '/views/JsonView.php';

$db = new Database();
$conn = $db->getConnection();

$action = isset($_GET['action']) ? $_GET['action'] : '';

try {
    $animalController = new AnimalController($conn);
    $importExportController = new ImportExportController($conn);
    $authController = new AuthController($conn);

    switch ($action) {
        case 'login':
            $authController->login();
            break;
        case 'register':
            $authController->register();
            break;
        case 'verifica_sesiune':
            if (isset($_SESSION['admin_logat']) && $_SESSION['admin_logat'] === true) {
                JsonView::render(["status" => "success", "logat" => true]);
            } else {
                JsonView::render(["status" => "error", "logat" => false]);
            }
            break;
        case 'logout':
            session_destroy();
            JsonView::render(["status" => "success", "message" => "Te-ai deconectat cu succes!"]);
            break;
        case 'get_animals':
            $animalController->getAnimals();
            break;
        case 'add_animal':
            $animalController->addAnimal();
            break;
        case 'delete_animal':
            $animalController->deleteAnimal();
            break;
        case 'get_categories':
            $animalController->preiaCategoriiAnimale();
            break;
        case 'export_json':
            $importExportController->exportJSON();
            break;
        case 'export_xml':
            $importExportController->exportXML();
            break;
        case 'import_json':
            $importExportController->importJSON();
            break;
        case 'import_xml':
            $importExportController->importXML();
            break;
        default:
            JsonView::render([
                "status" => "error",
                "message" => "Actiune invalida sau lipsa."
            ], 404);
            break;
    }
} finally {
    if ($conn) {
        oci_close($conn);
    }
}
?>