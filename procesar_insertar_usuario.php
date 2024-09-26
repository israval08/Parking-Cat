<?php
session_start();
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'administrador') {
    echo "Acceso denegado.";
    exit();
}

// Conexión a la base de datos
include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = $_POST['rut'];
    $nombre_completo = $_POST['nombre_completo'];
    $correo = $_POST['correo_electronico'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $contrasena = password_hash($_POST['contrasena'], PASSWORD_DEFAULT); // Encriptar contraseña

    // Insertar el usuario en la base de datos
    $sql = "INSERT INTO usuarios (rut, nombre_completo, tipo_usuario, correo_electronico, contrasena)
            VALUES (?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('sssss', $rut, $nombre_completo, $tipo_usuario, $correo, $contrasena);

    if ($stmt->execute()) {
        echo "Usuario insertado con éxito.";
    } else {
        echo "Error al insertar usuario: " . $conn->error;
    }

    $stmt->close();
    $conn->close();
}
?>
