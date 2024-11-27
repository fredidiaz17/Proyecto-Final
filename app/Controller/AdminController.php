<?php
session_start();
include('../Models/conexion.php');
include('../Models/login.php');

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

