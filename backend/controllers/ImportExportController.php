<?php
require_once __DIR__ . '/../models/AnimalModel.php';

class ImportExportController {
    private $model;

    public function __construct($dbConnection) {
        $this->model = new AnimalModel($dbConnection);
    }

    public function exportJSON() {
        $animale = $this->model->getFilteredAnimals([]);
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="animale_export.json"');
        echo json_encode($animale, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function exportXML() {
        $animale = $this->model->getFilteredAnimals([]);
        header('Content-Type: text/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="animale_export.xml"');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><animale></animale>');
        foreach ($animale as $animal) {
            $item = $xml->addChild('animal');
            foreach ($animal as $coloana => $valoare) {
                $item->addChild(strtolower($coloana), htmlspecialchars($valoare ?? ''));
            }
        }
        echo $xml->asXML();
        exit;
    }

    public function importJSON() {
        if (!isset($_FILES['fisier_import'])) {
            echo json_encode(["status" => "error", "message" => "Niciun fisier primit."]);
            return;
        }

        $jsonString = file_get_contents($_FILES['fisier_import']['tmp_name']);
        $animale = json_decode($jsonString, true); 

        if (!$animale || !is_array($animale)) {
            echo json_encode(["status" => "error", "message" => "Format JSON invalid sau gol."]);
            return;
        }

        $mapareClase = ["Mamifer" => 1, "Pasare" => 2, "Pasăre" => 2, "Reptila" => 3, "Reptilă" => 3];
        $mapareOrigini = ["Europa" => 1, "Africa" => 2, "Asia" => 3, "America de Nord" => 4];
        $mapareRegimuri = ["Carnivor" => 1, "Erbivor" => 2, "Omnivor" => 3];
        $mapareStatute = ["Specie protejata" => 1, "Specie protejată" => 1, "Pe cale de disparitie" => 2, "Pe cale de dispariție" => 2, "Neamenintata" => 3, "Neamenințată" => 3];
        $mapareClime = ["Temperata" => 1, "Temperată" => 1, "Tropicala" => 2, "Tropicală" => 2, "Desertica" => 3, "Deșertică" => 3];
        $mapareInmultire = ["Vivipar" => 1, "Ovipar" => 2];

        $contor = 0;
        foreach ($animale as $item) {
            if (!is_array($item)) continue; 

            $data = new stdClass();
            
            $data->nume_popular = $item['nume_popular'] ?? null;
            $data->nume_stiintific = $item['nume_stiintific'] ?? null;
            
            $numeClasa = $item['clasa'] ?? $item['clasa_animal'] ?? '';
            $data->id_clasa = isset($mapareClase[$numeClasa]) ? $mapareClase[$numeClasa] : 1;

            $numeOrigine = $item['origine'] ?? $item['origine_animal'] ?? '';
            $data->id_origine = isset($mapareOrigini[$numeOrigine]) ? $mapareOrigini[$numeOrigine] : 1;

            $numeRegim = $item['regim'] ?? $item['regim_alimentar'] ?? '';
            $data->id_regim = isset($mapareRegimuri[$numeRegim]) ? $mapareRegimuri[$numeRegim] : 1;

            $numeStatut = $item['statut'] ?? $item['statut_conservare'] ?? '';
            $data->id_statut = isset($mapareStatute[$numeStatut]) ? $mapareStatute[$numeStatut] : 1;

            $numeClima = $item['clima'] ?? '';
            $data->id_clima = isset($mapareClime[$numeClima]) ? $mapareClime[$numeClima] : 1;

            $numeInm = $item['inmultire'] ?? $item['mod_inmultire'] ?? '';
            $data->id_inmultire = isset($mapareInmultire[$numeInm]) ? $mapareInmultire[$numeInm] : 1;
            
            $data->are_blana = isset($item['are_blana']) ? (int)$item['are_blana'] : 0;
            $data->poate_fi_dresat = isset($item['poate_fi_dresat']) ? (int)$item['poate_fi_dresat'] : 0;
            $data->este_periculos = isset($item['este_periculos']) ? (int)$item['este_periculos'] : 0;
            $data->descriere_ro = $item['descriere_ro'] ?? null;

            if (!empty($item['imagine'])) {
                $data->imagine = basename($item['imagine']); 
            } elseif (!empty($item['url_imagine'])) {
                $data->imagine = basename($item['url_imagine']);
            } else {
                $data->imagine = null;
            }

            if (!empty($data->nume_popular)) {
                if ($this->model->addAnimal($data)) {
                    $contor++;
                }
            }
        }

        echo json_encode(["status" => "success", "numar_animale" => $contor]);
    }

    public function importXML() {
        if (!isset($_FILES['fisier_import'])) {
            echo json_encode(["status" => "error", "message" => "Niciun fisier primit."]);
            return;
        }

        $xml = simplexml_load_file($_FILES['fisier_import']['tmp_name']);
        if (!$xml) {
            echo json_encode(["status" => "error", "message" => "Format XML invalid."]);
            return;
        }

        $mapareClase = ["Mamifer" => 1, "Pasare" => 2, "Pasăre" => 2, "Reptila" => 3, "Reptilă" => 3];
        $mapareOrigini = ["Europa" => 1, "Africa" => 2, "Asia" => 3, "America de Nord" => 4];
        $mapareRegimuri = ["Carnivor" => 1, "Erbivor" => 2, "Omnivor" => 3];
        $mapareStatute = ["Specie protejata" => 1, "Specie protejată" => 1, "Pe cale de disparitie" => 2, "Pe cale de dispariție" => 2, "Neamenintata" => 3, "Neamenințată" => 3];
        $mapareClime = ["Temperata" => 1, "Temperată" => 1, "Tropicala" => 2, "Tropicală" => 2, "Desertica" => 3, "Deșertică" => 3];
        $mapareInmultire = ["Vivipar" => 1, "Ovipar" => 2];

        $contor = 0;
        foreach ($xml->animal as $animalNode) {
            
            $itemArray = json_decode(json_encode($animalNode), true);
            if (!is_array($itemArray)) continue;

            $data = new stdClass();
            
            $data->nume_popular = is_string($itemArray['nume_popular'] ?? null) ? $itemArray['nume_popular'] : null;
            $data->nume_stiintific = is_string($itemArray['nume_stiintific'] ?? null) ? $itemArray['nume_stiintific'] : null;
            
            $numeClasa = is_string($itemArray['clasa'] ?? $itemArray['clasa_animal'] ?? '') ? ($itemArray['clasa'] ?? $itemArray['clasa_animal']) : '';
            $data->id_clasa = isset($mapareClase[$numeClasa]) ? $mapareClase[$numeClasa] : 1;

            $numeOrigine = is_string($itemArray['origine'] ?? $itemArray['origine_animal'] ?? '') ? ($itemArray['origine'] ?? $itemArray['origine_animal']) : '';
            $data->id_origine = isset($mapareOrigini[$numeOrigine]) ? $mapareOrigini[$numeOrigine] : 1;

            $numeRegim = is_string($itemArray['regim'] ?? $itemArray['regim_alimentar'] ?? '') ? ($itemArray['regim'] ?? $itemArray['regim_alimentar']) : '';
            $data->id_regim = isset($mapareRegimuri[$numeRegim]) ? $mapareRegimuri[$numeRegim] : 1;

            $numeStatut = is_string($itemArray['statut'] ?? $itemArray['statut_conservare'] ?? '') ? ($itemArray['statut'] ?? $itemArray['statut_conservare']) : '';
            $data->id_statut = isset($mapareStatute[$numeStatut]) ? $mapareStatute[$numeStatut] : 1;

            $numeClima = is_string($itemArray['clima'] ?? '') ? $itemArray['clima'] : '';
            $data->id_clima = isset($mapareClime[$numeClima]) ? $mapareClime[$numeClima] : 1;

            $numeInm = is_string($itemArray['inmultire'] ?? $itemArray['mod_inmultire'] ?? '') ? ($itemArray['inmultire'] ?? $itemArray['mod_inmultire']) : '';
            $data->id_inmultire = isset($mapareInmultire[$numeInm]) ? $mapareInmultire[$numeInm] : 1;
            
            $data->are_blana = isset($itemArray['are_blana']) && is_numeric($itemArray['are_blana']) ? (int)$itemArray['are_blana'] : 0;
            $data->poate_fi_dresat = isset($itemArray['poate_fi_dresat']) && is_numeric($itemArray['poate_fi_dresat']) ? (int)$itemArray['poate_fi_dresat'] : 0;
            $data->este_periculos = isset($itemArray['este_periculos']) && is_numeric($itemArray['este_periculos']) ? (int)$itemArray['este_periculos'] : 0;
            $data->descriere_ro = is_string($itemArray['descriere_ro'] ?? null) ? $itemArray['descriere_ro'] : null;

            if (!empty($itemArray['imagine']) && is_string($itemArray['imagine'])) {
                $data->imagine = basename($itemArray['imagine']); 
            } elseif (!empty($itemArray['url_imagine']) && is_string($itemArray['url_imagine'])) {
                $data->imagine = basename($itemArray['url_imagine']);
            } else {
                $data->imagine = null;
            }

            if (!empty($data->nume_popular)) {
                if ($this->model->addAnimal($data)) {
                    $contor++;
                }
            }
        }

        
        echo json_encode(["status" => "success", "numar_animale" => $contor]);
    }
}
?>