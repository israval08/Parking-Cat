<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="./Estilos/styles.css">
</head>
<body>
    <footer>
        <div class="footer-content">
            <div class="footer-section about">
                <h4>Sobre Parking Cat</h4>
                <p>Un espacio para optimizar el proceso y la explotacion de tu empresa de parquimetro!!.</p>
            </div>
            <div class="recursos">
                <h4>Redes Sociales</h4>
                <ul>
                    <li><a href="#instagram"><i class="fa-brands fa-instagram"></i></a></li>
                    <li><a href="#facebook"><i class="fa-brands fa-facebook"></i></li>
                    <li><a href="#tiktok"><i class="fa-brands fa-tiktok"></i></li>
                </ul>
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
                    window.open('./recursos/contacto.html', 'ContactForm', 'width=400,height=300');
                }
            </script>
            <div class="footer-section legal">
                <p>&copy; 2024 Parking Cat | <a href="#privacy-policy">Política de Privacidad</a> | <a href="#terms">Términos y Condiciones</a></p>
            </div>
        </div>
    </footer>
</body>
</html>