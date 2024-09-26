<?php
session_start();
if (!isset($_SESSION['rut']) || $_SESSION['tipo_usuario'] != 'administrador') {
    echo json_encode(array('error' => 'Acceso denegado.'));
    exit();
}

include 'config.php';

if ($_SERVER['REQUEST_METHOD'] == 'GET' && isset($_GET['rut'])) {
    $rut = $_GET['rut'];

    try {
        // Consulta para obtener los datos del usuario
        $sql = "SELECT * FROM usuarios WHERE rut = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('s', $rut);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows == 1) {
            $row = $result->fetch_assoc();
            echo json_encode($row); 
        } else {
            echo json_encode(array('error' => 'Usuario no encontrado.'));
        }

        $stmt->close();
    } catch (Exception $e) {
        echo json_encode(array('error' => 'Error al cargar los datos del usuario: ' . $e->getMessage()));
    }
}

$conn->close();
?>