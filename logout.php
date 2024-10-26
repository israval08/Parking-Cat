<?php
session_start();
include 'config.php'; // Asegúrate de incluir el archivo de configuración para conectar con la base de datos

// Verificar si el usuario ha iniciado sesión y es un parqueador
if (isset($_SESSION['rut']) && $_SESSION['tipo_usuario'] == 'parqueador') {
    $rut_parqueador = $_SESSION['rut'];

    // Actualizar fecha_fin en la tabla jornadas para finalizar la jornada actual
    $sql_jornada = "UPDATE jornadas SET fecha_fin = NOW() WHERE rut_parqueador = ? AND fecha_fin IS NULL";
    $stmt_jornada = $conn->prepare($sql_jornada);
    $stmt_jornada->bind_param("s", $rut_parqueador);
    if (!$stmt_jornada->execute()) {
        error_log("Error al actualizar fecha_fin en jornadas: " . $stmt_jornada->error);
    }
    $stmt_jornada->close();

    // Restablecer las coordenadas del parqueador a NULL
    $sql_coordenadas = "UPDATE usuarios SET latitude = NULL, longitude = NULL WHERE rut = ?";
    $stmt_coordenadas = $conn->prepare($sql_coordenadas);
    $stmt_coordenadas->bind_param("s", $rut_parqueador);
    if (!$stmt_coordenadas->execute()) {
        error_log("Error al restablecer coordenadas del parqueador: " . $stmt_coordenadas->error);
    }
    $stmt_coordenadas->close();
}

// Destruir la sesión
session_unset(); // Elimina todas las variables de sesión
session_destroy(); // Destruye la sesión

// Cerrar la conexión a la base de datos
if (isset($conn)) {
    $conn->close();
}

// Redirigir al usuario a la página de inicio de sesión
header("Location: login.php");
exit();
?>
