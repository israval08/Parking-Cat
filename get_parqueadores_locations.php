<?php
// get_parqueadores_locations.php
include 'config.php';

// Consulta actualizada para evitar duplicados
$sql = "SELECT u.rut, MIN(u.nombre_completo) AS nombre, MIN(u.latitude) AS latitude, MIN(u.longitude) AS longitude, 
               IFNULL(SUM(e.monto_total), 0) AS total_recaudado
        FROM usuarios u
        INNER JOIN jornadas j ON u.rut = j.rut_parqueador AND j.fecha_fin IS NULL
        LEFT JOIN estacionamientos e ON e.rut_parqueador = u.rut 
            AND e.id_jornada = j.id AND e.estado = 'completado' AND DATE(e.hora_salida) = CURDATE()
        WHERE u.tipo_usuario = 'parqueador' 
        GROUP BY u.rut";

$result = $conn->query($sql);

$parqueadores = [];

if ($result && $result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        // Solo agregar si tiene coordenadas válidas
        if (!is_null($row['latitude']) && !is_null($row['longitude'])) {
            $parqueadores[] = [
                'rut' => $row['rut'],
                'nombre' => $row['nombre'],
                'latitude' => floatval($row['latitude']),
                'longitude' => floatval($row['longitude']),
                'total_recaudado' => intval($row['total_recaudado']) // Asegurarse de que sea un número entero
            ];
        }
    }
}

header('Content-Type: application/json');
echo json_encode($parqueadores);

$conn->close();
?>
