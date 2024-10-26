<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./Estilos/styles.css">
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="script.js" defer></script> <!-- Asegúrate de que el JS esté cargado después del HTML -->
</head>
<body>
    <div class="hamburger-menu" aria-label="Toggle menu">☰</div>
    <nav class="navbar">
        <div class="logo">
            <img src="./imagenes/parking cat.jpg" alt="Logo">
        </div>
        <div class="nav-links">
            <a href="index.php">Home</a>
            <a href="./recursos/quienessomos.php">Acerca de</a>
            <?php if (isset($_SESSION['rut'])): ?>
                <a href="logout.php">Salir de la sesión</a>
            <?php else: ?>
                <a href="login.php">Inicio de sesión</a>
            <?php endif; ?>
        </div>
    </nav>
</body>
</html>
