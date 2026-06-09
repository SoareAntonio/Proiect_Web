
CREATE TABLE Origini (
    id_origine NUMBER(10) PRIMARY KEY,
    denumire VARCHAR2(100) NOT NULL UNIQUE
);
CREATE SEQUENCE secv_origini START WITH 1 INCREMENT BY 1;

CREATE TABLE Regimuri_Alimentare (
    id_regim NUMBER(10) PRIMARY KEY,
    denumire VARCHAR2(100) NOT NULL UNIQUE
);
CREATE SEQUENCE secv_regimuri START WITH 1 INCREMENT BY 1;

CREATE TABLE Clase_Animale (
    id_clasa NUMBER(10) PRIMARY KEY,
    denumire VARCHAR2(100) NOT NULL UNIQUE 
);
CREATE SEQUENCE secv_clase START WITH 1 INCREMENT BY 1;

CREATE TABLE Statute_Conservare (
    id_statut NUMBER(10) PRIMARY KEY,
    denumire VARCHAR2(100) NOT NULL UNIQUE 
);
CREATE SEQUENCE secv_statute START WITH 1 INCREMENT BY 1;

CREATE TABLE Clime (
    id_clima NUMBER(10) PRIMARY KEY,
    denumire VARCHAR2(100) NOT NULL UNIQUE 
);
CREATE SEQUENCE secv_clime START WITH 1 INCREMENT BY 1;

CREATE TABLE Moduri_Inmultire (
    id_inmultire NUMBER(10) PRIMARY KEY,
    denumire VARCHAR2(100) NOT NULL UNIQUE 
);
CREATE SEQUENCE secv_inmultire START WITH 1 INCREMENT BY 1;


CREATE TABLE Animale (
    id_animal NUMBER(10) PRIMARY KEY,
    nume_popular VARCHAR2(150) NOT NULL,
    nume_stiintific VARCHAR2(150) NOT NULL UNIQUE,
    
    id_clasa NUMBER(10) NOT NULL,
    id_origine NUMBER(10) NOT NULL,
    id_regim NUMBER(10) NOT NULL,
    id_statut NUMBER(10) NOT NULL,
    id_clima NUMBER(10) NOT NULL,        
    id_inmultire NUMBER(10) NOT NULL,    
    
    are_blana NUMBER(1) DEFAULT 0 CHECK (are_blana IN (0, 1)),
    poate_fi_dresat NUMBER(1) DEFAULT 0 CHECK (poate_fi_dresat IN (0, 1)),
    este_periculos NUMBER(1) DEFAULT 0 CHECK (este_periculos IN (0, 1)),
    
    descriere_ro VARCHAR2(4000), 
    descriere_en VARCHAR2(4000),
    
    CONSTRAINT fk_clasa FOREIGN KEY (id_clasa) REFERENCES Clase_Animale(id_clasa),
    CONSTRAINT fk_origine FOREIGN KEY (id_origine) REFERENCES Origini(id_origine),
    CONSTRAINT fk_regim FOREIGN KEY (id_regim) REFERENCES Regimuri_Alimentare(id_regim),
    CONSTRAINT fk_statut FOREIGN KEY (id_statut) REFERENCES Statute_Conservare(id_statut),
    CONSTRAINT fk_clima FOREIGN KEY (id_clima) REFERENCES Clime(id_clima),
    CONSTRAINT fk_inmultire FOREIGN KEY (id_inmultire) REFERENCES Moduri_Inmultire(id_inmultire)
);
CREATE SEQUENCE secv_animale START WITH 1 INCREMENT BY 1;

CREATE TABLE Specii_Inrudite (
    id_animal1 NUMBER(10) NOT NULL,
    id_animal2 NUMBER(10) NOT NULL,
    PRIMARY KEY (id_animal1, id_animal2),
    CONSTRAINT fk_inrudit1 FOREIGN KEY (id_animal1) REFERENCES Animale(id_animal),
    CONSTRAINT fk_inrudit2 FOREIGN KEY (id_animal2) REFERENCES Animale(id_animal)
);

CREATE TABLE Dusmani_Naturali (
    id_prada NUMBER(10) NOT NULL,
    id_pradator NUMBER(10) NOT NULL,
    PRIMARY KEY (id_prada, id_pradator),
    CONSTRAINT fk_prada FOREIGN KEY (id_prada) REFERENCES Animale(id_animal),
    CONSTRAINT fk_pradator FOREIGN KEY (id_pradator) REFERENCES Animale(id_animal)
);

CREATE TABLE Imagini_Animale (
    id_imagine NUMBER(10) PRIMARY KEY,
    id_animal NUMBER(10) NOT NULL,
    url_imagine VARCHAR2(255) NOT NULL, 
    este_imagine_principala NUMBER(1) DEFAULT 0 CHECK (este_imagine_principala IN (0, 1)),
    CONSTRAINT fk_animal_img FOREIGN KEY (id_animal) REFERENCES Animale(id_animal)
);
CREATE SEQUENCE secv_imagini START WITH 1 INCREMENT BY 1;

CREATE TABLE Administratori (
    id_admin NUMBER(10) PRIMARY KEY,
    username VARCHAR2(50) NOT NULL UNIQUE,
    password_hash VARCHAR2(255) NOT NULL, 
    email VARCHAR2(100) NOT NULL UNIQUE
);
CREATE SEQUENCE secv_admini START WITH 1 INCREMENT BY 1;

ALTER TABLE Administratori 
ADD data_creare TIMESTAMP DEFAULT CURRENT_TIMESTAMP;

CREATE TABLE Utilizatori (
    id_user NUMBER(10) PRIMARY KEY,
    username VARCHAR2(50) UNIQUE NOT NULL,
    password_hash VARCHAR2(255) NOT NULL,
    email VARCHAR2(100) NOT NULL UNIQUE,
    data_creare TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE SEQUENCE seq_utilizatori_id START WITH 1 INCREMENT BY 1;

CREATE OR REPLACE TRIGGER trg_utilizatori_id
BEFORE INSERT ON Utilizatori
FOR EACH ROW
BEGIN
    IF :new.id_user IS NULL THEN
        :new.id_user := seq_utilizatori_id.NEXTVAL;
    END IF;
END;
/