<?php
/*session_start();
include('app/Models/conexion.php');
include('app/Models/login.php');

// Verificar si el usuario es administrador
if (!isset($_SESSION['tipo_usuario']) || $_SESSION['tipo_usuario'] !== 'administrador') {
    echo "Acceso no autorizado.";
    exit();
}

$con = new conexion(); 
$login = new Login($con->conectar());

// Manejo de la eliminación del usuario
if (isset($_GET['delete'])) {
    $user_id = $_GET['delete'];
    $result = $login->delete($user_id);
    if ($result['status'] === 'success') {
        echo "<p class='alert alert-success'>" . $result['message'] . "</p>";
    } else {
        echo "<p class='alert alert-danger'>" . $result['message'] . "</p>";
    }
}

// Manejo del formulario de edición de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $user_id = $_POST['user_id'];
    $cedula = $_POST['cedula'];
    $nit = $_POST['nit'];
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $mail = $_POST['mail'];
    $password = $_POST['password'];
    $tipo_usuario = $_POST['tipo_usuario'];

    // Actualizar el usuario
    $result = $login->update($user_id, $nit, $cedula, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $mail, $password, $tipo_usuario);
    if ($result['status'] === 'success') {
        echo "<p class='alert alert-success'>" . $result['message'] . "</p>";
    } else {
        echo "<p class='alert alert-danger'>" . $result['message'] . "</p>";
    }
}
// Obtener la lista de usuarios
$usuarios = $login->lis();
*/
include "../Controller/AdminController.php";
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Usuarios</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Administrar Usuarios</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cédula</th>
                    <th>NIT</th>
                    <th>Primer Nombre</th>
                    <th>Segundo Nombre</th>
                    <th>Primer Apellido</th>
                    <th>Segundo Apellido</th>
                    <th>Correo Electrónico</th>
                    <th>Tipo de Usuario</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($usuarios as $usuario): ?>
                    <tr>
                        <td><?php echo $usuario['id']; ?></td>
                        <td><?php echo $usuario['NIT']; ?></td>
                        <td><?php echo $usuario['cedula']; ?></td>
                        <td><?php echo $usuario['primer_nombre']; ?></td>
                        <td><?php echo $usuario['segundo_nombre']; ?></td>
                        <td><?php echo $usuario['primer_apellido']; ?></td>
                        <td><?php echo $usuario['segundo_apellido']; ?></td>
                        <td><?php echo $usuario['mail']; ?></td>
                        <td><?php echo $usuario['tipo_usuario']; ?></td>
                        <td>
                            <a href="?edit=<?php echo $usuario['id']; ?>" class="btn btn-warning btn-sm">Editar</a>
                            <a href="?delete=<?php echo $usuario['id']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este usuario?');">Eliminar</a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <a href="../../index.php" class="btn btn-warning ms-3">Home</a>
        <?php if (isset($_GET['edit'])): 
            $user_id = $_GET['edit'];
            $user = $login->getUserById($user_id);
        ?>
            <!-- Formulario de edición de usuario -->
            <h3>Editar Usuario</h3>
            <form method="POST" action="">
                <input type="hidden" name="user_id" value="<?php echo $user['id']; ?>">
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula <span style="color: red;">*</span></label>
                    <input type="text" name="cedula" class="form-control" value="<?php echo $user['cedula']; ?>" required readonly>
                </div>
                <div class="mb-3">
                    <label for="cedula" class="form-label">NIT <span style="color: red;">*</span></label>
                    <input type="text" name="nit" class="form-control" value="<?php echo $user['NIT']; ?>" required readonly>
                </div>
                <div class="mb-3">
                    <label for="primer_nombre" class="form-label">Primer Nombre  <span style="color: red;">*</span></label>
                    <input type="text" name="primer_nombre" class="form-control" value="<?php echo $user['primer_nombre']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                    <input type="text" name="segundo_nombre" class="form-control" value="<?php echo $user['segundo_nombre']; ?>">
                </div>
                <div class="mb-3">
                    <label for="primer_apellido" class="form-label">Primer Apellido <span style="color: red;">*</span></label>
                    <input type="text" name="primer_apellido" class="form-control" value="<?php echo $user['primer_apellido']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                    <input type="text" name="segundo_apellido" class="form-control" value="<?php echo $user['segundo_apellido']; ?>">
                </div>
                <div class="mb-3">
                    <label for="mail" class="form-label">Correo Electrónico <span style="color: red;">*</span></label>
                    <input type="email" name="mail" class="form-control" value="<?php echo $user['mail']; ?>" required>
                </div>
                <div class="mb-3">
                    <label for="password" class="form-label">Contraseña <span style="color: red;">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar la contraseña">
                    
                </div>
                <div class="mb-3">
                    <label for="tipo_usuario" class="form-label">Tipo de Usuario</label>
                    <select name="tipo_usuario" class="form-control">
                        <option value="usuario" <?php echo $user['tipo_usuario'] === 'usuario' ? 'selected' : ''; ?>>Usuario</option>
                        <option value="administrador" <?php echo $user['tipo_usuario'] === 'administrador' ? 'selected' : ''; ?>>Administrador</option>
                    </select>
                </div>
                <button type="submit" name="edit_user" class="btn btn-primary">Guardar Cambios</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
