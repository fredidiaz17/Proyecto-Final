<?php
session_start();
include('app/Models/conexion.php');
include('app/Models/consumo.php'); 
include('app/Models/login.php');

$con = new conexion();
$consumo = new Consumo($con->conectar()); 
$usuario = new Login($con->conectar());

// Verificar si el usuario es administrador
if (isset($_SESSION['tipo_usuario']) && $_SESSION['tipo_usuario'] === 'administrador') {
    // Si es administrador, obtener todos los consumos
    $consumos = $consumo->list();
} else {
    // Si no es administrador, obtener solo los consumos asociados a su cédula
    $consumos = $consumo->list($_SESSION['cedula']);
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

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Administrar Consumos</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body>
    <div class="container mt-5">
        <h2>Administrar Consumos</h2>
        <table class="table table-bordered">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Cédula</th>
                    <th>Consumo</th>
                    <th>Fecha</th>
                    <th>Tipo de Consumo</th>
                    <th>Horas</th>
                    <th>Costo Total</th> <!-- Nueva columna -->
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody>
                <?php if (!empty($consumos)): ?>
                    <?php foreach ($consumos as $consumo): ?>
                        <?php 
                        $costo_total = $consumo['Consumo'] * $consumo['Horas'] * 0.5; // Calcular el costo total
                        ?>
                        <tr>
                            <td><?php echo htmlspecialchars($consumo['id']); ?></td>
                            <td><?php echo htmlspecialchars($consumo['cedula']); ?></td>
                            <td><?php echo htmlspecialchars($consumo['Consumo']); ?></td>
                            <td><?php echo htmlspecialchars($consumo['fecha']); ?></td>
                            <td><?php echo htmlspecialchars($consumo['tipo_consumo']); ?></td>
                            <td><?php echo htmlspecialchars($consumo['Horas']); ?></td>
                            <td><?php echo '$' . number_format($costo_total, 2); ?></td> <!-- Mostrar costo total -->
                            <td>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="consumo_id" value="<?php echo htmlspecialchars($consumo['id']); ?>">
                                    <button type="submit" name="delete_consumo" class="btn btn-danger btn-sm" onclick="return confirm('¿Estás seguro de que quieres eliminar este consumo?');">Eliminar</button>
                                </form>
                                <form method="POST" action="" style="display:inline;">
                                    <input type="hidden" name="consumo_id" value="<?php echo htmlspecialchars($consumo['id']); ?>">
                                    <button type="submit" name="edit" class="btn btn-warning btn-sm">Editar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php else: ?>
                    <tr>
                        <td colspan="8" class="text-center">No hay consumos disponibles.</td>
                    </tr>
                <?php endif; ?>
            </tbody>
        </table>
        <a href="index.php" class="btn btn-warning ms-3">Home</a>
        
        <?php if ($consumo_edit): ?>
            <?php if (!empty($message)): ?>
                <div><?php echo $message; ?></div>
            <?php endif; ?>

            <h3>Editar Consumo</h3>
            <form method="POST" action="">
                <input type="hidden" name="consumo_id" value="<?php echo htmlspecialchars($consumo_edit['id']); ?>">
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula</label>
                    <input type="text" name="cedula" class="form-control" value="<?php echo htmlspecialchars($consumo_edit['cedula']); ?>" required readonly>
                </div>
                <div class="mb-3">
                    <label for="consumo" class="form-label">Consumo</label>
                    <input type="number" name="consumo" class="form-control" value="<?php echo htmlspecialchars($consumo_edit['Consumo']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha</label>
                    <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($consumo_edit['fecha']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_consumo" class="form-label">Tipo de Consumo</label>
                    <select name="tipo_consumo" class="form-select" id="tipo_consumo" required>
                        <option value="bajo" <?php echo (isset($consumo_edit['tipo_consumo']) && $consumo_edit['tipo_consumo'] === 'bajo') ? 'selected' : ''; ?>>Bajo</option>
                        <option value="medio" <?php echo (isset($consumo_edit['tipo_consumo']) && $consumo_edit['tipo_consumo'] === 'medio') ? 'selected' : ''; ?>>Medio</option>
                        <option value="alto" <?php echo (isset($consumo_edit['tipo_consumo']) && $consumo_edit['tipo_consumo'] === 'alto') ? 'selected' : ''; ?>>Alto</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="horas" class="form-label">Horas</label>
                    <input type="number" name="horas" class="form-control" value="<?php echo htmlspecialchars($consumo_edit['Horas']); ?>" required>
                </div>
                <button type="submit" name="edit_consumo" class="btn btn-primary">Guardar Cambios</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
