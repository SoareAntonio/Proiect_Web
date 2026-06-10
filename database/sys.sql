--Inlocuiți 'parola_ta_aici' cu o parolă sigură înainte de a rula scriptul
CREATE USER zoo_admin IDENTIFIED BY parola_ta_aici;
GRANT CONNECT, RESOURCE TO zoo_admin;
ALTER USER zoo_admin QUOTA UNLIMITED ON USERS;
COMMIT;