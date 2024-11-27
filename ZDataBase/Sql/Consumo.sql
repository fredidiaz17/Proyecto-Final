CREATE TABLE consumo (
    id INT AUTO_INCREMENT PRIMARY KEY,
    cedula VARCHAR(20) NOT NULL,    
    fecha DATE,               
    Consumo FLOAT,   
    Horas INT,      
    tipo_consumo ENUM('bajo', 'medio', 'alto') DEFAULT NULL,         
    FOREIGN KEY (cedula) REFERENCES usuarios(cedula)
);
