<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parking Cat</title>
    <link rel="stylesheet" href="./Estilos/styles.css">
    <link rel="stylesheet" href="./Estilos/contenedormapa.css"> <!-- Enlace al nuevo archivo CSS -->
    <link rel="preload" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.1.1/css/all.min.css" as="style">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500&display=swap" rel="stylesheet">
    <script src="script.js"></script>
</head>
<body>
    <!-- Navbar, contenido principal, y el resto del código -->
</body>
</html>
<body>
    <!-- Navbar -->
    <div class="hamburger-menu" aria-label="Toggle menu">☰</div>
    <nav class="navbar">
        <div class="logo">
            <img src="./imagenes/parking cat.jpg" alt="Logo">
        </div>
        <div class="nav-links">
            <a href="#home">Home</a>
            <a href="./recursos/quienessomos.php">Acerca de</a>
            <a href="./login.php">Inicio de sesión</a>
        </div>
    </nav>

    <!-- Contenido principal -->
    <header>
        <div class="container">
            <h1>Bienvenido a Parking Cat</h1>
        </div>
    </header>

    <!-- Contenedor del Mapa -->
    <div id="map-container">
        <?php include './mapa.php'; ?>
    </div>

    <!-- Footer desplegable -->
    <button id="toggleFooterBtn" class="footer-toggle-button">Más</button>
    <footer id="footer">
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
            <div class="footer-section legal">
                <p>&copy; 2024 Parking Cat | <a href="#privacy-policy">Política de Privacidad</a> | <a href="#terms">Términos y Condiciones</a></p>
            </div>
        </div>
    </footer>

    <script>
        // Función para desplegar y ocultar el footer
        const footer = document.getElementById('footer');
        const toggleFooterBtn = document.getElementById('toggleFooterBtn');
        
        toggleFooterBtn.addEventListener('click', () => {
            footer.classList.toggle('active');
        });

        function openContactForm() {
            window.open('./recursos/contacto.html', 'ContactForm', 'width=400,height=300');
        }
    </script>
</body>
</html>
