<?php
// Iniciar la sesión
session_start();

// Incluir el archivo de conexión a la base de datos
include('app/Models/conexion.php'); // Asegúrate de crear este archivo y definir la conexión
include('app/Models/login.php'); // Incluir la clase Login
include('app/Models/consumo.php');

// Crear una instancia de la clase Login
$con = new conexion(); 
$login = new Login($con->conectar());

// Manejo del formulario de inicio de sesión
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['login'])) {
    $mail = $_POST['mail'];
    $password = $_POST['password'];
    
    $result = $login->login($mail, $password);
    
    // Si el inicio de sesión es exitoso, guardar la información del usuario en la sesión
    if ($result['status'] === 'success') {
        $_SESSION['user_id'] = $result['user_id'];
        $_SESSION['tipo_usuario'] = $result['tipo_usuario'];
        $_SESSION['cedula'] = $login->getUserById($result['user_id'])['cedula'];
    }
}

// Manejo del cierre de sesión
if (isset($_GET['action']) && $_GET['action'] === 'logout') {
    session_destroy(); // Destruir la sesión
    header("Location: " . $_SERVER['PHP_SELF']); // Redirigir a la página principal
    exit();
}

// Manejo del formulario de registro
$result_register = null; // Inicializar la variable de resultado del registro
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['register'])) {
    $cedula = $_POST['cedula'];
    $nit = $_POST['nit'];
    $primer_nombre = $_POST['primer_nombre'];
    $segundo_nombre = $_POST['segundo_nombre'];
    $primer_apellido = $_POST['primer_apellido'];
    $segundo_apellido = $_POST['segundo_apellido'];
    $mail = $_POST['mail'];
    $password = $_POST['password'];

    // Guardar el resultado del registro
    $result_register = $login->create($nit, $cedula, $primer_nombre, $segundo_nombre, $primer_apellido, $segundo_apellido, $mail, $password);
}
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['consumo'])) {
    $cedula = $_POST['cedula']; // Cédula del dueño
    $consumo_value = $_POST['consumo']; // Convertir a entero
    $fecha = $_POST['fecha']; // Fecha del consumo
    $tipo_consumo = $_POST['tipo_consumo']; // Tipo de consumo
    $hora = $_POST['hora']; // Hora del consumo (nuevo campo)

    // Crear una instancia de la clase Consumo
    $consumo = new Consumo($con->conectar());

    // Llamar al método para registrar el consumo y obtener el resultado
    $result_consumo = $consumo->create($cedula, $consumo_value, $fecha, $tipo_consumo, $hora); // Añadir hora al método create

    // Mostrar el mensaje resultante
    echo "<p class='alert " . ($result_consumo['status'] === 'success' ? 'alert-success' : 'alert-danger') . "'>" . $result_consumo['message'] . "</p>";
}



?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css" rel="stylesheet">
</head>
<body>  
<?php
    // Verificar si no hay parámetros en la URL (sin GET ni POST)
    if (empty($_GET) && empty($_POST)) {
        echo "
        <script>
            window.onload = function() {
                // Simular clic en el botón solo cuando no hay parámetros en la URL
                document.getElementById('inicio').click();
            };
        </script>
        ";
    }
    ?>
    
<nav class="navbar navbar-expand-lg navbar-yellow">
    <div class="container">
        <a class="navbar-brand" href="index.php">
            <img src="public/img/Logo.png" alt="Logo" width="100" height="100" class="d-inline-block align-text-top img-fluid">
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav ms-auto">
                <li class="nav-item">
                    <form action="" method="GET">
                        <button type="submit" id="inicio" name="action" value="inicio" class="nav-link btn btn-link">Inicio</button>
                    </form>
                </li>
                <li class="nav-item">
                    <form action="" method="GET">
                        <button type="submit" name="action" value="foro" class="nav-link btn btn-link">Foro</button>
                    </form>
                </li>
                <li class="nav-item">
                    <form action="" method="GET">
                        <button type="submit" name="action" value="acerca" class="nav-link btn btn-link">Acerca de Nosotros</button>
                    </form>
                </li>
            </ul>
            <!-- Si el usuario está conectado, mostrar el botón de cerrar sesión -->
            <?php if (isset($_SESSION['user_id'])): echo '<a href="app/View/perfilView.php" class="btn btn-warning ms-3">Perfil</a>' ?>
                    
                <!-- Si el usuario es admin, mostrar el botón de administrador -->
                <?php if ($_SESSION['tipo_usuario'] === 'administrador'): ?>
                    <a href="app/View/admin.php" class="btn btn-warning ms-3">Admin</a>
                <?php endif; ?>
                <a href="?action=logout" class="btn btn-danger ms-3">Cerrar Sesión</a>
                <a href="#" class="btn btn-secondary ms-3" data-bs-toggle="modal" data-bs-target="#consumoModal">Registrar Consumo</a>
                <a href="app/View/ConsumosView.php" class="btn btn-info ms-3">Administrar consumos</a>
            <?php else: ?>
                <a href="#" class="btn btn-primary ms-3" data-bs-toggle="modal" data-bs-target="#loginModal">Login</a>
                <a href="#" class="btn btn-success ms-3" data-bs-toggle="modal" data-bs-target="#registerModal">Registrar</a>
            <?php endif; ?>
        </div>
    </div>
</nav>


    <div class="content">
        <?php
            // Mostrar mensajes de inicio de sesión
            if (isset($result)) {
                echo "<p class='alert " . ($result['status'] === 'success' ? 'alert-success' : 'alert-danger') . "'>" . $result['message'] . "</p>";
                $result = null; // Resetear la variable para evitar que se muestre de nuevo al recargar
            }
            // Mostrar mensajes de registro
            if (isset($result_register)) {
                echo "<p class='alert " . ($result_register['status'] === 'success' ? 'alert-success' : 'alert-danger') . "'>" . $result_register['message'] . "</p>";
                $result_register = null; // Resetear la variable para evitar que se muestre de nuevo al recargar
            }

            if (isset($_GET['action'])) {
                $action = $_GET['action'];
                if ($action == 'inicio') {
                    require("app/View/inicio.php");
                    inicio();
                } elseif ($action == 'foro') {
                    echo "<p>Has hecho clic en Foro</p>";
                } elseif ($action == 'acerca') {
                    echo "<p>Has hecho clic en Acerca de Nosotros</p>";
                
                } else {
                    echo "<p>Acción desconocida</p>";
                }
            }
        ?>
    </div>

    <!-- Modal de Inicio de Sesión -->
    <div class="modal fade" id="loginModal" tabindex="-1" aria-labelledby="loginModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginModalLabel">Iniciar Sesión</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="loginForm" method="POST" action="">
                        <div class="mb-3">
                            <label for="mail" class="form-label">Correo Electrónico</label>
                            <input type="email" name="mail" class="form-control" id="mail" placeholder="Ingresa tu correo electrónico" required>
                        </div>
                        <div class="mb-3">
                            <label for="password" class="form-label">Contraseña</label>
                            <input type="password" name="password" class="form-control" id="password" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <button type="submit" name="login" class="btn btn-primary w-100">Iniciar Sesión</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de Registro -->
    <div class="modal fade" id="registerModal" tabindex="-1" aria-labelledby="registerModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="registerModalLabel">Registrar</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form id="registerForm" method="POST" action="">
                        <div class="mb-3">
                            <label for="cedula" class="form-label">Cédula <span style="color: red;">*</span></label>
                            <input type="text" name="cedula" class="form-control" id="cedula" placeholder="Ingresa tu cédula" required>
                        </div>
                        <div class="mb-3">
                            <label for="nit" class="form-label">nit <span style="color: red;">*</span></label>
                            <input type="text" name="nit" class="form-control" id="nit" placeholder="Ingresa tu nit" required>
                        </div>
                        <div class="mb-3">
                            <label for="primer_nombre" class="form-label">Primer Nombre <span style="color: red;">*</span></label>
                            <input type="text" name="primer_nombre" class="form-control" id="primer_nombre" placeholder="Ingresa tu primer nombre" required>
                        </div>
                        <div class="mb-3">
                            <label for="segundo_nombre" class="form-label">Segundo Nombre</label>
                            <input type="text" name="segundo_nombre" class="form-control" id="segundo_nombre" placeholder="Ingresa tu segundo nombre">
                        </div>
                        <div class="mb-3">
                            <label for="primer_apellido" class="form-label">Primer Apellido <span style="color: red;">*</span></label>
                            <input type="text" name="primer_apellido" class="form-control" id="primer_apellido" placeholder="Ingresa tu primer apellido" required>
                        </div>
                        <div class="mb-3">
                            <label for="segundo_apellido" class="form-label">Segundo Apellido</label>
                            <input type="text" name="segundo_apellido" class="form-control" id="segundo_apellido" placeholder="Ingresa tu segundo apellido">
                        </div>
                        <div class="mb-3">
                            <label for="register_mail" class="form-label">Correo Electrónico <span style="color: red;">*</span></label>
                            <input type="email" name="mail" class="form-control" id="register_mail" placeholder="Ingresa tu correo electrónico" required>
                        </div>
                        <div class="mb-3">
                            <label for="register_password" class="form-label">Contraseña <span style="color: red;">*</span></label>
                            <input type="password" name="password" class="form-control" id="register_password" placeholder="Ingresa tu contraseña" required>
                        </div>
                        <button type="submit" name="register" class="btn btn-success w-100">Registrar</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <div class="modal fade" id="consumoModal" tabindex="-1" aria-labelledby="consumoModalLabel" aria-hidden="true">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="consumoModalLabel">Registrar Consumo</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <form id="consumo" method="POST" action="">
                    <div class="mb-3">
                        <label for="cedula" class="form-label">id</label>
                        <input type="text" name="cedula" class="form-control" id="cedula" value="<?php echo $_SESSION['user_id'] ?>" required readonly>
                    </div>
                    <div class="mb-3">
                        <label for="consumo" class="form-label">Consumo (kWh)  <span style="color: red;">*</span></label>
                        <input type="number" name= "consumo" class="form-control" id="consumo" placeholder="Ingresa cuanta electricidad consumiste en (kWh)" required>
                    </div>
                    <div class="mb-3">
                        <label for="hora" class="form-label">Horas  <span style="color: red;">*</span></label>
                        <input type="number" name= "hora" class="form-control" id="hora" placeholder="Ingresa cuantas horas consumiste" required>
                    </div>
                    <div class="mb-3">
                        <label for="fecha" class="form-label">Fecha  <span style="color: red;">*</span> </label>
                        <input type="date" name="fecha" class="form-control" id="fecha_nacimiento" required>
        </div>
        <div class="mb-3">
            <label for="tipo_consumo" class="form-label">Tipo de Consumo  <span style="color: red;">*</span> </label>
            <select name="tipo_consumo" class="form-select" id="tipo_consumo" required>
                <option value="" disabled selected>Selecciona el tipo de consumo</option>
                <option value="bajo">Bajo</option>
                <option value="medio">Medio</option>
                <option value="alto">Alto</option>
            </select>
        </div>
                    <button type="submit" name="submit_mascota" class="btn btn-success w-100">Registrar Consumo</button>
                </form>
            </div>
        </div>
    </div>
</div>
<footer class="footer" >
        <div class="container">
            <div class="row">
                <div class="col-md-3 col-sm-12 mb-3">
                    <h5>Nosotros</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">¿Quiénes somos?</a></li>
                        <li><a href="#">Nuestros aliados</a></li>
                        <li><a href="#">Trabaja con nosotros</a></li>
                        <li><a href="#">Política de seguridad y salud en el trabajo</a></li>
                        <li><a href="#">Responsabilidad social</a></li>
                        <li><a href="#">Nuestras acciones</a></li>
                        <li><a href="#">Donaciones</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-12 mb-3">
                    <h5>Importante</h5>
                    <ul class="list-unstyled">
                        <li><a href="#">Términos y condiciones</a></li>
                        <li><a href="#">Preguntas frecuentes</a></li>
                        <li><a href="#">Todo acerca de tus compras</a></li>
                        <li><a href="#">Política de privacidad y manejo de datos personales</a></li>
                        <li><a href="#">Métodos y políticas de envío</a></li>
                        <li><a href="#">Métodos de pago aceptados</a></li>
                        <li><a href="#">Políticas de devoluciones y garantías y derecho de retracto</a></li>
                        <li><a href="#">Términos y condiciones de promociones y eventos</a></li>
                        <li><a href="#">Manual SAGRILAF</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-12 mb-3">
                    <h5>Contáctenos</h5>
                    <ul class="list-unstyled">
                        <li>Ventas: +57 311 1111111</li>
                        <li>Atención al cliente: PBX 600 0000 ext. 9000, 9000, 9000</li>
                        <li><a href="#">Formulario de contacto</a></li>
                        <li><a href="#">PQR'S</a></li>
                    </ul>
                </div>
                <div class="col-md-3 col-sm-12 mb-3">
                    <h5>Síguenos</h5>
                    <div class="social-icons">
                        <a href="https://facebook.com"><i class="fab fa-facebook-f"></i></a>
                        <a href="https://instagram.com"><i class="fab fa-instagram"></i></a>
                        <a href="https://twitter.com"><i class="fab fa-twitter"></i></a>
                    </div>
                    <button class="btn btn-outline-light w-100 mt-3">Suscríbete al Newsletter</button>
                </div>
            </div>
        </div>
    </footer>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.11.7/dist/umd/popper.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/js/bootstrap.min.js"></script>
</body>
</html>
