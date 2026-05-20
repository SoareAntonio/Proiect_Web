<?php

require_once __DIR__ . '/../models/AnimalModel.php';
require_once __DIR__ . '/../views/JsonView.php';

class AnimalController
{
    private $animalModel;

    public function __construct($connection)
    {
        $this->animalModel = new AnimalModel($connection);
    }

    public function getAnimals()
    {
        $animals = $this->animalModel->getAllAnimals();

        JsonView::render([
            "status" => "success",
            "data" => $animals
        ]);
    }
}