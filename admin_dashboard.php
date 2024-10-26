<?php
session_start(); // Iniciar la sesión

// Verificar si el usuario ha iniciado sesión
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'administrador') {
    // Redirigir si no es administrador
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
include 'config.php';
include 'navbar.php';

// Obtener la última tarifa registrada
$sql = "SELECT id, tarifa_minuto, pago_minimo, pago_maximo, tarifa_maxima_diaria, fecha_inicio, fecha_fin FROM tarifas ORDER BY id DESC LIMIT 1";
$result = $conn->query($sql);
$tarifa = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="formularios.css"> <!-- CSS de los formularios -->
    <link rel="stylesheet" href="dashboard.css"> <!-- CSS del dashboard -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" /> <!-- Leaflet CSS para el mapa -->
    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script> <!-- Leaflet JS para el mapa -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- jQuery para facilitar AJAX -->
</head>
<body>
    <div class="contenedor">
        <h1>Bienvenido, <?php echo $_SESSION['nombre_completo']; ?> (Administrador)</h1>

        <div class="buttons">
            <button class="btn" id="toggle-users-btn" onclick="toggleUsers()">Mostrar Usuarios</button>
            <button class="btn" id="toggle-insert-btn" onclick="toggleInsert()">Insertar Usuario</button>
            <button class="btn" id="toggle-tarifas-btn" onclick="toggleTarifas()">Mostrar Tarifas</button>
            <button class="btn" id="toggle-map-btn" onclick="toggleMap()">Mostrar Mapa</button>
            <!-- Botón para mostrar/ocultar popups -->
            <button class="btn" id="toggle-popups-btn" onclick="togglePopups()">Ocultar Información</button>
        </div>

        <!-- Contenedor para insertar usuario -->
        <div id="insert-content" style="display: none;">
            <!-- Formulario para insertar un nuevo usuario -->
            <form id="form-insertar" method="POST">
                <label for="rut-insert">RUT:</label>
                <input type="text" name="rut" id="rut-insert" required><br>

                <label for="nombre-insert">Nombre Completo:</label>
                <input type="text" name="nombre" id="nombre-insert" required><br>

                <label for="email-insert">Correo Electrónico:</label>
                <input type="email" name="email" id="email-insert" required><br>

                <label for="tipo_usuario-insert">Tipo de Usuario:</label>
                <select name="tipo_usuario" id="tipo_usuario-insert" required>
                    <option value="parqueador">Parqueador</option>
                    <option value="administrador">Administrador</option>
                    <option value="encargado">Encargado</option>
                </select><br>

                <label for="contrasena-insert">Contraseña:</label>
                <input type="password" name="contrasena" id="contrasena-insert" required><br>

                <button type="submit">Insertar Usuario</button>
            </form>
        </div>

        <!-- Contenedor para la tabla de tarifas -->
        <div id="tarifas-content" style="display: none;">
            <h3>Tarifa actual</h3>
            <form id="form-tarifa" method="POST" onsubmit="return actualizarTarifa();">
                <label for="tarifa_minuto">Tarifa por minuto:</label>
                <input type="number" name="tarifa_minuto" value="<?php echo $tarifa['tarifa_minuto']; ?>" required><br>

                <label for="pago_minimo">Pago mínimo:</label>
                <input type="number" name="pago_minimo" value="<?php echo $tarifa['pago_minimo']; ?>" required><br>

                <label for="pago_maximo">Pago máximo:</label>
                <input type="number" name="pago_maximo" value="<?php echo $tarifa['pago_maximo']; ?>" required><br>

                <label for="tarifa_maxima_diaria">Tarifa máxima diaria:</label>
                <input type="number" name="tarifa_maxima_diaria" value="<?php echo $tarifa['tarifa_maxima_diaria']; ?>" required><br>

                <label for="fecha_inicio">Fecha de inicio:</label>
                <input type="date" name="fecha_inicio" value="<?php echo $tarifa['fecha_inicio']; ?>" required><br>

                <label for="fecha_fin">Fecha de fin:</label>
                <input type="date" name="fecha_fin" value="<?php echo $tarifa['fecha_fin']; ?>" required><br>

                <button type="submit">Actualizar Tarifa</button>
            </form>
        </div>

        <!-- Cuadro modal para actualizar los datos -->
        <div id="modal-actualizar" style="display:none;">
            <div class="modal-content">
                <h3>Actualizar Usuario</h3>
                <form id="form-actualizar">
                    <label for="rut-update">RUT (No editable):</label>
                    <input type="text" name="rut" id="rut-update" readonly><br>

                    <label for="nombre-update">Nombre Completo:</label>
                    <input type="text" name="nombre" id="nombre-update" required><br>

                    <label for="email-update">Correo Electrónico:</label>
                    <input type="email" name="email" id="email-update" required><br>

                    <label for="tipo_usuario-update">Tipo de Usuario:</label>
                    <select name="tipo_usuario" id="tipo_usuario-update" required>
                        <option value="parqueador">Parqueador</option>
                        <option value="administrador">Administrador</option>
                        <option value="encargado">Encargado</option>
                    </select><br>

                    <button type="submit">Guardar Cambios</button>
                    <button type="button" onclick="cerrarModal()">Cancelar</button>
                </form>
            </div>
        </div>

        <!-- Contenedor dinámico para Usuarios, Mapa, etc. -->
        <div id="dynamic-content" style="display: none;"></div>
        <div id="map" style="display:none; height: 400px; margin-top: 20px;"></div> <!-- Mapa oculto inicialmente -->

        <div class="logout">
            <button class="btn" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>
    </div>

    <script>
        // Variables globales para el mapa y los marcadores
        let map;
        let markers = {}; // Para almacenar los marcadores de los parqueadores
        let locationInterval; // Variable para controlar el intervalo de actualización
        let popupsVisible = true; // Controla si los popups están visibles

        function toggleMap() {
            const mapElement = document.getElementById('map');
            const btn = document.getElementById('toggle-map-btn');
            if (mapElement.style.display === 'none') {
                mapElement.style.display = 'block';
                initializeMap();
                btn.textContent = 'Ocultar Mapa';
            } else {
                mapElement.style.display = 'none';
                btn.textContent = 'Mostrar Mapa';
                if (typeof locationInterval !== 'undefined') {
                    clearInterval(locationInterval);
                }
            }
        }

        function initializeMap() {
            if (typeof map !== 'undefined') return;
            map = L.map('map').setView([-34.9824, -71.2362], 16);
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                maxZoom: 19,
                attribution: '© OpenStreetMap contributors'
            }).addTo(map);
            fetchLocations();
            locationInterval = setInterval(fetchLocations, 5000);
        }

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
                        if (popupsVisible) {
                            markers[key].openPopup();
                        }
                    } else {
                        const marker = L.marker([lat, lng]).addTo(map).bindPopup(popupContent);
                        markers[key] = marker;
                        if (popupsVisible) {
                            marker.openPopup();
                        }
                    }
                }
            });
        }

        function togglePopups() {
            const btn = document.getElementById('toggle-popups-btn');
            popupsVisible = !popupsVisible;
            Object.keys(markers).forEach(function(key) {
                if (popupsVisible) {
                    markers[key].openPopup();
                    btn.textContent = 'Ocultar Información';
                } else {
                    markers[key].closePopup();
                    btn.textContent = 'Mostrar Información';
                }
            });
        }

        function toggleUsers() {
            const btn = document.getElementById('toggle-users-btn');
            const content = document.getElementById('dynamic-content');
            if (content.style.display === 'none') {
                loadContent('usuarios_tabla.php', 'dynamic-content');
                content.style.display = 'block';
                btn.textContent = 'Ocultar Usuarios';
            } else {
                content.style.display = 'none';
                btn.textContent = 'Mostrar Usuarios';
            }
        }

        function toggleInsert() {
            const btn = document.getElementById('toggle-insert-btn');
            const content = document.getElementById('insert-content');
            if (content.style.display === 'none') {
                content.style.display = 'block';
                btn.textContent = 'Cancelar Inserción';
            } else {
                content.style.display = 'none';
                btn.textContent = 'Insertar Usuario';
            }
        }

        function toggleTarifas() {
            const btn = document.getElementById('toggle-tarifas-btn');
            const content = document.getElementById('tarifas-content');
            if (content.style.display === 'none') {
                content.style.display = 'block';
                btn.textContent = 'Ocultar Tarifas';
            } else {
                content.style.display = 'none';
                btn.textContent = 'Mostrar Tarifas';
            }
        }

        // Manejar el borrado del usuario
        $(document).on('click', '.btn-borrar', function() {
            const rut = $(this).data('id');
            if (confirm('¿Estás seguro de que deseas borrar este usuario?')) {
                $.ajax({
                    url: 'borrar_usuario.php',
                    method: 'POST',
                    data: { rut: rut },
                    success: function(response) {
                        alert(response);
                        toggleUsers(); // Recargar la tabla de usuarios después de borrar
                    },
                    error: function() {
                        alert('Error al borrar el usuario.');
                    }
                });
            }
        });

        function loadContent(page, targetId) {
            $.ajax({
                url: page,
                method: "GET",
                success: function(response) {
                    $('#' + targetId).html(response);
                },
                error: function() {
                    alert("Error al cargar la página.");
                }
            });
        }

        // Manejo del formulario de inserción de usuario con AJAX
        $('#form-insertar').submit(function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: 'insertar_usuario.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    alert(response);
                    toggleUsers();
                },
                error: function() {
                    alert('Error al insertar el usuario.');
                }
            });
        });

        // Manejar la apertura del cuadro modal con los datos del usuario
        $(document).on('click', '.btn-actualizar', function() {
            const rut = $(this).data('id'); // Obtener el RUT del usuario seleccionado
            $.ajax({
                url: 'obtener_usuario.php',
                method: 'POST',
                data: { rut: rut },
                dataType: 'json',
                success: function(response) {
                    if (response && !response.error) {
                        // Rellenar el modal de actualización con los datos del usuario
                        $('#rut-update').val(response.rut);
                        $('#nombre-update').val(response.nombre_completo);
                        $('#email-update').val(response.correo_electronico);
                        $('#tipo_usuario-update').val(response.tipo_usuario);

                        // Mostrar el modal de actualización
                        $('#modal-actualizar').show();
                    } else {
                        alert('No se pudo obtener la información del usuario.');
                    }
                },
                error: function() {
                    alert('Error al realizar la solicitud.');
                }
            });
        });

        // Manejar la actualización del usuario desde el modal
        $('#form-actualizar').submit(function(event) {
            event.preventDefault();
            const formData = $(this).serialize();
            $.ajax({
                url: 'actualizar_usuario.php',
                method: 'POST',
                data: formData,
                success: function(response) {
                    alert(response);
                    cerrarModal();
                    toggleUsers(); // Recargar la tabla de usuarios
                },
                error: function() {
                    alert('Error al actualizar el usuario.');
                }
            });
        });

        // Función para cerrar el modal
        function cerrarModal() {
            $('#modal-actualizar').hide();
        }

        // Función para formatear el RUT mientras se escribe (solo en inserción)
        function formatRutInput(rutField) {
            let rut = rutField.value.replace(/\./g, '').replace(/-/g, ''); // Eliminar puntos y guiones
            if (rut.length > 1) {
                // Agregar puntos y guión
                rut = rut.slice(0, -1).replace(/\B(?=(\d{3})+(?!\d))/g, '.') + '-' + rut.slice(-1);
            }
            rutField.value = rut; // Asignar el valor formateado al campo RUT
        }

        // Aplicar el formateo mientras el usuario escribe en el campo de inserción
        document.getElementById('rut-insert').addEventListener('input', function() {
            formatRutInput(this);
        });
    </script>
</body>
</html>

<?php
if (isset($conn)) {
    $conn->close();
}
?>
