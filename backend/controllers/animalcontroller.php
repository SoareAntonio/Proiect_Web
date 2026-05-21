<?php

require_once __DIR__ . '/../models/AnimalModel.php';
require_once __DIR__ . '/../views/JsonView.php';

class AnimalController
{
    private $model;

    public function __construct($dbConnection)
    {
        $this->model = new AnimalModel($dbConnection);
    }

    public function getAnimals()
    {
        $filters = [
            'id_clasa' => isset($_GET['id_clasa']) ? (int) $_GET['id_clasa'] : null,
            'id_origine' => isset($_GET['id_origine']) ? (int) $_GET['id_origine'] : null,
            'id_regim' => isset($_GET['id_regim']) ? (int) $_GET['id_regim'] : null,
            'id_clima' => isset($_GET['id_clima']) ? (int) $_GET['id_clima'] : null,
            'are_blana' => isset($_GET['are_blana']) ? (int) $_GET['are_blana'] : null,
            'poate_fi_dresat' => isset($_GET['poate_fi_dresat']) ? (int) $_GET['poate_fi_dresat'] : null,
            'este_periculos' => isset($_GET['este_periculos']) ? (int) $_GET['este_periculos'] : null
        ];

        $activeFilters = array_filter($filters, function ($value) {
            return $value !== null;
        });

        $animals = $this->model->getFilteredAnimals($activeFilters);

        JsonView::render([
            "status" => "success",
            "rezultate" => count($animals),
            "data" => $animals,
            "message" => count($animals) === 0
                ? "Nu s-au gasit animale care sa corespunda acestor criterii."
                : null
        ]);
    }
}