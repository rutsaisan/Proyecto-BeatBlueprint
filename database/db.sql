-- sudo mysql -u root -p

CREATE DATABASE beat_blueprint;

USE beat_blueprint;


CREATE TABLE Estilo_baile (
    id_estilo_baile INT AUTO_INCREMENT PRIMARY KEY,
    nombre_estilo_baile VARCHAR(100) NOT NULL UNIQUE
);


CREATE TABLE Usuarios (
    id_usuario INT AUTO_INCREMENT PRIMARY KEY,
    usuario VARCHAR(50) NOT NULL UNIQUE,
    nombre_completo VARCHAR(100),
    email VARCHAR(100) NOT NULL UNIQUE,
    contrasena VARCHAR(255) NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    id_estilo_baile INT,
    FOREIGN KEY (id_estilo_baile) REFERENCES Estilo_baile(id_estilo_baile) ON DELETE SET NULL
);


CREATE TABLE Wikipasos (
    id_paso INT AUTO_INCREMENT PRIMARY KEY,
    nombre_paso VARCHAR(100) NOT NULL,
    nivel ENUM('Básico', 'Medio', 'Avanzado') NOT NULL,
    descripcion TEXT,
    tutorial VARCHAR(255),
    imagen VARCHAR(255),
    id_estilo_baile INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_estilo_baile) REFERENCES Estilo_baile(id_estilo_baile) ON DELETE CASCADE
);


CREATE TABLE Videos (
    id_video INT AUTO_INCREMENT PRIMARY KEY,
    video VARCHAR(255) NOT NULL,
    descripcion TEXT,
    id_estilo_baile INT NOT NULL,
    id_usuario INT NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES Usuarios(id_usuario) ON DELETE CASCADE,
    FOREIGN KEY (id_estilo_baile) REFERENCES Estilo_baile(id_estilo_baile) ON DELETE CASCADE
);


CREATE TABLE Canciones (
    id_cancion INT AUTO_INCREMENT PRIMARY KEY,
    ruta_mp3 VARCHAR(255),
    nombre_cancion VARCHAR(100),
    artista VARCHAR(100),
    id_estilo_baile INT NOT NULL,
    FOREIGN KEY (id_estilo_baile) REFERENCES Estilo_baile(id_estilo_baile) ON DELETE CASCADE
);

-- crea usuario nuevo con contraseña
CREATE USER 
'beatblueprint'@'%' 
IDENTIFIED  BY 'BeatBlueprint123$';
-- permite acceso a ese usuario
GRANT USAGE ON *.* TO 'beatblueprint'@'%';
-- quitale todos los limites que tenga
ALTER USER 'beatblueprint'@'%' 
REQUIRE NONE 
WITH MAX_QUERIES_PER_HOUR 0 
MAX_CONNECTIONS_PER_HOUR 0 
MAX_UPDATES_PER_HOUR 0 
MAX_USER_CONNECTIONS 0;
-- dale acceso a la base de datos empresadam
GRANT ALL PRIVILEGES ON `beat_blueprint`.* 
TO 'beatblueprint'@'%';
-- recarga la tabla de privilegios
FLUSH PRIVILEGES;
