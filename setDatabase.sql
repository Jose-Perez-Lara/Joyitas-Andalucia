create table artistas(
    idArtista INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(50),
    apellidos VARCHAR(50),
    fechaNacimiento DATE
);

CREATE TABLE conciertos (
    idConcierto INT AUTO_INCREMENT PRIMARY KEY,
    establecimiento VARCHAR(50),
    provincia VARCHAR(50)
);

CREATE TABLE artista_concierto (
    idArtista INT,
    idConcierto INT,
    PRIMARY KEY (idArtista, idConcierto),
    FOREIGN KEY (idArtista) REFERENCES artistas(idArtista) ON DELETE CASCADE,
    FOREIGN KEY (idConcierto) REFERENCES conciertos(idConcierto) ON DELETE CASCADE
);


INSERT INTO artistas (nombre, apellidos, fechaNacimiento) VALUES
('Carlos', 'Santana', '1947-07-20'),
('Shakira', 'Mebarak', '1977-02-02'),
('Luis', 'Miguel', '1970-04-19'),
('Alicia', 'Keys', '1981-01-25'),
('Juanes', 'Aristizábal', '1972-08-09');


INSERT INTO conciertos (establecimiento, provincia) VALUES
('Madison Square Garden', 'Nueva York'),
('La Riviera', 'Madrid'),
('Estadio Azteca', 'Ciudad de México'),
('Royal Albert Hall', 'Londres'),
('Arena Monterrey', 'Monterrey');

INSERT INTO artista_concierto (idArtista, idConcierto) VALUES
(21, 1),
(21, 3),
(22, 2),
(23, 4),
(24, 5),
(25, 1);