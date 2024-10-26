<?php
include 'config.php'; // Conexión a la base de datos

if (isset($_POST['rut'])) {
    $rut = $_POST['rut'];

    // Consulta para obtener los datos del usuario
    $sql = "SELECT rut, nombre_completo, correo_electronico, tipo_usuario FROM usuarios WHERE rut = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $rut);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        // Devolver los datos del usuario en formato JSON
        echo json_encode($usuario);
    } else {
        echo json_encode(['error' => 'Usuario no encontrado']);
    }

    $stmt->close();
} else {
    echo json_encode(['error' => 'RUT no recibido']);
}

$conn->close();
?>
