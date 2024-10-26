<?php
include 'config.php';

if (isset($_POST['patente'])) {
    $patente = $_POST['patente'];

    // Buscar el estacionamiento activo para la patente
    $sql = "SELECT estacionamientos.id, hora_entrada, tarifas.tarifa_minuto, tarifas.tarifa_maxima_diaria 
            FROM estacionamientos 
            INNER JOIN tarifas ON estacionamientos.id_tarifa = tarifas.id 
            WHERE estacionamientos.patente = ? AND estado = 'activo'";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $patente);
    $stmt->execute();
    $result = $stmt->get_result();
    $data = $result->fetch_assoc();

    if ($data) {
        $id_estacionamiento = $data['id'];
        $hora_entrada = new DateTime($data['hora_entrada']);
        $hora_salida = new DateTime();
        $intervalo = $hora_entrada->diff($hora_salida);
        $minutos_totales = ($intervalo->days * 24 * 60) + ($intervalo->h * 60) + $intervalo->i;

        // Calcular tarifa
        $tarifa_total = $minutos_totales * $data['tarifa_minuto'];
        if ($tarifa_total > $data['tarifa_maxima_diaria']) {
            $tarifa_total = $data['tarifa_maxima_diaria'];
        }

        // Enviar respuesta como JSON
        echo json_encode([
            'id_estacionamiento' => $id_estacionamiento,
            'monto_total' => $tarifa_total
        ]);
    } else {
        echo json_encode(['error' => 'No se encontró un estacionamiento activo para la patente ingresada.']);
    }
}
