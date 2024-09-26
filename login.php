<?php
// Iniciar sesión
session_start();

// Conexión a la base de datos
include 'config.php'; // Aquí debes configurar tu conexión a la base de datos
include 'navbar.php';
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Obtener los datos del formulario
    $rut = $_POST['rut']; // El RUT debe ser ingresado con puntos y guiones
    $password = $_POST['password'];

    // Consulta para buscar al usuario por su RUT
    $sql = "SELECT * FROM usuarios WHERE rut = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $result = $stmt->get_result();

    // Verificar si se encontró un usuario
    if ($result->num_rows == 1) {
        $usuario = $result->fetch_assoc();
        
        // Verificar la contraseña
        if (password_verify($password, $usuario['contrasena'])) {
            // Si la contraseña es correcta, iniciar la sesión
            $_SESSION['rut'] = $usuario['rut'];
            $_SESSION['nombre_completo'] = $usuario['nombre_completo'];
            $_SESSION['tipo_usuario'] = $usuario['tipo_usuario'];

            // Redirigir al administrador a la página de trabajo
            if ($usuario['tipo_usuario'] == 'administrador') {
                header("Location: admin_dashboard.php");
                exit();
            }
        } else {
            echo "Contraseña incorrecta";
        }
    } else {
        echo "No se encontró un usuario con ese RUT";
    }
}
?>

<!-- Formulario de login -->
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="./Estilos/login.css"> <!-- Enlace al archivo CSS -->
   
</head>
<body>
  <!-- Incluir el archivo navbar.php -->

    
  <div class="container3">
        <form class="login-form" action="" method="POST" onsubmit="formatRut()">
            <h2>Iniciar sesión</h2>
            <div class="form-group">
                <input type="text" name="rut" id="rut" class="form-control" placeholder="RUT" required>
            </div>
            <div class="form-group">
                <input type="password" name="password" id="password" class="form-control" placeholder="Contraseña" required>
            </div>
            <input type="submit" value="Iniciar sesión" class="btn">
        </form>
    </div>
 

    <script>
        function formatRut() {
            let rutField = document.getElementById('rut');
            let rut = rutField.value.replace(/\./g, '').replace(/-/g, ''); // Eliminar puntos y guión
            if (rut.length > 1) {
                // Agregar puntos y guión
                rut = rut.slice(0, -1).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + rut.slice(-1);
            }
            rutField.value = rut; // Asignar el valor formateado al campo RUT
        }

        document.getElementById('rut').addEventListener('keydown', function(event) {
            if (event.key === 'Enter' || event.key === 'Tab') {
                event.preventDefault(); // Evitar que se envíe el formulario o se haga tabulación
                formatRut();
                if (event.key === 'Enter') {
                    document.querySelector('form').submit(); // Enviar el formulario al presionar Enter
                }
            }
        });

        document.querySelector('form').addEventListener('submit', function(event) {
            formatRut(); // Asegurar que el RUT esté formateado al enviar el formulario
        });
    </script>
</body>
</html>