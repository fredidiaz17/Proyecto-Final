<?php
class conexion {
    private $link;

    function __construct() {
    }   

    public function conectar() {
        $this->link = new mysqli('localhost', 'root', '', 'electrica');
        
        if ($this->link->connect_errno) {
           // echo "Falló la conexión a MySQL: (" . $this->link->connect_errno . ") " . $this->link->connect_error;
        } else {
           // echo "Conexión establecida<br>"; // Move this inside the else block
        }
        
        return $this->link; // Return after the connection check
    }

    public function desconectar() {
        mysqli_close($this->link);
       // echo "Conexión cerrada<br>";
    }
}

?>
