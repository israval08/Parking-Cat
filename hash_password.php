<?php
// La contraseña que quieres cifrar
$contraseña = 'Felipeaurora05$';

// Genera el hash de la contraseña
$hash = password_hash($contraseña, PASSWORD_DEFAULT);

// Muestra el hash para que puedas insertarlo en la base de datos
echo "El hash generado es: " . $hash;
?>
