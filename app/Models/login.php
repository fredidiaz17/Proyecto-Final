<?php
class Login {
    private $link;

    // Constructor para recibir la conexión a la base de datos
    function __construct($link) {
        $this->link = $link;
    }

    // Método para crear un nuevo usuario, reutilizando IDs eliminados si están disponibles
    public function create($nit, $cedula, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $mail, $password, $tipo_usuario = 'usuario') {
        // Verificar si la cédula ya está registrada
        $sql_check_cedula = "SELECT id FROM usuarios WHERE cedula = ?";
        $stmt_check_cedula = $this->link->prepare($sql_check_cedula);
        $stmt_check_cedula->bind_param("s", $cedula);
        $stmt_check_cedula->execute();
        $result_cedula = $stmt_check_cedula->get_result();
    
        if ($result_cedula->num_rows > 0) {
            return [
                'status' => 'error',
                'message' => 'La cédula ya está registrada.'
            ];
        }

        $sql_check_nit = "SELECT id FROM usuarios WHERE NIT = ?";
        $stmt_check_nit = $this->link->prepare($sql_check_nit);
        $stmt_check_nit->bind_param("s", $nit);
        $stmt_check_nit->execute();
        $result_nit = $stmt_check_nit->get_result();
    
        if ($result_nit->num_rows > 0) {
            return [
                'status' => 'error',
                'message' => 'El nit ya está registrada.'
            ];
        }
    
        // Verificar si el correo electrónico ya está registrado
        $sql_check_email = "SELECT id FROM usuarios WHERE mail = ?";
        $stmt_check_email = $this->link->prepare($sql_check_email);
        $stmt_check_email->bind_param("s", $mail);
        $stmt_check_email->execute();
        $result_email = $stmt_check_email->get_result();
    
        if ($result_email->num_rows > 0) {
            return [
                'status' => 'error',
                'message' => 'El correo electrónico ya está registrado.'
            ];
        }
    
        // Hashear la contraseña
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    
        // Verificar si hay IDs eliminados disponibles
        $sql_check_deleted = "SELECT id FROM ids_eliminados LIMIT 1";
        $result_deleted = $this->link->query($sql_check_deleted);
    
        if ($result_deleted->num_rows > 0) {
            // Reutilizar el ID eliminado
            $row = $result_deleted->fetch_assoc();
            $id = $row['id'];
    
            // Eliminar ese ID de la tabla `ids_eliminados`
            $sql_delete_id = "DELETE FROM ids_eliminados WHERE id = ?";
            $stmt_delete = $this->link->prepare($sql_delete_id);
            $stmt_delete->bind_param("i", $id);
            $stmt_delete->execute();
        } else {
            // Si no hay IDs eliminados, dejar que se utilice un nuevo autoincremental
            $id = NULL;
        }
    
        // Insertar el nuevo usuario sin el campo `id`
        $sql_insert = "INSERT INTO usuarios (NIT, cedula, primer_nombre, segundo_nombre, primer_apellido, segundo_apellido, mail, password, tipo_usuario)
                       VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmt_insert = $this->link->prepare($sql_insert);
        $stmt_insert->bind_param("issssssss", $nit, $cedula, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $mail, $hashed_password, $tipo_usuario);
        
        if ($stmt_insert->execute()) {
            return [
                'status' => 'success',
                'message' => 'Usuario registrado exitosamente.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Error al registrar el usuario: ' . $stmt_insert->error
            ];
        }
    }
    

    // Método para listar todos los usuarios
    public function lis() {
        $sql = 'SELECT * FROM `usuarios` ORDER BY CAST(`cedula` AS UNSIGNED) ASC';
        $result = $this->link->query($sql);
        $arr = array();
        
        while ($fil = $result->fetch_assoc()) {
            $arr[] = $fil;
        }
        
        return $arr;
    }
    

    // Método para obtener un usuario por su ID
    public function getUserById($id) {
        $sql = "SELECT * FROM usuarios WHERE id = ?";
        $stmt = $this->link->prepare($sql);
        $stmt->bind_param("i", $id);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }
    public function getUserBycedula($cedula) {
        $sql = "SELECT * FROM usuarios WHERE cedula = ?";
        $stmt = $this->link->prepare($sql);
        $stmt->bind_param("i", $cedula);
        $stmt->execute();
        $result = $stmt->get_result();
        return $result->fetch_assoc();
    }

    public function update($id, $nit, $cedula, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $mail, $password, $tipo_usuario) {
        // Comienza construyendo el SQL básico
        $sql_update = "UPDATE usuarios SET NIT = ?, cedula = ?, primer_nombre = ?, segundo_nombre = ?, primer_apellido = ?, segundo_apellido = ?, mail = ?, tipo_usuario = ? WHERE id = ?";
        
        // Prepara la sentencia de actualización sin contraseña
        $stmt_update = $this->link->prepare($sql_update);
        $stmt_update->bind_param("ssssssssi", $nit, $cedula, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $mail, $tipo_usuario, $id);
        
        // Ejecutar la actualización sin cambiar la contraseña
        if ($stmt_update->execute()) {
            // Si hay una nueva contraseña, actualízala por separado
            if (!empty($password)) {
                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql_update_password = "UPDATE usuarios SET password = ? WHERE id = ?";
                $stmt_update_password = $this->link->prepare($sql_update_password);
                $stmt_update_password->bind_param("si", $hashed_password, $id);
                $stmt_update_password->execute();
            }
            return [
                'status' => 'success',
                'message' => 'Usuario actualizado exitosamente.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Error al actualizar el usuario: ' . $stmt_update->error
            ];
        }
    }

    // Método para eliminar un usuario y guardar su ID en la tabla ids_eliminados
    public function delete($id) {
        // Eliminar el usuario de la tabla `usuarios`
        $sql_delete = "DELETE FROM usuarios WHERE id = ?";
        $stmt_delete = $this->link->prepare($sql_delete);
        $stmt_delete->bind_param("i", $id);
        
        if ($stmt_delete->execute()) {
            // Guardar el ID eliminado en la tabla `ids_eliminados` para reutilizarlo
            $sql_insert_deleted = "INSERT INTO ids_eliminados (id) VALUES (?)";
            $stmt_insert_deleted = $this->link->prepare($sql_insert_deleted);
            $stmt_insert_deleted->bind_param("i", $id);
            $stmt_insert_deleted->execute();
            
            return [
                'status' => 'success',
                'message' => 'Usuario eliminado y ID guardado para reutilización.'
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Error al eliminar el usuario: ' . $stmt_delete->error
            ];
        }
    }

    public function login($mail, $password) {
        // Buscar al usuario por el email
        $sql = "SELECT id, password, tipo_usuario FROM usuarios WHERE mail = ?";
        $stmt = $this->link->prepare($sql);
        $stmt->bind_param("s", $mail);
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Verificar la contraseña usando password_verify
            if (password_verify($password, $user['password'])) {
                // Inicio de sesión exitoso
                return [
                    'status' => 'success',
                    'message' => 'Inicio de sesión exitoso',
                    'user_id' => $user['id'],
                    'tipo_usuario' => $user['tipo_usuario']
                ];
            } else {
                // Contraseña incorrecta
                return [
                    'status' => 'error',
                    'message' => 'Contraseña incorrecta'
                ];
            }
        } else {
            // Usuario no encontrado
            return [
                'status' => 'error',
                'message' => 'Usuario no encontrado'
            ];
        }
    }
}
?>

