-- Date initiale pentru nomenclatoare

INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Europa');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Africa');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Asia');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'America de Nord');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'America de Sud');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Australia');

INSERT INTO Regimuri_Alimentare (id_regim, denumire) VALUES (secv_regimuri.NEXTVAL, 'Carnivor');
INSERT INTO Regimuri_Alimentare (id_regim, denumire) VALUES (secv_regimuri.NEXTVAL, 'Erbivor');
INSERT INTO Regimuri_Alimentare (id_regim, denumire) VALUES (secv_regimuri.NEXTVAL, 'Omnivor');

INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Mamifer');
INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Pasare');
INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Reptila');
INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Amfibian');
INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Peste');

INSERT INTO Statute_Conservare (id_statut, denumire) VALUES (secv_statute.NEXTVAL, 'Neamenintata');
INSERT INTO Statute_Conservare (id_statut, denumire) VALUES (secv_statute.NEXTVAL, 'Protejata');
INSERT INTO Statute_Conservare (id_statut, denumire) VALUES (secv_statute.NEXTVAL, 'Vulnerabila');
INSERT INTO Statute_Conservare (id_statut, denumire) VALUES (secv_statute.NEXTVAL, 'Pe cale de disparitie');

INSERT INTO Clime (id_clima, denumire) VALUES (secv_clime.NEXTVAL, 'Temperata');
INSERT INTO Clime (id_clima, denumire) VALUES (secv_clime.NEXTVAL, 'Tropicala');
INSERT INTO Clime (id_clima, denumire) VALUES (secv_clime.NEXTVAL, 'Desertica');
INSERT INTO Clime (id_clima, denumire) VALUES (secv_clime.NEXTVAL, 'Polara');

INSERT INTO Moduri_Inmultire (id_inmultire, denumire) VALUES (secv_inmultire.NEXTVAL, 'Vivipar');
INSERT INTO Moduri_Inmultire (id_inmultire, denumire) VALUES (secv_inmultire.NEXTVAL, 'Ovipar');

-- Animale de test

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Leu', 'Panthera leo',
    1, 2, 1, 3, 2, 1,
    1, 1, 1,
    'Leul este un mamifer carnivor originar din Africa, cunoscut pentru coama masculilor si comportamentul social.',
    'The lion is a carnivorous mammal native to Africa, known for the male mane and social behavior.'
);

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Lup', 'Canis lupus',
    1, 1, 1, 2, 1, 1,
    1, 1, 1,
    'Lupul este un mamifer carnivor din Europa si Asia, adaptat la clima temperata si rece.',
    'The wolf is a carnivorous mammal from Europe and Asia, adapted to temperate and cold climates.'
);

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Camila', 'Camelus dromedarius',
    1, 3, 2, 1, 3, 1,
    1, 1, 0,
    'Camila este un mamifer erbivor adaptat la conditii desertice si poate supravietui perioade lungi fara apa.',
    'The camel is a herbivorous mammal adapted to desert conditions and can survive long periods without water.'
);

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Vultur', 'Aquila chrysaetos',
    2, 1, 1, 2, 1, 2,
    0, 0, 1,
    'Vulturul este o pasare rapitoare, carnivora, cu vedere foarte buna si capacitate mare de zbor.',
    'The eagle is a carnivorous bird of prey with excellent eyesight and strong flight ability.'
);

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Iguana', 'Iguana iguana',
    3, 5, 2, 1, 2, 2,
    0, 0, 0,
    'Iguana este o reptila erbivora originara din America de Sud, adaptata la climate calde.',
    'The iguana is a herbivorous reptile native to South America, adapted to warm climates.'
);

-- Imagini initiale

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 1, 'assets/images/leu.jpg', 1);

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 2, 'assets/images/lup.jpg', 1);

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 3, 'assets/images/camila.jpg', 1);

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 4, 'assets/images/vultur.jpg', 1);

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 5, 'assets/images/iguana.jpg', 1);

-- Admin initial
-- Parola nu este finala. Pentru proiect real trebuie hash generat cu password_hash in PHP.
INSERT INTO Administratori (id_admin, username, password_hash, email)
VALUES (secv_admini.NEXTVAL, 'admin', 'admin123', 'admin@zoo.local');

COMMIT;