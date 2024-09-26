
function mostrarDatosUsuario(rut) {
    fetch('obtener_usuario.php?rut=' + rut)
        .then(response => response.json())
        .then(data => {
            if (data.error) {
                alert(data.error);
            } else {
                // Construir la tabla con los datos del usuario
                const tablaUsuario = `
                    <table>
                        <tr><th>RUT</th><td>${data.rut}</td></tr>
                        <tr><th>Nombre Completo</th><td><input type="text" id="nombre_completo" value="${data.nombre_completo}"></td></tr>
                        <tr><th>Correo Electrónico</th><td><input type="email" id="correo_electronico" value="${data.correo_electronico}"></td></tr>
                        <tr><th>Tipo de Usuario</th><td>
                            <select id="tipo_usuario">
                                <option value="parqueador" ${data.tipo_usuario === 'parqueador' ? 'selected' : ''}>Parqueador</option>
                                <option value="encargado" ${data.tipo_usuario === 'encargado' ? 'selected' : ''}>Encargado</option>
                                <option value="administrador" ${data.tipo_usuario === 'administrador' ? 'selected' : ''}>Administrador</option>
                            </select>
                        </td></tr>
                        <tr><th>Estado</th><td>
                            <select id="estado">
                                <option value="activo" ${data.estado === 'activo' ? 'selected' : ''}>Activo</option>
                                <option value="inactivo" ${data.estado === 'inactivo' ? 'selected' : ''}>Inactivo</option>
                            </select>
                        </td></tr>
                    </table>
                    <button onclick="confirmarActualizacion('${data.rut}')">Confirmar Actualización</button>
                `;

                // Mostrar la tabla en el div
                document.getElementById('datos-usuario').innerHTML = tablaUsuario;
                document.getElementById('datos-usuario').style.display = 'block';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al cargar los datos del usuario.');
        });
}

function confirmarActualizacion(rut) {
    if (confirm("¿Estás seguro de que deseas actualizar este usuario?")) {
        // Obtener los datos actualizados del formulario
        const nombreCompleto = document.getElementById('nombre_completo').value;
        const correoElectronico = document.getElementById('correo_electronico').value;
        const tipoUsuario = document.getElementById('tipo_usuario').value;
        const estado = document.getElementById('estado').value;

        // Enviar los datos actualizados al servidor mediante AJAX
        fetch('procesar_actualizar_usuario.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: `rut=${rut}&nombre_completo=${nombreCompleto}&correo_electronico=${correoElectronico}&tipo_usuario=${tipoUsuario}&estado=${estado}`
        })
        .then(response => response.text())
        .then(data => {
            alert(data); // Mostrar la respuesta del servidor
            document.getElementById('datos-usuario').style.display = 'none'; // Ocultar el div después de la actualización
            loadContent('usuarios_tabla.php', 'dynamic-content'); // Recargar la tabla de usuarios
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Error al actualizar el usuario.');
        });
    }
}