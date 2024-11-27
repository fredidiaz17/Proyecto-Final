CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,   -- ID autoincremental como clave primaria
    NIT INT(20) UNIQUE NOT NULL,
    cedula VARCHAR(20) UNIQUE NOT NULL,            -- Cédula como un campo único (no clave primaria)
    primer_nombre VARCHAR(50) NOT NULL,
    segundo_nombre VARCHAR(50),
    primer_apellido VARCHAR(50) NOT NULL,
    segundo_apellido VARCHAR(50),
    mail VARCHAR(100) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    tipo_usuario ENUM('administrador', 'usuario') DEFAULT 'usuario'
);
