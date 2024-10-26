<?php
// Conexión a la base de datos
include 'config.php';

function marcarComoMoroso($id_estacionamiento) {
    global $conn;

    // Cambiar el estado del estacionamiento a moroso
    $sql = "UPDATE estacionamientos SET estado = 'impago' WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_estacionamiento);
    if ($stmt->execute()) {
        echo "El vehículo ha sido marcado como moroso.";
    } else {
        echo "Error al marcar como moroso.";
    }
}

function pagarDeuda($id_estacionamiento) {
    global $conn;

    // Actualizar estado a completado y registrar el pago
    $sql = "UPDATE estacionamientos SET estado = 'completado', monto_total = NULL WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_estacionamiento);
    if ($stmt->execute()) {
        echo "El pago ha sido registrado y el estado actualizado.";
    } else {
        echo "Error al registrar el pago.";
    }
}

// Ejemplo de uso
if (isset($_POST['pagar'])) {
    pagarDeuda($_POST['id_estacionamiento']);
} else {
    marcarComoMoroso(1);
}
?>
