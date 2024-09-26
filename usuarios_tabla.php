<?php
// Conexión a la base de datos
include 'config.php';

// Consulta para obtener los usuarios
$query = "SELECT rut, nombre_completo, tipo_usuario, correo_electronico, estado FROM usuarios"; 
$result = mysqli_query($conn, $query);

echo '<table>
    <thead>
        <tr>
            <th>RUT</th>
            <th>Nombre Completo</th>
            <th>Tipo de Usuario</th>
            <th class="email-column">Correo Electr\u00f3nico</th>
            <th>Estado</th> 
            <th>Acciones</th>
        </tr>
    </thead>
    <tbody>';

while ($row = mysqli_fetch_assoc($result)) {
    echo "<tr>";
    echo "<td>{$row['rut']}</td>";
    echo "<td>{$row['nombre_completo']}</td>";
    echo "<td>{$row['tipo_usuario']}</td>";
    echo "<td class='email-column'>{$row['correo_electronico']}</td>";
    echo "<td>{$row['estado']}</td>"; 
    echo "<td>
        <button class='btn' onclick=\"mostrarDatosUsuario('{$row['rut']}')\">Actualizar</button>
        <button class='btn' onclick=\"eliminarUsuario('{$row['rut']}')\">Borrar</button>
    </td>";
    echo "</tr>";
}

echo '</tbody>
</table>';

// Div para mostrar los datos del usuario a actualizar
echo '<div id="datos-usuario" style="display: none;"></div>';

mysqli_close($conn);
?>