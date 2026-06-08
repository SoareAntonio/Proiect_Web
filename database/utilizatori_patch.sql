-- Tabel pentru conturi de vizitatori create din register.html

CREATE TABLE Utilizatori (
    id_utilizator NUMBER(10) PRIMARY KEY,
    username VARCHAR2(50) NOT NULL UNIQUE,
    email VARCHAR2(100) NOT NULL UNIQUE,
    password_hash VARCHAR2(255) NOT NULL,
    rol VARCHAR2(30) DEFAULT 'vizitator' NOT NULL,
    data_creare DATE DEFAULT SYSDATE NOT NULL
);

CREATE SEQUENCE secv_utilizatori START WITH 1 INCREMENT BY 1;

COMMIT;
