<?php
include "../Controller/ConsumosController.php";
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
                        if($consumo['tipo_consumo'] == 'bajo' ){
                            $costo_total = $costo_total * 0.7;
                        }elseif($consumo['tipo_consumo'] == 'medio'){
                            $costo_total = $costo_total * 0.9;
                        }
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
        <a href="../../index.php" class="btn btn-warning ms-3">Home</a>
        <form method="POST" action="">
            <button type="submit" name="Cambiargrafico" class="btn btn-primary">Extra grafica</button>
        </form>
        <?php if ($consumo_edit): ?>
            <?php if (!empty($message)): ?>
                <div><?php echo $message; ?></div>
            <?php endif; ?>

            <h3>Editar Consumo</h3>
            <form method="POST" action="">
                <input type="hidden" name="consumo_id" value="<?php echo htmlspecialchars($consumo_edit['id']); ?>">
                <div class="mb-3">
                    <label for="cedula" class="form-label">Cédula  <span style="color: red;">*</span></label>
                    <input type="text" name="cedula" class="form-control" value="<?php echo htmlspecialchars($consumo_edit['cedula']); ?>" required readonly>
                </div>
                <div class="mb-3">
                    <label for="consumo" class="form-label">Consumo  <span style="color: red;">*</span> </label>
                    <input type="number" name="consumo" class="form-control" value="<?php echo htmlspecialchars($consumo_edit['Consumo']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="fecha" class="form-label">Fecha  <span style="color: red;">*</span> </label>
                    <input type="date" name="fecha" class="form-control" value="<?php echo htmlspecialchars($consumo_edit['fecha']); ?>" required>
                </div>
                <div class="mb-3">
                    <label for="tipo_consumo" class="form-label">Tipo de Consumo  <span style="color: red;">*</span> </label>
                    <select name="tipo_consumo" class="form-select" id="tipo_consumo" required>
                        <option value="bajo" <?php echo (isset($consumo_edit['tipo_consumo']) && $consumo_edit['tipo_consumo'] === 'bajo') ? 'selected' : ''; ?>>Bajo</option>
                        <option value="medio" <?php echo (isset($consumo_edit['tipo_consumo']) && $consumo_edit['tipo_consumo'] === 'medio') ? 'selected' : ''; ?>>Medio</option>
                        <option value="alto" <?php echo (isset($consumo_edit['tipo_consumo']) && $consumo_edit['tipo_consumo'] === 'alto') ? 'selected' : ''; ?>>Alto</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label for="horas" class="form-label">Horas  <span style="color: red;">*</span> </label>
                    <input type="number" name="horas" class="form-control" value="<?php echo htmlspecialchars($consumo_edit['Horas']); ?>" required>
                </div>
                <button type="submit" name="edit_consumo" class="btn btn-primary">Guardar Cambios</button>
            </form>
        <?php endif; ?>
    </div>
</body>
</html>
<?php
// Verificar si hay consumos disponibles
if (!empty($consumos) && is_array($consumos)) {
    foreach ($consumos as $consumo) {
        // Asegurarse de que los índices existen en el array
        if (isset($consumo['Consumo'], $consumo['Horas'], $consumo['tipo_consumo'], $consumo['fecha'])) {
            $costo_total = $consumo['Consumo'] * $consumo['Horas'] * 0.5; // Cálculo básico del costo

            // Aplicar descuento según el tipo de consumo
            if ($consumo['tipo_consumo'] == 'bajo') {
                $costo_total *= 0.7; // 30% de descuento si es "bajo"
            } elseif ($consumo['tipo_consumo'] == 'medio') {
                $costo_total *= 0.9; // 10% de descuento si es "medio"
            }

            // Almacenar los datos
            $fechas[] = $consumo['fecha'];
            $costos[] = $costo_total; // Guardamos el costo total calculado
            $horas[] = $consumo['Horas']; // Guardamos las horas
        }
    }

    // Convertir a JSON solo si hay datos
    if (!empty($fechas) && !empty($costos) && !empty($horas)) {
        $fechas = json_encode($fechas);
        $costos = json_encode($costos);
        $horas = json_encode($horas);
        
        echo "<p>";
        echo "<p><p><p><p><p>";
        require("./Graficas.php");
        $grafica = false;
        Cambiargrafica($fechas, $costos, $horas, $grafica);
        echo "<p><p><p><p><p>";
        
        if (isset($_POST['Cambiargrafico'])) {
            // Mostrar mensaje cuando se presione el botón
            $grafica = !$grafica; // Alternar el valor de $grafica
            Cambiargrafica($fechas, $costos, $horas, $grafica);
        }
    } else {
        // Mensaje centrado
        echo "<div style='text-align: center;'><p>No hay datos suficientes para mostrar la gráfica.</p></div>";
    }
} else {
    // Mensaje centrado
    echo "<div style='text-align: center;'><p>No se encontraron consumos.</p></div>";
}

?>


