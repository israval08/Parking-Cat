<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nosotros</title>
    <link rel="stylesheet" href="../Estilos/styles.css">
    
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    
    <script src="../script.js"></script>
</head>
<body>
    <!-- Navbar -->
    <div class="hamburger-menu" aria-label="Toggle menu">☰</div>
    <nav class="navbar">
        <div class="logo">
            <img src="../imagenes/parking cat.jpg" alt="Logo"> <!-- Asegúrate de que la ruta sea correcta -->
        </div>
        <div class="nav-links">
            <a href="../index.php">Home</a>
           
            <a href="../login.php">Inicio de sesión</a>
        </div>
    </nav>
 
   <!-- Contenido principal -->
    <header>
        <div class="container">
            <h1>Bienvenido a Parking Cat</h1>
         
        </div>
    </header>
    <br>



    <div class="container2">
        <div class="left-content">
            <h3> ¿Que es Parking Cat? </h3>
            <p>
                Bienvenido a **Parking Cat**, tu solución integral para la gestión de estacionamientos en entornos urbanos. Nuestro sistema está diseñado para facilitar la vida tanto de usuarios que buscan un lugar seguro para estacionar su vehículo, como de administradores que requieren un control eficiente de los espacios de parqueo disponibles.
            </p>
            
            <p>
                **Parking Cat** permite a los parqueadores manejar el sistema de cobros de estacionamiento en calles específicas, optimizando el uso del tiempo. Los encargados y administradores tienen acceso a herramientas avanzadas para gestionar tarifas, supervisar la ocupación de los estacionamientos y mantener actualizados los datos de usuarios y ubicaciones.
            </p>
            
            <p>
                Utilizando tecnologías modernas y una base de datos robusta, **Parking Cat** asegura que todos los datos estén seguros y accesibles en tiempo real. Nuestro objetivo es mejorar la movilidad en la ciudad, reducir el tiempo de búsqueda de parqueo y ofrecer una experiencia fluida y segura para todos nuestros usuarios.
            </p>
            
        </div>
        <div class="right-content logo-container">
            <img src="../imagenes/parking cat.jpg" alt="Logo Parking Cat"> <!-- Asegúrate de que la ruta sea correcta -->
        </div>
    </div>
    
<br>


    <!-- Footer -->
    <button id="toggleFooterBtn" class="footer-toggle-button">Más</button>
    <footer>
        <div class="footer-content">
            <div class="footer-section about">
                <h4>Sobre Parking Cat</h4>
                <p>Un espacio para optimizar el proceso y la explotacion de tu empresa de parquimetro!!.</p>
            </div>
            <div class="recursos">
                <ol>
                    <li><a href="#instagram"><i class="fa-brands fa-instagram"></i></a></li>
                    <li><a href="#facebook"><i class="fa-brands fa-facebook"></i></li>
                    <li><a href="#tiktok"><i class="fa-brands fa-tiktok"></i></li>
                </ol>
            </div>
            <div class="footer-section newsletter">
                <h4>Suscríbete a nuestro boletín</h4>
                <form action="#">
                    <input type="email" placeholder="Tu correo electrónico">
                    <button type="submit">Suscribirse</button>
                </form>
            </div>
            <div class="footer-section contact">
                <h4>Contacto</h4>
                <p>Correo: parkingcat_contacto@parkingcat.com</p>
                <p>Teléfono: +123 456 7890</p>
                <p><a href="javascript:void(0);" onclick="openContactForm()">Formulario de Contacto</a></p>
            </div>
            
            <script>
                function openContactForm() {
                    window.open('./contacto.php', 'ContactForm', 'width=400,height=300');
                }
            </script>
            
            <div class="footer-section legal">
                <p>&copy; 2024 Parking Cat | <a href="#privacy-policy">Política de Privacidad</a> | <a href="#terms">Términos y Condiciones</a></p>
            </div>
        </div>
    </footer>
</body>
</html>
