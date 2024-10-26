<?php
include 'config.php';

// Consulta para obtener las tarifas actuales
$sql = "SELECT id, tarifa_minuto, tarifa_maxima_diaria, fecha_inicio, fecha_fin FROM tarifas";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<h3>Tarifas actuales</h3>";
    echo "<table border='1'>";
    echo "<tr><th>ID</th><th>Tarifa por minuto</th><th>Tarifa máxima diaria</th><th>Fecha de inicio</th><th>Fecha fin</th><th>Actualizar</th></tr>";
    
    // Mostrar cada tarifa con su respectivo formulario de actualización
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<form id='form-tarifa-{$row['id']}' method='POST' onsubmit='return actualizarTarifa({$row['id']});'>";
        echo "<td>{$row['id']}</td>";
        echo "<td><input type='number' name='tarifa_minuto' value='{$row['tarifa_minuto']}' required></td>";
        echo "<td><input type='number' name='tarifa_maxima_diaria' value='{$row['tarifa_maxima_diaria']}' required></td>";
        echo "<td><input type='date' name='fecha_inicio' value='{$row['fecha_inicio']}' required></td>";
        echo "<td><input type='date' name='fecha_fin' value='{$row['fecha_fin']}'></td>";
        echo "<td><button type='submit'>Actualizar</button></td>";
        echo "<input type='hidden' name='id' value='{$row['id']}'>";
        echo "</form>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No hay tarifas registradas.";
}

$conn->close();
?>
