<?php
session_start();
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'administrador') {
    echo "Acceso denegado.";
    exit();
}
?>
<script>
    function formatearRUT(input) {
        let rut = input.value.replace(/\./g, '').replace('-', '');
        if (rut.length > 1) {
            rut = rut.slice(0, -1) + '-' + rut.slice(-1); // Agrega el guion
        }
        if (rut.length > 4) {
            rut = rut.slice(0, -5) + '.' + rut.slice(-5); // Agrega el primer punto
        }
        if (rut.length > 8) {
            rut = rut.slice(0, -9) + '.' + rut.slice(-9); // Agrega el segundo punto
        }
        input.value = rut;
    }
</script>

<h2>Insertar Usuario</h2>
<form action="procesar_insertar_usuario.php" method="POST">
    <div>
        <label for="rut">RUT:</label>
        <input type="text" id="rut" name="rut" maxlength="12" oninput="formatearRUT(this)" required>
    </div>
    <div>
        <label for="nombre_completo">Nombre Completo:</label>
        <input type="text" id="nombre_completo" name="nombre_completo" required>
    </div>
    <div>
        <label for="tipo_usuario">Tipo de Usuario:</label>
        <select id="tipo_usuario" name="tipo_usuario" required>
            <option value="parqueador">Parqueador</option>
            <option value="encargado">Encargado</option>
        </select>
    </div>
    <div>
        <label for="correo_electronico">Correo Electrónico:</label>
        <input type="email" id="correo_electronico" name="correo_electronico" required>
    </div>
    <div>
        <label for="contrasena">Contraseña:</label>
        <input type="password" id="contrasena" name="contrasena" required>
    </div>
    <div>
        <button type="submit">Insertar Usuario</button>
    </div>
</form>
