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

    private function normalizeazaString($str) {
        if (!is_string($str)) return '';
        $str = mb_strtolower(trim($str), 'UTF-8');
        $in  = ['ă', 'â', 'î', 'ș', 'ț', 'ş', 'ţ'];
        $out = ['a', 'a', 'i', 's', 't', 's', 't'];
        return str_replace($in, $out, $str);
    }

    private function proceseazaImportAnimale($animale) {
        
        $mapareClase = [
            "mamifer" => 1, "pasare" => 2, "reptila" => 3, "amfibian" => 4, "peste" => 5
        ];
        
        $mapareOrigini = [
            "europa" => 1, "africa" => 2, "asia" => 3, "america de nord" => 4, 
            "america de sud" => 5, "australia" => 6, "antarctica" => 7
        ];
        
        $mapareRegimuri = [
            "carnivor" => 1, "erbivor" => 2, "omnivor" => 3, "vegetarian" => 4
        ];
        
        $mapareStatute = [
            "daunatoare" => 1, "neamenintata" => 2, "protejata" => 3, 
            "vulnerabila" => 4, "pe cale de disparitie" => 5
        ];
        
        $mapareClime = [
            "temperata" => 1, "tropicala" => 2, "desertica" => 3, "polara" => 4
        ];
        
        $mapareInmultire = [
            "vivipar" => 1, "ovipar" => 2, "ovovivipar" => 3, "asexuata" => 4
        ];

        $contor = 0;
        foreach ($animale as $item) {
            if (!is_array($item)) continue; 

            $data = new stdClass();
            
            $data->nume_popular = $item['nume_popular'] ?? null;
            $data->nume_stiintific = $item['nume_stiintific'] ?? null;
            
            $numeClasa = $this->normalizeazaString($item['clasa'] ?? $item['clasa_animal'] ?? '');
            $data->id_clasa = $mapareClase[$numeClasa] ?? 1;

            $numeOrigine = $this->normalizeazaString($item['origine'] ?? $item['origine_animal'] ?? '');
            $data->id_origine = $mapareOrigini[$numeOrigine] ?? 1;

            $numeRegim = $this->normalizeazaString($item['regim'] ?? $item['regim_alimentar'] ?? '');
            $data->id_regim = $mapareRegimuri[$numeRegim] ?? 1;

            $numeStatut = $this->normalizeazaString($item['statut'] ?? $item['statut_conservare'] ?? '');
            $data->id_statut = $mapareStatute[$numeStatut] ?? 1;

            $numeClima = $this->normalizeazaString($item['clima'] ?? '');
            $data->id_clima = $mapareClime[$numeClima] ?? 1;

            $numeInm = $this->normalizeazaString($item['inmultire'] ?? $item['mod_inmultire'] ?? '');
            $data->id_inmultire = $mapareInmultire[$numeInm] ?? 1;
            
            $data->are_blana = isset($item['are_blana']) ? (int)$item['are_blana'] : 0;
            $data->poate_fi_dresat = isset($item['poate_fi_dresat']) ? (int)$item['poate_fi_dresat'] : 0;
            $data->este_periculos = isset($item['este_periculos']) ? (int)$item['este_periculos'] : 0;
            
            $data->descriere_ro = $item['descriere_ro'] ?? '';
            $data->descriere_en = $item['descriere_en'] ?? '';

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