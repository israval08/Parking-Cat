<?php
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];
    $rut_parqueador = $_POST['rut_parqueador'];

    if (!empty($latitude) && !empty($longitude) && !empty($rut_parqueador)) {
        $sql = "UPDATE usuarios SET latitude = ?, longitude = ? WHERE rut = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('dds', $latitude, $longitude, $rut_parqueador);

        if ($stmt->execute()) {
            echo "Ubicación actualizada correctamente.";
        } else {
            echo "Error al actualizar la ubicación: " . $stmt->error;
        }
    } else {
        echo "Datos incompletos.";
    }
}

$conn->close();
?>
