<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario ha iniciado sesión y si es un encargado
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'encargado') {
    // Redirigir si no es un usuario tipo encargado
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
include 'config.php';
include 'navbar.php'; // Incluye la barra de navegación

// Manejo de eliminación de bitácora
if (isset($_POST['eliminar_bitacora'])) {
    $id_bitacora = $_POST['id_bitacora'];
    $sql = "DELETE FROM bitacora WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $id_bitacora);
    if ($stmt->execute()) {
        $mensaje = "Bitácora eliminada correctamente.";
    } else {
        $error = "Error al eliminar la bitácora: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Encargado</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" /> <!-- Leaflet CSS para el mapa -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script> <!-- Leaflet JS para el mapa -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery -->
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        .contenedor {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        .mensaje {
            margin: 10px 0;
            padding: 10px;
            border-radius: 5px;
        }
        .mensaje.exito {
            background-color: #d4edda;
            color: #155724;
        }
        .mensaje.error {
            background-color: #f8d7da;
            color: #721c24;
        }
        .bitacora-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            margin: 10px 0;
            background-color: #f1f1f1;
            border-radius: 5px;
        }
        .bitacora-item button {
            background-color: #dc3545;
            color: white;
            border: none;
            padding: 5px 10px;
            border-radius: 3px;
            cursor: pointer;
        }
        .bitacora-item button:hover {
            background-color: #c82333;
        }
        #map {
            height: 400px;
            margin-top: 20px;
            border-radius: 10px;
            border: 1px solid #ccc;
        }
        textarea {
            width: 100%;
            padding: 10px;
            border: 1px solid #ccc;
            border-radius: 5px;
        }
        button {
            background-color: #007bff;
            color: white;
            border: none;
            padding: 10px 15px;
            border-radius: 5px;
            cursor: pointer;
        }
        button:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>
    <div class="contenedor">
        <h1>Bienvenido, <?php echo $_SESSION['nombre_completo']; ?> (Encargado)</h1>

        <!-- Mapa para la ubicación de los parqueadores -->
        <h2>Mapa de Ubicaciones de Parqueadores</h2>
        <div id="map"></div>

        <!-- Formulario para registrar eventos en la bitácora -->
        <h2>Registrar Bitácora</h2>
        <?php if (isset($mensaje)): ?>
            <div class="mensaje exito"><?php echo $mensaje; ?></div>
        <?php elseif (isset($error)): ?>
            <div class="mensaje error"><?php echo $error; ?></div>
        <?php endif; ?>
        <form method="POST" action="">
            <label for="texto-bitacora">Escriba los eventos o comentarios del día:</label>
            <textarea id="texto-bitacora" name="texto_bitacora" rows="5" required></textarea>
            <br>
            <button type="submit" name="guardar_bitacora">Guardar Bitácora</button>
        </form>

        <!-- Consultar bitácoras del encargado -->
        <h2>Bitácoras Registradas</h2>
        <?php
        // Verificar si se ha enviado el formulario para guardar la bitácora
        if (isset($_POST['guardar_bitacora'])) {
            $texto_bitacora = $_POST['texto_bitacora'];
            $fecha = date('Y-m-d'); // Fecha actual
            $rut_encargado = $_SESSION['rut'];

            // Insertar el registro en la tabla de bitácora
            $sql = "INSERT INTO bitacora (encargado_rut, fecha, texto) VALUES (?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param('sss', $rut_encargado, $fecha, $texto_bitacora);

            if ($stmt->execute()) {
                echo "<p class='mensaje exito'>Bitácora guardada correctamente.</p>";
            } else {
                echo "<p class='mensaje error'>Error al guardar la bitácora: " . $conn->error . "</p>";
            }
        }

        $sql = "SELECT id, fecha, texto FROM bitacora WHERE encargado_rut = ? ORDER BY fecha DESC";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $_SESSION['rut']);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo "<div class='bitacora-item'>";
                echo "<span><strong>{$row['fecha']}:</strong> {$row['texto']}</span>";
                echo "<form method='POST' action='' style='margin: 0;'>
                        <input type='hidden' name='id_bitacora' value='{$row['id']}'>
                        <button type='submit' name='eliminar_bitacora'>Eliminar</button>
                      </form>";
                echo "</div>";
            }
        } else {
            echo "<p>No hay bitácoras registradas.</p>";
        }
        ?>
    </div>

    <script>
        // Variables globales para el mapa y los marcadores
        let map;
        let markers = {}; // Para almacenar los marcadores de los parqueadores
        let locationInterval; // Variable para controlar el intervalo de actualización

        // Inicializar el mapa
        function initializeMap() {
            if (typeof map !== 'undefined') return;
            map = L.map('map').setView([-34.9824, -71.2362], 16); // Coordenadas iniciales de ejemplo
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            fetchLocations();
            locationInterval = setInterval(fetchLocations, 5000); // Actualizar las ubicaciones cada 5 segundos
        }

        // Función para obtener las ubicaciones de los parqueadores
        function fetchLocations() {
            $.ajax({
                url: 'get_parqueadores_locations.php',
                method: 'GET',
                dataType: 'json',
                success: function(data) {
                    updateMarkers(data);
                },
                error: function() {
                    alert('Error al obtener las ubicaciones.');
                }
            });
        }

        // Función para actualizar los marcadores en el mapa
        function updateMarkers(parqueadores) {
            parqueadores.forEach(function(parqueador) {
                const key = parqueador.rut;
                const lat = parqueador.latitude;
                const lng = parqueador.longitude;

                if (lat && lng) {
                    const popupContent = `Parqueador: ${parqueador.nombre}<br>Recaudado: $${parqueador.total_recaudado}`;
                    if (markers[key]) {
                        markers[key].setLatLng([lat, lng]);
                        markers[key].getPopup().setContent(popupContent);
                    } else {
                        const marker = L.marker([lat, lng]).addTo(map).bindPopup(popupContent);
                        markers[key] = marker;
                    }
                }
            });
        }

        // Iniciar la captura de ubicación cuando la página carga
        window.onload = function() {
            initializeMap();
        };
    </script>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>
