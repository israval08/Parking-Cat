<?php
include 'config.php'; // Conexión a la base de datos

if (isset($_POST['rut'])) {
    // Recibir el RUT enviado por AJAX
    $rut = $_POST['rut'];

    // Verificar que el RUT no esté vacío
    if (!empty($rut)) {
        // Preparar la consulta para borrar el usuario
        $sql = "DELETE FROM usuarios WHERE rut = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $rut);

        if ($stmt->execute()) {
            echo "Usuario eliminado correctamente.";
        } else {
            echo "Error al eliminar el usuario: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: El RUT no puede estar vacío.";
    }
} else {
    echo "Error: Parámetro 'RUT' no recibido.";
}

$conn->close();
?>
