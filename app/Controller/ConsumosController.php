<?php
session_start();
include('../Models/conexion.php');
include('../Models/consumo.php'); 
include('../Models/login.php');

$con = new conexion();
$consumo = new Consumo($con->conectar()); 
$usuario = new Login($con->conectar());

// Verificar si el usuario es administrador
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] == 'administrador') {
    // Si es administrador, obtener todos los consumos
    $consumos = $consumo->list();
} else {
    // Si no es administrador, obtener solo los consumos asociados a su cédula
    $cedula_usuario = $_SESSION['cedula']; // Asegúrate de que la cédula del usuario esté almacenada en la sesión
    $testo = $consumo->list();
    
    // Filtrar los consumos por la cédula del usuario
    $testo = array_filter($testo, function($item) use ($cedula_usuario) {
        return $item['cedula'] == $cedula_usuario;
    });
    
    // Reindexar el array para eliminar posibles agujeros en los índices
    $testo = array_values($testo);
    
    // Imprimir el resultado para verificar
    $consumos = $testo;
}

// Manejo de la eliminación de consumo
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['delete_consumo'])) {
    $consumo_id = $_POST['consumo_id'];
    $result = $consumo->delete($consumo_id);
    if ($result['status'] === 'success') {
        echo "<p class='alert alert-success'>" . $result['message'] . "</p>";
    } else {
        echo "<p class='alert alert-danger'>" . $result['message'] . "</p>";
    }
}

// Manejo del formulario de edición de consumo
$message = "";
$consumo_edit = null;

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['edit_consumo'])) {
        $consumo_id = $_POST['consumo_id'];
        $cedula = $_POST['cedula'];
        $consumo_value = (float) $_POST['consumo'];
        $fecha = $_POST['fecha'];
        $tipo_consumo = $_POST['tipo_consumo'];
        $horas = (int) $_POST['horas'];

        // Actualizar el consumo
        $result = $consumo->update($consumo_id, $cedula, $fecha, $consumo_value, $tipo_consumo, $horas);
        $consumo->actualizarTipoConsumo($consumo_id, $_POST['tipo_consumo']);
        header("Refresh:0"); // Esta línea recarga la página
        exit();
    } elseif (isset($_POST['edit'])) {
        $consumo_id = $_POST['consumo_id'];
        $consumo_edit = $consumo->getConsumoById($consumo_id);
    }
}
?>