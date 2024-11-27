<?php
class Consumo {
    private $link;

    // Constructor para recibir la conexión a la base de datos
    function __construct($link) {
        $this->link = $link;
    }

    // Método para crear un nuevo consumo
    public function create($cedula, $consumo, $fecha, $tipo_consumo, $horas) {
        // Asegúrate de que el consumo y horas son números enteros
        $consumo = (float) $consumo; // Cambiar a float para coincidir con el tipo de datos en la base de datos
        $horas = (int) $horas; // Asegúrate de que las horas son un número entero

        // Insertar el nuevo consumo en la base de datos
        $sql_insert = "INSERT INTO consumo (cedula, consumo, fecha, tipo_consumo, horas) VALUES (?, ?, ?, ?, ?)";
        $stmt_insert = $this->link->prepare($sql_insert);

        // Comprobar si la preparación de la declaración fue exitosa
        if (!$stmt_insert) {
            return [
                'status' => 'error',
                'message' => 'Error al preparar la consulta: ' . $this->link->error
            ];
        }

        $stmt_insert->bind_param("sissi", $cedula, $consumo, $fecha, $tipo_consumo, $horas);

        if ($stmt_insert->execute()) {
            return [
                'status' => 'success',
                'message' => 'Consumo registrado exitosamente.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Error al registrar el consumo: ' . $stmt_insert->error
            ];
        }
    }

    // Método para listar todos los registros de consumo
    public function list() {
        $sql = "SELECT * FROM consumo ORDER BY fecha";
        $stmt = $this->link->prepare($sql);

        if (!$stmt) {
            return [];
        }

        $stmt->execute();
        $result = $stmt->get_result();
        $arr = [];

        while ($row = $result->fetch_assoc()) {
            $arr[] = $row;
        }

        return $arr;
    }

    public function getConsumoById(int $id) {
        // Verificar si la conexión es válida
        if (!$this->link) {
            die("Error en la conexión a la base de datos.");
        }
    
        // Preparar la consulta
        $sql = "SELECT * FROM consumo WHERE id = ?";
        $stmt = $this->link->prepare($sql);
    
        // Verificar si la preparación fue exitosa
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->link->error);
        }
    
        // Enlazar el parámetro de entrada
        $stmt->bind_param("i", $id);
    
        // Ejecutar la consulta
        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta: " . $stmt->error);
        }
    
        // Obtener el resultado de la consulta
        $result = $stmt->get_result();
    
        // Verificar si se encontraron registros
        if ($result && $result->num_rows > 0) {
            // Devolver solo el primer registro encontrado
            return $result->fetch_assoc();
        } else {
            // Si no se encuentran registros, retornar null
            return null;
        }
    }
    public function getConsumoByCedula(int $cedula) {
        // Verificar si la conexión es válida
        if (!$this->link) {
            die("Error en la conexión a la base de datos.");
        }
    
        // Preparar la consulta
        $sql = "SELECT * FROM consumo WHERE cedula = ?";
        $stmt = $this->link->prepare($sql);
    
        // Verificar si la preparación fue exitosa
        if (!$stmt) {
            die("Error al preparar la consulta: " . $this->link->error); // Aquí muestra el error de la base de datos
        }
    
        // Verificar si el parámetro cedula es válido
        if (!is_int($cedula)) {
            die("El parámetro cedula debe ser un número entero.");
        }
    
        // Enlazar el parámetro de entrada
        $stmt->bind_param("i", $cedula);
    
        // Ejecutar la consulta
        if (!$stmt->execute()) {
            die("Error al ejecutar la consulta: " . $stmt->error); // Aquí muestra el error de la ejecución
        }
    
        // Obtener el resultado de la consulta
        $result = $stmt->get_result();
    
        // Verificar si se encontraron registros
        if ($result && $result->num_rows > 0) {
            // Devolver solo el primer registro encontrado
            return $result->fetch_assoc();
        } else {
            // Si no se encuentran registros, retornar null
            return null;
        }
    }
    
        
    

    // Método para actualizar un registro de consumo
    public function update(int $id, string $cedula, string $fecha, float $consumo, string $tipo_consumo, int $horas) {
        // Validar los campos obligatorios
        if (empty($cedula) || empty($fecha) || empty($tipo_consumo)) {
            return ['status' => 'error', 'message' => 'Los campos cédula, fecha y tipo de consumo son obligatorios.'];
        }

        // Consulta SQL para actualizar el registro
        $sql_update = "UPDATE consumo SET cedula = ?, fecha = ?, consumo = ?, tipo_consumo = ?, horas = ? WHERE id = ?";
        $stmt_update = $this->link->prepare($sql_update);

        // Comprobar si la preparación de la consulta fue exitosa
        if (!$stmt_update) {
            return ['status' => 'error', 'message' => 'Error al preparar la consulta: ' . $this->link->error];
        }

        // Bind de parámetros
        $stmt_update->bind_param("sssisi", $cedula, $fecha, $consumo, $tipo_consumo, $horas, $id);

        // Ejecutar la consulta y manejar el resultado
        if ($stmt_update->execute()) {
            return ['status' => 'success', 'message' => 'Consumo actualizado exitosamente.'];
        } else {
            return ['status' => 'error', 'message' => 'Error al actualizar el consumo: ' . $stmt_update->error];
        }
    }

    // Método para eliminar un registro de consumo por su ID
    public function delete(int $id) {
        $sql_delete = "DELETE FROM consumo WHERE id = ?";
        $stmt_delete = $this->link->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);

        if (!$stmt_delete) {
            return ['status' => 'error', 'message' => 'Error al preparar la consulta: ' . $this->link->error];
        }

        if ($stmt_delete->execute()) {
            return ['status' => 'success', 'message' => 'Consumo eliminado exitosamente.'];
        } else {
            return ['status' => 'error', 'message' => 'Error al eliminar el consumo: ' . $stmt_delete->error];
        }
    }
    // Método para obtener el valor de tipo de consumo en SQL
// Método para actualizar solo el tipo de consumo
public function actualizarTipoConsumo(int $id, string $nuevo_tipo) {
    // Obtener el valor correspondiente en SQL
    $tipo_consumo_sql = $this->obtenerValorTipoConsumo($nuevo_tipo);
    if ($tipo_consumo_sql === null) {
        return ['status' => 'error', 'message' => 'Tipo de consumo no válido.'];
    }

    // Consulta SQL para actualizar solo el tipo de consumo
    $sql_update = "UPDATE consumo SET tipo_consumo = ? WHERE id = ?";
    $stmt_update = $this->link->prepare($sql_update);

    // Comprobar si la preparación de la consulta fue exitosa
    if (!$stmt_update) {
        return ['status' => 'error', 'message' => 'Error al preparar la consulta: ' . $this->link->error];
    }

    // Bind de parámetros
    $stmt_update->bind_param("si", $tipo_consumo_sql, $id);

    // Ejecutar la consulta y manejar el resultado
    if ($stmt_update->execute()) {
        return ['status' => 'success', 'message' => 'Tipo de consumo actualizado exitosamente.'];
    } else {
        return ['status' => 'error', 'message' => 'Error al actualizar el tipo de consumo: ' . $stmt_update->error];
    }
}

// Método para obtener el valor de tipo de consumo en SQL
private function obtenerValorTipoConsumo($tipo_consumo) {
    switch (strtolower($tipo_consumo)) {
        case 'bajo':
            return 'bajo'; // Valor para 'bajo' en SQL
        case 'medio':
            return 'medio'; // Valor para 'medio' en SQL
        case 'alto':
            return 'alto'; // Valor para 'alto' en SQL
        default:
            return null; // Manejar el caso de tipo no válido
    }
}

}
?>
