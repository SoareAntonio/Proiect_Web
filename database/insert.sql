
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Europa');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Africa');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Asia');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'America de Nord');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'America de Sud');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Australia');
INSERT INTO Origini (id_origine, denumire) VALUES (secv_origini.NEXTVAL, 'Antarctica');

INSERT INTO Regimuri_Alimentare (id_regim, denumire) VALUES (secv_regimuri.NEXTVAL, 'Carnivor');
INSERT INTO Regimuri_Alimentare (id_regim, denumire) VALUES (secv_regimuri.NEXTVAL, 'Erbivor');
INSERT INTO Regimuri_Alimentare (id_regim, denumire) VALUES (secv_regimuri.NEXTVAL, 'Omnivor');
INSERT INTO Regimuri_Alimentare (id_regim, denumire) VALUES (secv_regimuri.NEXTVAL, 'Vegetarian');

INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Mamifer');
INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Pasare');
INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Reptila');
INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Amfibian');
INSERT INTO Clase_Animale (id_clasa, denumire) VALUES (secv_clase.NEXTVAL, 'Peste');

INSERT INTO Statute_Conservare (id_statut, denumire) VALUES (secv_statute.NEXTVAL, 'Daunatoare');
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
INSERT INTO Moduri_Inmultire (id_inmultire, denumire) VALUES (secv_inmultire.NEXTVAL, 'Ovovivipar');
INSERT INTO Moduri_Inmultire (id_inmultire, denumire) VALUES (secv_inmultire.NEXTVAL, 'Asexuata');



-- Admin initial
INSERT INTO Administratori (id_admin, username, password_hash, email)
VALUES (secv_admini.NEXTVAL, 'admin', '$2y$10$bhkdWkIFMndhi/j22s14WeqWLR2NUdHOcGSi2hcWg6yCStK/2ATRO', 'admin@zoo.ro');

COMMIT;