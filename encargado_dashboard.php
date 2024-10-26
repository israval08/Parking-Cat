<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario ha iniciado sesión y si es un encargado
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'encargado') {
    // Redirigir si no es un usuario tipo encargado
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
include 'config.php';
include 'navbar.php'; // Incluye la barra de navegación

// Consulta para obtener la lista de usuarios activos
$sql = "SELECT rut, nombre_completo, tipo_usuario, correo_electronico FROM usuarios WHERE estado = 'activo'";
$result = $conn->query($sql);

?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Encargado</title>
    <link rel="stylesheet" href="dashboard.css"> <!-- Asegúrate de tener un CSS para estilos -->
</head>
<body>
    <div class="contenedor">
        <h1>Bienvenido, <?php echo $_SESSION['nombre_completo']; ?> (Encargado)</h1>
        
        <!-- Mensaje de construcción -->
        <div class="mensaje-construccion">
            <p>Este dashboard está en construcción. Próximamente verás más información relevante.</p>
        </div>

        <!-- Tabla para mostrar usuarios activos -->
        <h2>Usuarios Activos</h2>
        <?php
        if ($result->num_rows > 0) {
            echo "<table border='1'>";
            echo "<tr><th>RUT</th><th>Nombre Completo</th><th>Tipo de Usuario</th><th>Correo Electrónico</th></tr>";
            while ($row = $result->fetch_assoc()) {
                echo "<tr>";
                echo "<td>{$row['rut']}</td>";
                echo "<td>{$row['nombre_completo']}</td>";
                echo "<td>{$row['tipo_usuario']}</td>";
                echo "<td>{$row['correo_electronico']}</td>";
                echo "</tr>";
            }
            echo "</table>";
        } else {
            echo "<p>No hay usuarios activos en este momento.</p>";
        }
        ?>

        <div class="logout">
            <button class="btn" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>
    </div>

    <?php
    // Cerrar la conexión a la base de datos
    if (isset($conn)) {
        $conn->close();
    }
    ?>
</body>
</html>
