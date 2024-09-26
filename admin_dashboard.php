<?php
// Verificar si el usuario ha iniciado sesión
session_start();
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'administrador') {
    // Redirigir si no es administrador
    header("Location: login.php");
    exit();
}

// Conexión a la base de datos
include 'config.php';
include 'navbar.php';
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración</title>
    <link rel="stylesheet" href="dashboard.css"> <!-- Asegúrate de que la ruta sea correcta -->
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script> <!-- Añade jQuery para facilitar AJAX -->
    <script src="funciones.js"></script>
</head>
<body>
    <!-- Incluir la barra de navegación -->

    <div class="contenedor">
        <h1>Bienvenido, <?php echo $_SESSION['nombre_completo']; ?> (Administrador)</h1>

        <div class="buttons">
            <button class="btn" id="toggle-users-btn" onclick="toggleUsers()">Mostrar Usuarios</button>
            <button class="btn" id="toggle-insert-btn" onclick="toggleInsert()">Insertar Usuario</button>
        </div>

        <!-- Contenedor para la tabla de usuarios -->
        <div id="dynamic-content" style="display: none;"></div>
        <!-- Contenedor para el formulario de inserción -->
        <div id="insert-content" style="display: none;"></div>

        <div class="logout">
            <button class="btn" onclick="window.location.href='logout.php'">Cerrar sesión</button>
        </div>
    </div>

    <script>
        // Función para mostrar u ocultar la tabla de usuarios
        function toggleUsers() {
            const btn = document.getElementById('toggle-users-btn');
            const content = document.getElementById('dynamic-content');
            
            if (content.style.display === 'none') {
                // Mostrar la tabla
                loadContent('usuarios_tabla.php', 'dynamic-content');
                content.style.display = 'block';
                btn.textContent = 'Ocultar Usuarios';
            } else {
                // Ocultar la tabla
                content.style.display = 'none';
                btn.textContent = 'Mostrar Usuarios';
            }
        }

        // Función para mostrar u ocultar el formulario de inserción de usuario
        function toggleInsert() {
            const btn = document.getElementById('toggle-insert-btn');
            const content = document.getElementById('insert-content');
            
            if (content.style.display === 'none') {
                // Mostrar el formulario de inserción
                loadContent('insertar_usuario.php', 'insert-content');
                content.style.display = 'block';
                btn.textContent = 'Cancelar Inserción';
            } else {
                // Ocultar el formulario de inserción
                content.style.display = 'none';
                btn.textContent = 'Insertar Usuario';
            }
        }

        // Función para cargar contenido dinámicamente en un div usando AJAX
        function loadContent(page, targetId) {
            $.ajax({
                url: page,
                method: "GET",
                success: function(response) {
                    $('#' + targetId).html(response); // Cargar el contenido en el div
                },
                error: function() {
                    alert("Error al cargar la página.");
                }
            });
        }

        // Función para eliminar un usuario
        function eliminarUsuario(rut) {
            if (confirm("¿Estás seguro de que deseas eliminar este usuario?")) {
                $.ajax({
                    url: 'procesar_borrar_usuario.php',
                    method: 'POST',
                    data: { rut: rut },
                    success: function(response) {
                        alert(response);
                        loadContent('usuarios_tabla.php', 'dynamic-content'); // Volver a cargar la tabla después de borrar
                    },
                    error: function() {
                        alert("Error al borrar el usuario.");
                    }
                });
            }
        }

        // Función para cargar el formulario de actualización con los datos de un usuario específico
        function cargarFormularioActualizar(rut) {
            $.ajax({
                url: 'obtener_usuario.php', // Archivo PHP que obtiene los datos del usuario
                method: 'POST',
                data: { rut: rut },
                dataType: 'json',
                success: function(response) {
                    // Llenar el formulario con los datos del usuario
                    $('#dynamic-content').html(`
                        <h2>Actualizar Usuario</h2>
                        <form id="form-actualizar-usuario">
                            <label for="rut">RUT:</label>
                            <input type="text" id="rut" name="rut" value="${response.rut}" readonly>
                            <br><br>
                            <label for="nombre">Nuevo Nombre Completo:</label>
                            <input type="text" id="nombre" name="nombre_completo" value="${response.nombre_completo}">
                            <br><br>
                            <label for="correo">Nuevo Correo Electrónico:</label>
                            <input type="email" id="correo" name="correo_electronico" value="${response.correo_electronico}">
                            <br><br>
                            <label for="tipo_usuario">Nuevo Tipo de Usuario:</label>
                            <select id="tipo_usuario" name="tipo_usuario">
                                <option value="">-- Seleccionar --</option>
                                <option value="parqueador" ${response.tipo_usuario === 'parqueador' ? 'selected' : ''}>Parqueador</option>
                                <option value="encargado" ${response.tipo_usuario === 'encargado' ? 'selected' : ''}>Encargado</option>
                            </select>
                            <br><br>
                            <label for="estado">Estado:</label>
                            <select id="estado" name="estado">
                                <option value="">-- Seleccionar --</option>
                                <option value="activo" ${response.estado === 'activo' ? 'selected' : ''}>Activo</option>
                                <option value="inactivo" ${response.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                            </select>
                            <br><br>
                            <input type="submit" value="Actualizar Usuario">
                        </form>
                    `);
                    
                    // Habilitar la actualización mediante AJAX
                    $('#form-actualizar-usuario').on('submit', function(e) {
                        e.preventDefault(); // Previene la recarga de la página
                        $.ajax({
                            url: 'procesar_actualizar_usuario.php',
                            method: 'POST',
                            data: $(this).serialize(), // Envía los datos del formulario
                            success: function(response) {
                                alert(response);
                                loadContent('usuarios_tabla.php', 'dynamic-content'); // Recargar la tabla de usuarios
                            },
                            error: function() {
                                alert('Error al actualizar el usuario.');
                            }
                        });
                    });
                },
                error: function() {
                    alert('Error al cargar los datos del usuario.');
                }
            });
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>
