<?php
include 'config.php'; // Conexión a la base de datos

// Consulta para obtener la lista de usuarios
$sql = "SELECT rut, nombre_completo, tipo_usuario, correo_electronico FROM usuarios";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>RUT</th><th>Nombre Completo</th><th>Tipo de Usuario</th><th>Correo Electrónico</th><th>Acciones</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>{$row['rut']}</td>";
        echo "<td>{$row['nombre_completo']}</td>";
        echo "<td>{$row['tipo_usuario']}</td>";
        echo "<td>{$row['correo_electronico']}</td>";
        // Botones de actualizar y borrar
        echo "<td>
                <button class='btn-actualizar' data-id='{$row['rut']}'>Actualizar</button>
                <button class='btn-borrar' data-id='{$row['rut']}'>Borrar</button>
              </td>";
        echo "</tr>";
    }
    echo "</table>";
} else {
    echo "No se encontraron usuarios.";
}

$conn->close();
?>

<!-- Cuadro modal para actualizar los datos -->
<div id="modal-actualizar" style="display:none;">
    <div class="modal-content">
        <h3>Actualizar Usuario</h3>
        <form id="form-actualizar">
            <label for="rut">RUT (No editable):</label>
            <input type="text" name="rut" id="rut" readonly><br>

            <label for="nombre">Nombre Completo:</label>
            <input type="text" name="nombre" id="nombre" required><br>

            <label for="email">Correo Electrónico:</label>
            <input type="email" name="email" id="email" required><br>

            <label for="tipo_usuario">Tipo de Usuario:</label>
            <select name="tipo_usuario" id="tipo_usuario" required>
                <option value="parqueador">Parqueador</option>
                <option value="administrador">Administrador</option>
                <option value="encargado">Encargado</option>
            </select><br>

            <button type="submit">Guardar Cambios</button>
            <button type="button" onclick="cerrarModal()">Cancelar</button>
        </form>
    </div>
</div>

<script>
    // Manejar la apertura del cuadro modal
    $(document).on('click', '.btn-actualizar', function() {
        const rut = $(this).data('id'); // Obtener el RUT del usuario seleccionado
        $.ajax({
            url: 'obtener_usuario.php', // Archivo que obtiene los datos del usuario
            method: 'POST',
            data: { rut: rut }, // Enviar el RUT del usuario al servidor
            dataType: 'json',
            success: function(response) {
                if (response) {
                    // Rellenar el modal con los datos del usuario
                    $('#rut').val(response.rut);
                    $('#nombre').val(response.nombre_completo);
                    $('#email').val(response.correo_electronico);
                    $('#tipo_usuario').val(response.tipo_usuario);
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

    // Cerrar el modal
    function cerrarModal() {
        $('#modal-actualizar').hide();
    }

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
                loadContent('usuarios_tabla.php', 'dynamic-content'); // Recargar la tabla de usuarios después de actualizar
            },
            error: function() {
                alert('Error al actualizar el usuario.');
            }
        });
    });

    // Función para cargar contenido dinámico
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
</script>




<style>
    /* Estilos del modal */
    #modal-actualizar {
        position: fixed;
        left: 50%;
        top: 50%;
        transform: translate(-50%, -50%);
        z-index: 9999;
        background-color: white;
        padding: 20px;
        border: 1px solid #ccc;
        border-radius: 10px;
        width: 300px;
        box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
    }
    .modal-content {
        display: flex;
        flex-direction: column;
    }
    .modal-content input, .modal-content select {
        margin-bottom: 10px;
        padding: 8px;
        width: 100%;
        box-sizing: border-box;
    }
    
    /* Estilo para el botón de Actualizar */
    .btn-actualizar {
        background-color: blue;
        color: white;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 5px;
    }

    .btn-actualizar:hover {
        background-color: darkblue;
    }

    /* Estilo para el botón de Borrar */
    .btn-borrar {
        background-color: red;
        color: white;
        border: none;
        padding: 8px 16px;
        cursor: pointer;
        border-radius: 5px;
    }

    .btn-borrar:hover {
        background-color: darkred;
    }

    /* Hacer que la tabla sea responsiva y ocultar la columna de correo en pantallas pequeñas */
    @media only screen and (max-width: 768px) {
        th:nth-child(4), td:nth-child(4) {
            display: none;
        }
    }
</style>


