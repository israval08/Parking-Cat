<?php
session_start();
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'administrador') {
    echo "Acceso denegado.";
    exit();
}

// Conexión a la base de datos
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = $_POST['rut'];

    // Borrar el usuario
    $sql = "DELETE FROM usuarios WHERE rut = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $rut);

    if ($stmt->execute()) {
        echo "Usuario borrado con éxito.";
    } else {
        echo "Error al borrar usuario: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
