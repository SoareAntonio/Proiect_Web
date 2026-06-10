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
    secv_animale.NEXTVAL, 'Lup Cenușiu', 'Canis lupus',
    1, 1, 1, 1, 1, 1,
    1, 0, 1,
    'Lupul este un prădător social extrem de inteligent, care trăiește și vânează în haite bine organizate.',
    'The wolf is a highly intelligent social predator that lives and hunts in well-organized packs.'
);

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Tigru Siberian', 'Panthera tigris altaica',
    1, 3, 1, 4, 1, 1,
    1, 0, 1,
    'Cea mai mare felină din lume, adaptată perfect la mediile reci datorită blănii sale groase.',
    'The largest cat in the world, perfectly adapted to cold environments thanks to its thick fur.'
);

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Ursul Brun', 'Ursus arctos',
    1, 1, 3, 2, 1, 1,
    1, 0, 1,
    'Un mamifer masiv, solitar, cu o dietă foarte variată, de la fructe de pădure până la pește.',
    'A massive, solitary mammal with a highly varied diet, from berries to fish.'
);

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Căprioara', 'Capreolus capreolus',
    1, 1, 2, 1, 1, 1,
    1, 0, 0,
    'Un mamifer ierbivor elegant și sperios, foarte răspândit în pădurile de foioase.',
    'An elegant and skittish herbivorous mammal, widespread in deciduous forests.'
);

INSERT INTO Animale (
    id_animal, nume_popular, nume_stiintific,
    id_clasa, id_origine, id_regim, id_statut, id_clima, id_inmultire,
    are_blana, poate_fi_dresat, este_periculos,
    descriere_ro, descriere_en
) VALUES (
    secv_animale.NEXTVAL, 'Vulturul Pleșuv', 'Haliaeetus leucocephalus',
    2, 4, 1, 2, 1, 2,
    0, 1, 1,
    'Pasăre de pradă impunătoare, simbol al Americii de Nord, recunoscută după capul acoperit cu pene albe.',
    'Imposing bird of prey, symbol of North America, recognized by its white-feathered head.'
);

-- Imagini initiale

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 1, 'assets/images/lup_cenusiu.jpg', 1);

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 2, 'assets/images/tigru_siberian.jpg', 1);

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 3, 'assets/images/urs_brun.jpg', 1);

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 4, 'assets/images/caprioara.jpg', 1);

INSERT INTO Imagini_Animale (id_imagine, id_animal, url_imagine, este_imagine_principala)
VALUES (secv_imagini.NEXTVAL, 5, 'assets/images/vultur_plesuv.jpg', 1);


-- Admin initial
INSERT INTO Administratori (id_admin, username, password_hash, email)
VALUES (secv_admini.NEXTVAL, 'admin', '$2y$10$bhkdWkIFMndhi/j22s14WeqWLR2NUdHOcGSi2hcWg6yCStK/2ATRO', 'admin@zoo.ro');

COMMIT;