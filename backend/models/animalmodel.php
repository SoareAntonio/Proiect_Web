<?php

class AnimalModel
{
    private $conn;

    public function __construct($connection)
    {
        $this->conn = $connection;
    }

    public function getAllAnimals()
    {
        $sql = "
            SELECT
                a.id_animal,
                a.nume_popular,
                a.nume_stiintific,
                c.denumire AS clasa,
                o.denumire AS origine,
                r.denumire AS regim_alimentar,
                s.denumire AS statut,
                cl.denumire AS clima,
                mi.denumire AS mod_inmultire,
                a.are_blana,
                a.poate_fi_dresat,
                a.este_periculos,
                a.descriere_ro,
                a.descriere_en,
                img.url_imagine
            FROM Animale a
            JOIN Clase_Animale c ON a.id_clasa = c.id_clasa
            JOIN Origini o ON a.id_origine = o.id_origine
            JOIN Regimuri_Alimentare r ON a.id_regim = r.id_regim
            JOIN Statute_Conservare s ON a.id_statut = s.id_statut
            JOIN Clime cl ON a.id_clima = cl.id_clima
            JOIN Moduri_Inmultire mi ON a.id_inmultire = mi.id_inmultire
            LEFT JOIN Imagini_Animale img 
                ON a.id_animal = img.id_animal 
                AND img.este_imagine_principala = 1
            ORDER BY a.id_animal
        ";

        $statement = oci_parse($this->conn, $sql);
        oci_execute($statement);

        $animals = [];

        while ($row = oci_fetch_assoc($statement)) {
            $animals[] = [
                "id" => $row["ID_ANIMAL"],
                "nume_popular" => $row["NUME_POPULAR"],
                "nume_stiintific" => $row["NUME_STIINTIFIC"],
                "clasa" => $row["CLASA"],
                "origine" => $row["ORIGINE"],
                "regim_alimentar" => $row["REGIM_ALIMENTAR"],
                "statut" => $row["STATUT"],
                "clima" => $row["CLIMA"],
                "mod_inmultire" => $row["MOD_INMULTIRE"],
                "are_blana" => (int)$row["ARE_BLANA"],
                "poate_fi_dresat" => (int)$row["POATE_FI_DRESAT"],
                "este_periculos" => (int)$row["ESTE_PERICULOS"],
                "descriere_ro" => $row["DESCRIERE_RO"],
                "descriere_en" => $row["DESCRIERE_EN"],
                "url_imagine" => $row["URL_IMAGINE"]
            ];
        }

        oci_free_statement($statement);

        return $animals;
    }
}