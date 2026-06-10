<?php
require_once __DIR__ . '/../models/AnimalModel.php';
require_once __DIR__ . '/../views/JsonView.php';

class AnimalController {
    private $model;

    public function __construct($dbConnection) {
        $this->model = new AnimalModel($dbConnection);
    }

    public function getAnimals() {
        $filters = [
            'id_clasa' => isset($_GET['id_clasa']) ? (int) $_GET['id_clasa'] : null,
            'id_origine' => isset($_GET['id_origine']) ? (int) $_GET['id_origine'] : null,
            'id_regim' => isset($_GET['id_regim']) ? (int) $_GET['id_regim'] : null,
            'id_clima' => isset($_GET['id_clima']) ? (int) $_GET['id_clima'] : null,
            'are_blana' => isset($_GET['are_blana']) ? (int) $_GET['are_blana'] : null,
            'poate_fi_dresat' => isset($_GET['poate_fi_dresat']) ? (int) $_GET['poate_fi_dresat'] : null,
            'este_periculos' => isset($_GET['este_periculos']) ? (int) $_GET['este_periculos'] : null
        ];

        $activeFilters = array_filter($filters, function ($value) { return $value !== null; });
        $animals = $this->model->getFilteredAnimals($activeFilters);

        JsonView::render([
            "status" => "success",
            "rezultate" => count($animals),
            "data" => $animals,
            "message" => count($animals) === 0 ? "Nu s-au găsit animale conform criteriilor." : null
        ]);
    }

    public function deleteAnimal() {
        $id = isset($_GET['id']) ? (int)$_GET['id'] : null;

        if (!$id) {
            JsonView::render(["status" => "error", "message" => "ID-ul animalului lipsește."], 400);
        }

        $succes = $this->model->deleteAnimal($id);
        if ($succes) {
            JsonView::render(["status" => "success", "message" => "Animalul a fost șters!"]);
        } else {
            JsonView::render(["status" => "error", "message" => "Eroare la ștergerea animalului."], 500);
        }
    }

    public function addAnimal() {
        $data = json_decode(file_get_contents("php://input"));
        $res = $this->model->addAnimal($data);
        
        if ($res) {
            JsonView::render(["status" => "success", "message" => "Salvat!"]);
        } else {
            JsonView::render(["status" => "error", "message" => "Eroare la inserare."], 500);
        }
    }

    public function preiaCategoriiAnimale() {
        $data = [
            "clase" => $this->model->preiaDictionar("Clase_Animale", "id_clasa", "denumire"),
            "origini" => $this->model->preiaDictionar("Origini", "id_origine", "denumire"),
            "regimuri" => $this->model->preiaDictionar("Regimuri_Alimentare", "id_regim", "denumire"),
            "statute" => $this->model->preiaDictionar("Statute_Conservare", "id_statut", "denumire"),
            "clime" => $this->model->preiaDictionar("Clime", "id_clima", "denumire"),
            "inmultiri" => $this->model->preiaDictionar("Moduri_Inmultire", "id_inmultire", "denumire")
        ];
        JsonView::render(["status" => "success", "data" => $data]);
    }

    public function deleteAllAnimals() {
        try {
            $this->model->curataBazaDeDate(); 
            
            JsonView::render([
                "status" => "success", 
                "message" => "Toate animalele au fost șterse cu succes."
            ]);
        } catch (Exception $e) {
            JsonView::render([
                "status" => "error", 
                "message" => "Eroare la ștergere: " . $e->getMessage()
            ], 500);
        }
    }

}
?>