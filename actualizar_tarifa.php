<?php
include 'config.php'; // Conexión a la base de datos

if (isset($_POST['tarifa_minuto']) && isset($_POST['pago_minimo']) && isset($_POST['pago_maximo']) && isset($_POST['tarifa_maxima_diaria']) && isset($_POST['fecha_inicio']) && isset($_POST['fecha_fin'])) {
    $tarifa_minuto = $_POST['tarifa_minuto'];
    $pago_minimo = $_POST['pago_minimo'];
    $pago_maximo = $_POST['pago_maximo'];
    $tarifa_maxima_diaria = $_POST['tarifa_maxima_diaria'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = $_POST['fecha_fin'];

    // Actualizar la última tarifa
    $sql = "UPDATE tarifas SET tarifa_minuto = ?, pago_minimo = ?, pago_maximo = ?, tarifa_maxima_diaria = ?, fecha_inicio = ?, fecha_fin = ? ORDER BY id DESC LIMIT 1";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiiiss', $tarifa_minuto, $pago_minimo, $pago_maximo, $tarifa_maxima_diaria, $fecha_inicio, $fecha_fin);

    if ($stmt->execute()) {
        echo "Tarifa actualizada correctamente.";
    } else {
        echo "Error al actualizar la tarifa: " . $stmt->error;
    }

    $stmt->close();
} else {
    echo "Error: Parámetros no recibidos.";
}

$conn->close();
?>
