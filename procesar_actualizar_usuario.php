<?php
session_start();
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'administrador') {
    echo "Acceso denegado.";
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $rut = $_POST['rut'];
    $nombre_completo = $_POST['nombre_completo'];
    $correo = $_POST['correo_electronico'];
    $tipo_usuario = $_POST['tipo_usuario'];
    $estado = $_POST['estado']; 

    try {
        // Consulta UPDATE
        $sql = "UPDATE usuarios SET nombre_completo = ?, correo_electronico = ?, tipo_usuario = ?, estado = ? WHERE rut = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('sssss', $nombre_completo, $correo, $tipo_usuario, $estado, $rut); 

        if ($stmt->execute()) {
            echo "Usuario actualizado correctamente."; 
        } else {
            echo "Error al actualizar usuario: " . $stmt->error; 
        }

        $stmt->close();
    } catch (Exception $e) {
        echo "Error en la actualización: " . $e->getMessage();
    }
}

$conn->close();
?>