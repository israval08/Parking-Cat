<?php
session_start();
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'administrador') {
    echo "Acceso denegado.";
    exit();
}

// Conexión a la base de datos
include 'config.php';

try {
    if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['rut'])) {
        $rut = $_GET['rut'];

        // Consulta para obtener los datos del usuario
        $sql = "SELECT * FROM usuarios WHERE rut = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $rut);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();

            // Mostrar el formulario prellenado con los datos del usuario
            echo '<form method="POST" action="procesar_actualizar_usuario.php">';
            echo '<input type="hidden" name="rut" value="' . $row['rut'] . '">';
            echo 'Nombre completo: <input type="text" name="nombre_completo" value="' . $row['nombre_completo'] . '"><br>';
            echo 'Correo electrónico: <input type="email" name="correo_electronico" value="' . $row['correo_electronico'] . '"><br>';
            echo 'Tipo de usuario: <select name="tipo_usuario">';
            echo '<option value="parqueador" ' . ($row['tipo_usuario'] == 'parqueador' ? 'selected' : '') . '>Parqueador</option>';
            echo '<option value="encargado" ' . ($row['tipo_usuario'] == 'encargado' ? 'selected' : '') . '>Encargado</option>';
            echo '<option value="administrador" ' . ($row['tipo_usuario'] == 'administrador' ? 'selected' : '') . '>Administrador</option>';
            echo '</select><br>';
            echo 'Estado: <select name="estado">';
            echo '<option value="activo" ' . ($row['estado'] == 'activo' ? 'selected' : '') . '>Activo</option>';
            echo '<option value="inactivo" ' . ($row['estado'] == 'inactivo' ? 'selected' : '') . '>Inactivo</option>';
            echo '</select><br>';
            echo '<input type="submit" value="Actualizar">';
            echo '</form>';
        } else {
            echo "Error: Usuario no encontrado.";
        }

        $stmt->close();
    } 
} catch (Exception $e) {
    echo "Error al cargar los datos del usuario: " . $e->getMessage();
}

$conn->close();
?>