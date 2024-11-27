<?php
session_start();
include('app/Models/conexion.php');
include('app/Models/login.php');

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['user_id'])) {
    echo "Acceso no autorizado.";
    exit();
}

$con = new conexion();
$login = new Login($con->conectar());

// Obtener la información del usuario logueado
$user_id = $_SESSION['user_id'];
$user = $login->getUserById($user_id);

// Manejo del formulario de edición de usuario
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['edit_user'])) {
    $cedula = $_POST['cedula'];
    $nit = $_POST['nit'];
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    // Actualizar el usuario (no se permite editar tipo_usuario aquí)
    $result = $login->update($user_id, $nit, $cedula, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $mail, $password, $user['tipo_usuario']);
    if ($result['status'] === 'success') {
        // Mostrar mensaje de éxito y redirigir al usuario a index.php
        echo "<p class='alert alert-success'>" . $result['message'] . "</p>";
        header('Location: index.php'); // Redirigir al usuario al index después de la actualización exitosa
        exit(); // Asegurarse de que el script se detiene después de la redirección
    } else {
        echo "<p class='alert alert-danger'>" . $result['message'] . "</p>";
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil de Usuario</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Editar Perfil</h2>
        <!-- Formulario de edición de perfil de usuario -->
        <form method="POST" action="">
        <div class="mb-3">
                <label for="cedula" class="form-label">NIT</label>
                <input type="text" name="nit" class="form-control" value="<?php echo $user['NIT']; ?>" required readonly>
            </div>
            <div class="mb-3">
                <label for="cedula" class="form-label">Cédula</label>
                <input type="text" name="cedula" class="form-control" value="<?php echo $user['cedula']; ?>" required readonly>
            </div>
            <div class="mb-3">
                <label for="primer_nombre" class="form-label">Primer Nombre</label>
                <input type="text" name="primer_nombre" class="form-control" value="<?php echo $user['primer_nombre']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                <input type="text" name="segundo_nombre" class="form-control" value="<?php echo $user['segundo_nombre']; ?>">
            </div>
            <div class="mb-3">
                <label for="primer_apellido" class="form-label">Primer Apellido</label>
                <input type="text" name="primer_apellido" class="form-control" value="<?php echo $user['primer_apellido']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                <input type="text" name="segundo_apellido" class="form-control" value="<?php echo $user['segundo_apellido']; ?>">
            </div>
            <div class="mb-3">
                <label for="mail" class="form-label">Correo Electrónico</label>
                <input type="email" name="mail" class="form-control" value="<?php echo $user['mail']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="password" class="form-label">Contraseña</label>
                <input type="password" name="password" class="form-control" placeholder="Dejar en blanco para no cambiar la contraseña">
            </div>
            <button type="submit" name="edit_user" class="btn btn-primary">Guardar Cambios</button>
        </form>
    </div>
</body>
</html>
