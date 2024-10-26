<?php
session_start();

// Verificar si el parqueador ha iniciado sesión
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'parqueador') {
    header("Location: login_parqueador.php"); // Redirigir al formulario de inicio de sesión si no ha iniciado sesión
    exit();
}

// Conexión a la base de datos
include '../includes/config.php';

// Obtener el RUT del parqueador de la sesión
$rut_parqueador = $_SESSION['rut'];

// --- Registrar un nuevo estacionamiento ---

if (isset($_POST['registrar_estacionamiento'])) {
    $patente = $_POST['patente'];
    $id_calle = $_POST['id_calle'];
    $id_tarifa = $_POST['id_tarifa'];

    // Validar la patente (6 caracteres alfanuméricos, formato aa1111 o aaaa11)
    if (!preg_match('/^(aa\d{4}|[a-zA-Z]{4}\d{2})$/', $patente)) {
        echo "Error: Formato de patente inválido.";
    } else {
        // Insertar el nuevo estacionamiento en la base de datos
        $sql = "INSERT INTO estacionamientos (patente, rut_parqueador, id_calle, id_tarifa) 
                VALUES (?, ?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssii', $patente, $rut_parqueador, $id_calle, $id_tarifa);

        if ($stmt->execute()) {
            echo "Estacionamiento registrado correctamente.";
        } else {
            echo "Error al registrar el estacionamiento: " . $stmt->error;
        }
    }
}

// --- Finalizar un estacionamiento ---

if (isset($_POST['finalizar_estacionamiento'])) {
    $id_estacionamiento = $_POST['id_estacionamiento'];

    // Obtener la hora de entrada del estacionamiento
    $sql = "SELECT hora_entrada FROM estacionamientos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_estacionamiento);
    $stmt->execute();
    $stmt->bind_result($hora_entrada);
    $stmt->fetch();
    $stmt->close();

    // Calcular el tiempo transcurrido y el monto total (implementar la lógica de cálculo según las tarifas)
    $tiempo_transcurrido = calcularTiempoTranscurrido($hora_entrada); // Implementar esta función
    $monto_total = calcularMontoTotal($id_tarifa, $tiempo_transcurrido); // Implementar esta función

    // Actualizar el estacionamiento en la base de datos
    $sql = "UPDATE estacionamientos SET hora_salida = NOW(), monto_total = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('di', $monto_total, $id_estacionamiento);

    if ($stmt->execute()) {
        echo "Estacionamiento finalizado correctamente. Monto total: $" . $monto_total;
    } else {
        echo "Error al finalizar el estacionamiento: " . $stmt->error;
    }
}

// --- Consultar historial de estacionamientos ---

// (Implementar la lógica para obtener y mostrar el historial de estacionamientos del parqueador)

$conn->close();
?>