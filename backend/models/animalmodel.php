<?php

class AnimalModel
{
    private $conn;

    public function __construct($dbConnection)
    {
        $this->conn = $dbConnection;
    }

    public function getFilteredAnimals($filters)
    {
        $sql = "
            SELECT
                a.id_animal,
                a.nume_popular,
                a.nume_stiintific,

                c.denumire AS clasa_animal,
                o.denumire AS origine_animal,
                r.denumire AS regim_alimentar,
                s.denumire AS statut_conservare,
                cl.denumire AS clima,
                mi.denumire AS mod_inmultire,

                a.are_blana,
                a.poate_fi_dresat,
                a.este_periculos,
                a.descriere_ro,
                a.descriere_en,

                img.url_imagine,

                (
                    SELECT LISTAGG(a2.nume_popular, ', ') WITHIN GROUP (ORDER BY a2.nume_popular)
                    FROM Dusmani_Naturali dn
                    JOIN Animale a2 ON dn.id_pradator = a2.id_animal
                    WHERE dn.id_prada = a.id_animal
                ) AS dusmani_naturali

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

            WHERE 1 = 1
        ";

        $allowedFilters = [
            'id_clasa',
            'id_origine',
            'id_regim',
            'id_clima',
            'are_blana',
            'poate_fi_dresat',
            'este_periculos'
        ];

        foreach ($filters as $column => $value) {
            if (in_array($column, $allowedFilters)) {
                $sql .= " AND a.$column = :$column";
            }
        }

        $sql .= " ORDER BY a.id_animal";

        $stmt = oci_parse($this->conn, $sql);

        foreach ($filters as $column => $value) {
            if (in_array($column, $allowedFilters)) {
                oci_bind_by_name($stmt, ":$column", $filters[$column]);
            }
        }

        oci_execute($stmt);

        $results = [];

        while ($row = oci_fetch_assoc($stmt)) {
            $results[] = [
                "id" => $row["ID_ANIMAL"],
                "nume_popular" => $row["NUME_POPULAR"],
                "nume_stiintific" => $row["NUME_STIINTIFIC"],

                "clasa" => $row["CLASA_ANIMAL"],
                "origine" => $row["ORIGINE_ANIMAL"],
                "regim_alimentar" => $row["REGIM_ALIMENTAR"],
                "statut" => $row["STATUT_CONSERVARE"],
                "clima" => $row["CLIMA"],
                "mod_inmultire" => $row["MOD_INMULTIRE"],

                "are_blana" => (int) $row["ARE_BLANA"],
                "poate_fi_dresat" => (int) $row["POATE_FI_DRESAT"],
                "este_periculos" => (int) $row["ESTE_PERICULOS"],

                "descriere_ro" => $row["DESCRIERE_RO"],
                "descriere_en" => $row["DESCRIERE_EN"],

                "url_imagine" => $row["URL_IMAGINE"],
                "dusmani_naturali" => $row["DUSMANI_NATURALI"]
            ];
        }

        oci_free_statement($stmt);

        return $results;
    }
}