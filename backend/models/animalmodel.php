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
                ) AS dusmani_naturali,
                
                (
                    SELECT LISTAGG(ax.nume_popular, ', ') WITHIN GROUP (ORDER BY ax.nume_popular)
                    FROM Animale ax
                    WHERE ax.id_animal IN (
                        SELECT si.id_animal2 FROM Specii_Inrudite si WHERE si.id_animal1 = a.id_animal
                        UNION
                        SELECT si.id_animal1 FROM Specii_Inrudite si WHERE si.id_animal2 = a.id_animal
                    )
                ) AS specii_inrudite

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
            'este_periculos',
            'id_statut', 
            'id_inmultire'
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
        $idUriVazute = [];

        while ($row = oci_fetch_assoc($stmt)) {
            $idAnimal = $row["ID_ANIMAL"];

            if (!in_array($idAnimal, $idUriVazute)) {
                $idUriVazute[] = $idAnimal;

            $results[] = [
                "id" => $idAnimal,
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

                "descriere_ro" => isset($row["DESCRIERE_RO"]) ? $row["DESCRIERE_RO"] : '',
                "descriere_en" => isset($row["DESCRIERE_EN"]) ? $row["DESCRIERE_EN"] : '',

                "url_imagine" => $row["URL_IMAGINE"],
                "dusmani_naturali" => $row["DUSMANI_NATURALI"],
                "specii_inrudite" => $row["SPECII_INRUDITE"]
            ];
        }

        }
        oci_free_statement($stmt);

        return $results;
    }

    public function deleteAnimal($id) {
        $sql1 = "DELETE FROM Imagini_Animale WHERE id_animal = :id";
        $stmt1 = oci_parse($this->conn, $sql1); oci_bind_by_name($stmt1, ":id", $id); oci_execute($stmt1); oci_free_statement($stmt1);

        $sql2 = "DELETE FROM Dusmani_Naturali WHERE id_prada = :id OR id_pradator = :id";
        $stmt2 = oci_parse($this->conn, $sql2); oci_bind_by_name($stmt2, ":id", $id); oci_execute($stmt2); oci_free_statement($stmt2);

        $sql3 = "DELETE FROM Specii_Inrudite WHERE id_animal1 = :id OR id_animal2 = :id";
        $stmt3 = oci_parse($this->conn, $sql3); oci_bind_by_name($stmt3, ":id", $id); oci_execute($stmt3); oci_free_statement($stmt3);

        $sqlMain = "DELETE FROM Animale WHERE id_animal = :id";
        $stmtMain = oci_parse($this->conn, $sqlMain); oci_bind_by_name($stmtMain, ":id", $id); 
        $rezultat = oci_execute($stmtMain); 
        oci_free_statement($stmtMain);

        return $rezultat;
    }

    public function addAnimal($data) {
        $sql = "INSERT INTO Animale (
                    id_animal, nume_popular, nume_stiintific, 
                    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
                    are_blana, poate_fi_dresat, este_periculos, descriere_ro , descriere_en
                ) VALUES (
                    secv_animale.NEXTVAL, :nume_pop, :nume_st, 
                    :id_clasa, :id_orig, :id_regim, :id_statut, :id_clima, :id_inm,
                    :blana, :dresabil, :periculos, :desc_ro, :desc_en
                ) RETURNING id_animal INTO :last_id";

        $stmt = oci_parse($this->conn, $sql);
        
        oci_bind_by_name($stmt, ":nume_pop", $data->nume_popular);
        oci_bind_by_name($stmt, ":nume_st", $data->nume_stiintific);
        oci_bind_by_name($stmt, ":id_clasa", $data->id_clasa);
        oci_bind_by_name($stmt, ":id_orig", $data->id_origine);
        oci_bind_by_name($stmt, ":id_regim", $data->id_regim);
        oci_bind_by_name($stmt, ":id_statut", $data->id_statut);
        oci_bind_by_name($stmt, ":id_clima", $data->id_clima);
        oci_bind_by_name($stmt, ":id_inm", $data->id_inmultire);
        oci_bind_by_name($stmt, ":blana", $data->are_blana);
        oci_bind_by_name($stmt, ":dresabil", $data->poate_fi_dresat);
        oci_bind_by_name($stmt, ":periculos", $data->este_periculos);
        oci_bind_by_name($stmt, ":desc_ro", $data->descriere_ro);
        oci_bind_by_name($stmt, ":desc_en", $data->descriere_en);
        
        oci_bind_by_name($stmt, ":last_id", $new_id, 10);

        if (!oci_execute($stmt)) return false;

        if (!empty($data->imagine)) {
            $img_path = "assets/images/" . $data->imagine;
            $sqlImg = "INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala) 
                       VALUES (secv_imagini.NEXTVAL, :new_id, :path, 1)";
            $stmtImg = oci_parse($this->conn, $sqlImg);
            oci_bind_by_name($stmtImg, ":new_id", $new_id);
            oci_bind_by_name($stmtImg, ":path", $img_path);
            oci_execute($stmtImg);
        }

        return true;
    }

    public function preiaDictionar($tabel, $id_col, $nume_col) {
        $sql = "SELECT $id_col AS ID, $nume_col AS NUME FROM $tabel ORDER BY $nume_col";
        $stmt = oci_parse($this->conn, $sql);
        oci_execute($stmt);
        $data = [];
        while ($row = oci_fetch_assoc($stmt)) { $data[] = $row; }
        return $data;
    }

    public function deleteAllAnimals() {
        $sql = "DELETE FROM animale";
        $stmt = oci_parse($this->conn, $sql);
        
        $result = @oci_execute($stmt);
        
        if (!$result) {
            $error = oci_error($stmt);
            throw new Exception("Oracle refuză ștergerea: " . $error['message']);
        }
    }

    public function curataBazaDeDate() {
        $queries = [
            "DELETE FROM Dusmani_Naturali",
            "DELETE FROM Specii_Inrudite",
            "DELETE FROM Imagini_Animale",
            "DELETE FROM Animale"
        ];

        foreach ($queries as $sql) {
            $stmt = oci_parse($this->conn, $sql);

            if (!$stmt) {
                $error = oci_error($this->conn);
                throw new Exception("Eroare la pregătirea query-ului: " . $error['message']);
            }

            if (!oci_execute($stmt, OCI_NO_AUTO_COMMIT)) {
                $error = oci_error($stmt);
                oci_rollback($this->conn);
                throw new Exception("Eroare la ștergere pentru query [$sql]: " . $error['message']);
            }

            oci_free_statement($stmt);
        }

        oci_commit($this->conn);
    }
}