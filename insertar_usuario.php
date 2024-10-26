<?php
include 'config.php'; // Conexión a la base de datos

// Comprobar si se recibieron los parámetros correctos
if (isset($_POST['rut']) && isset($_POST['nombre']) && isset($_POST['email']) && isset($_POST['tipo_usuario']) && isset($_POST['contrasena'])) {
    // Recibir los datos enviados por el formulario
    $rut = $_POST['rut'];
    $nombre = $_POST['nombre'];
    $email = $_POST['email'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Encriptar la contraseña

    // Validar que los datos no estén vacíos
    if (!empty($rut) && !empty($nombre) && !empty($email) && !empty($tipo_usuario)) {
        // Verificar que no exista un usuario con el mismo RUT o correo
        $sql_check = "SELECT * FROM usuarios WHERE rut = ? OR correo_electronico = ?";
        $stmt_check = $conn->prepare($sql_check);
        $stmt_check->bind_param("ss", $rut, $email);
        $stmt_check->execute();
        $result_check = $stmt_check->get_result();

        if ($result_check->num_rows > 0) {
            echo "Error: Ya existe un usuario con ese RUT o correo electrónico.";
        } else {
            // Insertar el nuevo usuario en la base de datos
            $sql = "INSERT INTO usuarios (rut, nombre_completo, correo_electronico, tipo_usuario, contrasena, estado) 
                    VALUES (?, ?, ?, ?, ?, 'activo')";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sssss", $rut, $nombre, $email, $tipo_usuario, $contrasena);

            if ($stmt->execute()) {
                echo "Usuario insertado correctamente.";
            } else {
                echo "Error al insertar el usuario: " . $stmt->error;
            }

            $stmt->close();
        }

        $stmt_check->close();
    } else {
        echo "Error: Todos los campos son obligatorios.";
    }
} else {
    // Depuración: Imprimir los parámetros recibidos
    echo "Error: Parámetros no recibidos. Detalles de POST: ";
    echo '<pre>';
    print_r($_POST);
    echo '</pre>';
}

$conn->close();
?>
