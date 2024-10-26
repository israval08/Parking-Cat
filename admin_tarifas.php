<?php
// Procesar la nueva tarifa
if (isset($_POST['agregar_tarifa'])) {
    $tarifa_minuto = $_POST['tarifa_minuto'];
    $tarifa_maxima_diaria = $_POST['tarifa_maxima_diaria'];
    $fecha_inicio = $_POST['fecha_inicio'];
    $fecha_fin = isset($_POST['fecha_fin']) ? $_POST['fecha_fin'] : null;

    // Insertar o actualizar la tarifa en la base de datos
    $sql = "INSERT INTO tarifas (tarifa_minuto, tarifa_maxima_diaria, fecha_inicio, fecha_fin) 
            VALUES (?, ?, ?, ?) 
            ON DUPLICATE KEY UPDATE tarifa_minuto = VALUES(tarifa_minuto), tarifa_maxima_diaria = VALUES(tarifa_maxima_diaria), fecha_fin = VALUES(fecha_fin)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('iiss', $tarifa_minuto, $tarifa_maxima_diaria, $fecha_inicio, $fecha_fin);
    
    if ($stmt->execute()) {
        echo "Tarifa actualizada correctamente.";
    } else {
        echo "Error al actualizar la tarifa: " . $stmt->error;
    }
}
?>
