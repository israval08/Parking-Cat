<?php
// Archivo de configuración (config.php)
$servername = "localhost";
$username = "root";
$password = "";  // Si no tienes contraseña, deja este campo vacío
$dbname = "sistema_estacionamiento";

// Crear conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("No se pudo conectar a la base de datos. Por favor, verifica tu conexión a internet y los datos de acceso.");
}

// No cierres la conexión aquí, ya que aún puede que necesites hacer consultas
// conn->close(); 
?>
