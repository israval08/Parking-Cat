<?php
include 'config.php'; // Conexión a la base de datos

if (isset($_POST['rut']) && isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['tipo_usuario'])) {
    $rut = $_POST['rut'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $tipo_usuario = $_POST['tipo_usuario'];

    // Validar que los datos no estén vacíos
    if (!empty($rut) && !empty($nombre) && !empty($email) && !empty($tipo_usuario)) {
        // Preparar la consulta para actualizar el usuario
        $sql = "UPDATE usuarios SET nombre_completo = ?, correo_electronico = ?, tipo_usuario = ? WHERE rut = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssss", $nombre, $email, $tipo_usuario, $rut);

        if ($stmt->execute()) {
            echo "Usuario actualizado correctamente.";
        } else {
            echo "Error al actualizar el usuario: " . $stmt->error;
        }

        $stmt->close();
    } else {
        echo "Error: Todos los campos son obligatorios.";
    }
} else {
    echo "Error: Parámetros no recibidos.";
}

$conn->close();
?>
