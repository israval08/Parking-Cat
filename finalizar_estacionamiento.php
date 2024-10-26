<?php
include 'config.php';

if (isset($_POST['id_estacionamiento'])) {
    $id_estacionamiento = $_POST['id_estacionamiento'];

    // Buscar el estacionamiento correspondiente y calcular el monto total
    $sql = "SELECT estacionamientos.hora_entrada, tarifas.tarifa_minuto, tarifas.tarifa_maxima_diaria 
            FROM estacionamientos 
            INNER JOIN tarifas ON estacionamientos.id_tarifa = tarifas.id 
            WHERE estacionamientos.id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_estacionamiento);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        $hora_entrada = new DateTime($data['hora_entrada']);
        $hora_salida = new DateTime();
        $intervalo = $hora_entrada->diff($hora_salida);
        $minutos_totales = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

        // Calcular tarifa
        $tarifa_total = $minutos_totales * $data['tarifa_minuto'];
        if ($tarifa_total > $data['tarifa_maxima_diaria']) {
            $tarifa_total = $data['tarifa_maxima_diaria'];
        }

        // Actualizar el monto total y la hora de salida en la base de datos
        $sql_update = "UPDATE estacionamientos 
                       SET hora_salida = NOW(), monto_total = ?, estado = 'completado' 
                       WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('ii', $tarifa_total, $id_estacionamiento);
        if ($stmt_update->execute()) {
            echo "Pago realizado correctamente.";
        } else {
            echo "Error al realizar el pago: " . $stmt_update->error;
        }
    }
}
?>
