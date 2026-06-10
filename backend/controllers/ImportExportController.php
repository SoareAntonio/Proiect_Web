<?php
require_once __DIR__ . '/../models/AnimalModel.php';

class ImportExportController {
    private $model;

    public function __construct($dbConnection) {
        $this->model = new AnimalModel($dbConnection);
    }

    public function exportJSON() {
        $animale = $this->model->getFilteredAnimals([]);
        
        if (ob_get_length()) ob_clean(); 
        
        header('Content-Type: application/json; charset=utf-8');
        header('Content-Disposition: attachment; filename="animale_export.json"');
        echo json_encode($animale, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        exit;
    }

    public function exportXML() {
        $animale = $this->model->getFilteredAnimals([]);
        
        if (ob_get_length()) ob_clean(); 
        
        header('Content-Type: text/xml; charset=utf-8');
        header('Content-Disposition: attachment; filename="animale_export.xml"');
        
        $xml = new SimpleXMLElement('<?xml version="1.0" encoding="UTF-8"?><animale></animale>');
        foreach ($animale as $animal) {
            $item = $xml->addChild('animal');
            foreach ($animal as $coloana => $valoare) {
                $item->addChild(strtolower($coloana), htmlspecialchars($valoare ?? '', ENT_QUOTES | ENT_XML1, 'UTF-8'));
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

        try {
            $this->model->curataBazaDeDate();
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            return;
        }
        
        $jsonString = file_get_contents($_FILES['fisier_import']['tmp_name']);
        $animale = json_decode($jsonString, true); 

        if (!$animale || !is_array($animale)) {
            echo json_encode(["status" => "error", "message" => "Format JSON invalid sau gol."]);
            return;
        }

        $this->proceseazaImportAnimale($animale);
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

        try {
            $this->model->curataBazaDeDate(); 
        } catch (Exception $e) {
            echo json_encode(["status" => "error", "message" => $e->getMessage()]);
            return;
        }

        $animale = [];
        foreach ($xml->animal as $animalNode) {
            $animale[] = json_decode(json_encode($animalNode), true);
        }

        $this->proceseazaImportAnimale($animale);
    }

    private function proceseazaImportAnimale($animale) {
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
            
            $data->nume_popular = is_string($item['nume_popular'] ?? null) ? $item['nume_popular'] : null;
            $data->nume_stiintific = is_string($item['nume_stiintific'] ?? null) ? $item['nume_stiintific'] : null;
            
            $numeClasa = is_string($item['clasa'] ?? $item['clasa_animal'] ?? '') ? ($item['clasa'] ?? $item['clasa_animal']) : '';
            $data->id_clasa = isset($mapareClase[$numeClasa]) ? $mapareClase[$numeClasa] : 1;

            $numeOrigine = is_string($item['origine'] ?? $item['origine_animal'] ?? '') ? ($item['origine'] ?? $item['origine_animal']) : '';
            $data->id_origine = isset($mapareOrigini[$numeOrigine]) ? $mapareOrigini[$numeOrigine] : 1;

            $numeRegim = is_string($item['regim'] ?? $item['regim_alimentar'] ?? '') ? ($item['regim'] ?? $item['regim_alimentar']) : '';
            $data->id_regim = isset($mapareRegimuri[$numeRegim]) ? $mapareRegimuri[$numeRegim] : 1;

            $numeStatut = is_string($item['statut'] ?? $item['statut_conservare'] ?? '') ? ($item['statut'] ?? $item['statut_conservare']) : '';
            $data->id_statut = isset($mapareStatute[$numeStatut]) ? $mapareStatute[$numeStatut] : 1;

            $numeClima = is_string($item['clima'] ?? '') ? $item['clima'] : '';
            $data->id_clima = isset($mapareClime[$numeClima]) ? $mapareClime[$numeClima] : 1;

            $numeInm = is_string($item['inmultire'] ?? $item['mod_inmultire'] ?? '') ? ($item['inmultire'] ?? $item['mod_inmultire']) : '';
            $data->id_inmultire = isset($mapareInmultire[$numeInm]) ? $mapareInmultire[$numeInm] : 1;
            
            $data->are_blana = isset($item['are_blana']) && is_numeric($item['are_blana']) ? (int)$item['are_blana'] : 0;
            $data->poate_fi_dresat = isset($item['poate_fi_dresat']) && is_numeric($item['poate_fi_dresat']) ? (int)$item['poate_fi_dresat'] : 0;
            $data->este_periculos = isset($item['este_periculos']) && is_numeric($item['este_periculos']) ? (int)$item['este_periculos'] : 0;
            
            $data->descriere_ro = (isset($item['descriere_ro']) && is_string($item['descriere_ro'])) ? $item['descriere_ro'] : '';
            $data->descriere_en = (isset($item['descriere_en']) && is_string($item['descriere_en'])) ? $item['descriere_en'] : '';

            if (!empty($item['imagine']) && is_string($item['imagine'])) {
                $data->imagine = basename($item['imagine']); 
            } elseif (!empty($item['url_imagine']) && is_string($item['url_imagine'])) {
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
}
?>