<?php
include "../Controller/perfilController.php"
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
                <label for="cedula" class="form-label">Cédula  <span style="color: red;">*</span> </label>
                <input type="text" name="cedula" class="form-control" value="<?php echo $user['cedula']; ?>" required readonly>
            </div>
            <div class="mb-3">
                <label for="primer_nombre" class="form-label">Primer Nombre  <span style="color: red;">*</span> </label>
                <input type="text" name="primer_nombre" class="form-control" value="<?php echo $user['primer_nombre']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                <input type="text" name="segundo_nombre" class="form-control" value="<?php echo $user['segundo_nombre']; ?>">
            </div>
            <div class="mb-3">
                <label for="primer_apellido" class="form-label">Primer Apellido  <span style="color: red;">*</span> </label>
                <input type="text" name="primer_apellido" class="form-control" value="<?php echo $user['primer_apellido']; ?>" required>
            </div>
            <div class="mb-3">
                <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                <input type="text" name="segundo_apellido" class="form-control" value="<?php echo $user['segundo_apellido']; ?>">
            </div>
            <div class="mb-3">
                <label for="mail" class="form-label">Correo Electrónico  <span style="color: red;">*</span> </label>
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