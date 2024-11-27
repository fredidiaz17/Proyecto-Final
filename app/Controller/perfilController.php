<?php
session_start();
include('../Models/conexion.php');
include('../Models/login.php');

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
        header('Location: ../../index.php'); // Redirigir al usuario al index después de la actualización exitosa
        exit(); // Asegurarse de que el script se detiene después de la redirección
    } else {
        echo "<p class='alert alert-danger'>" . $result['message'] . "</p>";
    }
}
?>