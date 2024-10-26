<?php
session_start();

// Verificar si el parqueador ha iniciado sesión
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'parqueador') {
    header("Location: login_parqueador.php");
    exit();
}

// Conexión a la base de datos
include 'config.php';

// Obtener el RUT del parqueador de la sesión
$rut_parqueador = $_SESSION['rut'];

// Usaremos la sesión para controlar el estado de la jornada
if (!isset($_SESSION['jornada_activa'])) {
    $_SESSION['jornada_activa'] = 'NO'; // Definir la jornada como no activa inicialmente
}

// --- Iniciar jornada ---
if (isset($_POST['iniciar_jornada'])) {
    $_SESSION['jornada_activa'] = 'SI'; // Cambiar el estado de la sesión para indicar jornada activa
    // Crear un nuevo registro de jornada
    $sql = "INSERT INTO jornadas (rut_parqueador, fecha_inicio) VALUES (?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $rut_parqueador);
    $stmt->execute();
    $_SESSION['id_jornada'] = $stmt->insert_id;
    echo "Jornada iniciada correctamente.";
}

// --- Cerrar jornada ---
if (isset($_POST['cerrar_jornada'])) {
    // Pedir la contrasena del parqueador para cerrar la jornada
    if (isset($_POST['clave_parqueador'])) {
        $clave_ingresada = $_POST['clave_parqueador'];
        
        // Verificar la contrasena del parqueador en la base de datos
        $sql = "SELECT contrasena FROM usuarios WHERE rut = ? AND tipo_usuario = 'parqueador'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $rut_parqueador);
        $stmt->execute();
        $result = $stmt->get_result();
        $usuario = $result->fetch_assoc();
        
        if ($usuario && password_verify($clave_ingresada, $usuario['contrasena'])) {
            $_SESSION['jornada_activa'] = 'NO'; // Cambiar el estado de la sesión para indicar jornada no activa
            // Actualizar la fecha de fin de la jornada actual
            $sql = "UPDATE jornadas SET fecha_fin = NOW() WHERE id = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('i', $_SESSION['id_jornada']);
            $stmt->execute();
            // Pasar todos los estacionamientos activos a morosidad
            $sql = "UPDATE estacionamientos SET estado = 'impago' WHERE rut_parqueador = ? AND estado = 'activo' AND id_jornada = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $rut_parqueador, $_SESSION['id_jornada']);
            $stmt->execute();
            unset($_SESSION['id_jornada']);
            echo "Jornada cerrada correctamente.";
        } else {
            echo "Clave incorrecta. No se pudo cerrar la jornada.";
        }
    } else {
        echo "Debe ingresar la contrasena para cerrar la jornada.";
    }
}

// --- Registrar un nuevo estacionamiento ---
if (isset($_POST['registrar_estacionamiento'])) {
    $patente = $_POST['patente'];
    
    // Validar la patente en el formato chileno correcto: AA1234 o AAAA12
    if (preg_match('/^[A-Z]{2}\d{4}$|^[A-Z]{4}\d{2}$/', $patente)) {
        $id_calle = $_POST['id_calle'];
        $id_tarifa = $_POST['id_tarifa'];

        $sql = "INSERT INTO estacionamientos (patente, rut_parqueador, id_calle, id_tarifa, hora_entrada, estado, id_jornada) 
                VALUES (?, ?, ?, ?, NOW(), 'activo', ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('ssiii', $patente, $rut_parqueador, $id_calle, $id_tarifa, $_SESSION['id_jornada']);
        $stmt->execute();
        echo "Estacionamiento registrado correctamente.";
    } else {
        echo "Formato de patente inválido. Debe ser AA1234 o AAAA12.";
    }
}

// --- Calcular monto de estacionamiento ---
if (isset($_POST['calcular_monto'])) {
    $patente = $_POST['patente_cobro'];

    $sql = "SELECT estacionamientos.id, TIMESTAMPDIFF(MINUTE, estacionamientos.hora_entrada, NOW()) AS minutos, tarifas.tarifa_minuto, tarifas.tarifa_maxima_diaria 
            FROM estacionamientos 
            INNER JOIN tarifas ON estacionamientos.id_tarifa = tarifas.id
            WHERE estacionamientos.patente = ? AND estacionamientos.estado = 'activo' AND estacionamientos.id_jornada = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $patente, $_SESSION['id_jornada']);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $monto = min($row['minutos'] * $row['tarifa_minuto'], $row['tarifa_maxima_diaria']);
        $_SESSION['monto_a_pagar'] = $monto;
        $_SESSION['id_estacionamiento'] = $row['id'];
        echo "El monto a pagar es: $monto CLP. ¿Desea proceder con el pago?";
    } else {
        echo "No se encontró un estacionamiento activo para la patente ingresada.";
    }
}

// --- Cobrar estacionamiento ---
if (isset($_POST['cobrar_estacionamiento'])) {
    if (isset($_SESSION['monto_a_pagar']) && isset($_SESSION['id_estacionamiento'])) {
        $monto = $_SESSION['monto_a_pagar'];
        $id_estacionamiento = $_SESSION['id_estacionamiento'];

        // Actualizar el estado y monto total del estacionamiento
        $sql_update = "UPDATE estacionamientos SET hora_salida = NOW(), monto_total = ?, estado = 'completado' WHERE id = ?";
        $stmt_update = $conn->prepare($sql_update);
        $stmt_update->bind_param('ii', $monto, $id_estacionamiento);
        $stmt_update->execute();

        unset($_SESSION['monto_a_pagar']);
        unset($_SESSION['id_estacionamiento']);

        echo "Pago realizado correctamente. El monto pagado es: $monto CLP.";
    } else {
        echo "Debe calcular el monto antes de proceder con el pago.";
    }
}

// --- Recaudación diaria ---
$sql = "SELECT SUM(monto_total) AS total_recaudado 
        FROM estacionamientos 
        WHERE rut_parqueador = ? AND estado = 'completado' AND id_jornada = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param('si', $rut_parqueador, $_SESSION['id_jornada']);
$stmt->execute();
$result = $stmt->get_result();
$recaudacion_diaria = $result->fetch_assoc()['total_recaudado'] ?? 0;

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Parqueador</title>
    <link rel="stylesheet" href="dashboard.css">
    <style>
        /* Estilos para el formulario */
        .formulario {
            margin-top: 20px;
            padding: 20px;
            background-color: white;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        label {
            display: block;
            margin-bottom: 8px;
        }
        input[type="text"], input[type="number"], input[type="password"], select {
            width: 100%;
            padding: 10px;
            margin-bottom: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }
        button {
            background-color: #28a745;
            color: white;
            border: none;
            padding: 10px 20px;
            cursor: pointer;
            font-size: 16px;
            border-radius: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #218838;
        }
        .logout button {
            background-color: #dc3545;
        }
        .logout button:hover {
            background-color: #c82333;
        }
        /* Estilos para el mensaje de error o éxito */
        #monto-container {
            margin-top: 20px;
            padding: 15px;
            display: none;
            border: 1px solid #ddd;
            border-radius: 8px;
        }
        #monto-container.success {
            background-color: #d4edda;
            color: #155724;
        }
        #monto-container.error {
            background-color: #f8d7da;
            color: #721c24;
        }
    </style>

    <script>
        // Capturar la ubicación en tiempo real y enviarla al servidor
        function iniciarGeolocalizacion() {
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(function(position) {
                    const lat = position.coords.latitude;
                    const lng = position.coords.longitude;

                    // Enviar la ubicación al servidor
                    fetch('save_location.php', {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded'
                        },
                        body: `latitude=${lat}&longitude=${lng}&rut_parqueador=<?php echo $rut_parqueador; ?>`
                    })
                    .then(response => response.text())
                    .then(data => {
                        console.log('Ubicación enviada:', data);
                    })
                    .catch(error => console.error('Error al enviar la ubicación:', error));
                }, function(error) {
                    console.error('Error al obtener la geolocalización:', error);
                });
            } else {
                alert('La geolocalización no es soportada por este navegador.');
            }
        }

        // Iniciar la captura de ubicación cuando la página carga
        window.onload = function() {
            iniciarGeolocalizacion();
        };
    </script>
</head>
<body>
    <div class="contenedor">
        <h1>Bienvenido, <?php echo $_SESSION['nombre_completo']; ?> (Parqueador)</h1>

        <!-- Mostrar el estado de la jornada -->
        <?php if ($_SESSION['jornada_activa'] == 'NO'): ?>
            <form method="POST" action="">
                <button type="submit" name="iniciar_jornada">Iniciar Jornada</button>
            </form>
        <?php else: ?>
            <form method="POST" action="">
                <label for="clave_parqueador">Ingrese su contrasena para cerrar la jornada:</label>
                <input type="password" id="clave_parqueador" name="clave_parqueador" required>
                <button type="submit" name="cerrar_jornada">Cerrar Jornada</button>
            </form>

            <h2>Registrar Estacionamiento</h2>
            <div class="formulario">
                <form method="POST" action="">
                    <label for="patente">Patente:</label>
                    <input type="text" id="patente" name="patente" required placeholder="Formato: AA1234 o AAAA12">

                    <label for="id_calle">Calle:</label>
                    <select id="id_calle" name="id_calle" required>
                        <?php
                        // Conectar a la base de datos y obtener las calles
                        include 'config.php';
                        $sql = "SELECT id, nombre_calle FROM calles";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value=\"{$row['id']}\">{$row['nombre_calle']}</option>";
                        }
                        ?>
                    </select>

                    <label for="id_tarifa">Tarifa:</label>
                    <select id="id_tarifa" name="id_tarifa" required>
                        <?php
                        $sql = "SELECT id, tarifa_minuto, tarifa_maxima_diaria FROM tarifas";
                        $result = $conn->query($sql);
                        while ($row = $result->fetch_assoc()) {
                            echo "<option value=\"{$row['id']}\">{$row['tarifa_minuto']} por minuto / {$row['tarifa_maxima_diaria']} máximo diario</option>";
                        }
                        ?>
                    </select>

                    <button type="submit" name="registrar_estacionamiento">Registrar</button>
                </form>
            </div>

            <h2>Finalizar Estacionamiento (Por Patente)</h2>
            <form method="POST" action="">
                <label for="patente-cobro">Patente:</label>
                <input type="text" id="patente-cobro" name="patente_cobro" required placeholder="Formato: AA1234 o AAAA12">
                <button type="submit" name="calcular_monto">Calcular Monto</button>
                <?php if (isset($_SESSION['monto_a_pagar'])): ?>
                    <p>El monto a pagar es: <?php echo $_SESSION['monto_a_pagar']; ?> CLP</p>
                    <button type="submit" name="cobrar_estacionamiento">Cobrar Estacionamiento</button>
                <?php endif; ?>
            </form>

            <h2>Historial de Estacionamientos</h2>
            <?php
            $sql = "SELECT estacionamientos.id AS estacionamiento_id, estacionamientos.patente, calles.nombre_calle, tarifas.tarifa_minuto, tarifas.tarifa_maxima_diaria, estacionamientos.hora_entrada, estacionamientos.hora_salida, estacionamientos.monto_total
                    FROM estacionamientos
                    INNER JOIN calles ON estacionamientos.id_calle = calles.id
                    INNER JOIN tarifas ON estacionamientos.id_tarifa = tarifas.id
                    WHERE estacionamientos.rut_parqueador = ? AND estacionamientos.id_jornada = ?
                    ORDER BY estacionamientos.hora_entrada DESC";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('si', $rut_parqueador, $_SESSION['id_jornada']);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<tr><th>ID</th><th>Patente</th><th>Calle</th><th>Tarifa</th><th>Hora Entrada</th><th>Hora Salida</th><th>Monto Total</th></tr>";
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>{$row['estacionamiento_id']}</td>";
                    echo "<td>{$row['patente']}</td>";
                    echo "<td>{$row['nombre_calle']}</td>";
                    echo "<td>{$row['tarifa_minuto']} por minuto / {$row['tarifa_maxima_diaria']} máximo diario</td>";
                    echo "<td>{$row['hora_entrada']}</td>";
                    echo "<td>{$row['hora_salida']}</td>";
                    echo "<td>{$row['monto_total']}</td>";
                    echo "</tr>";
                }
                echo "</table>";
            } else {
                echo "No hay estacionamientos registrados.";
            }
            ?>

            <h2>Recaudación Total de la Jornada</h2>
            <p>Total recaudado hoy: <?php echo $recaudacion_diaria; ?> CLP</p>
        <?php endif; ?>

        <div class="logout">
            <button class="btn" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>
    </div>
</body>
</html>
