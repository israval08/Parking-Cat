// script.js

document.addEventListener("DOMContentLoaded", function() {
    const hamburgerMenu = document.querySelector('.hamburger-menu');
    const navLinks = document.querySelector('.nav-links');

    hamburgerMenu.addEventListener('click', function() {
        navLinks.classList.toggle('show');
    });

    const toggleFooterBtn = document.getElementById('toggleFooterBtn');
    const footer = document.querySelector('footer');

    toggleFooterBtn.addEventListener('click', function() {
        footer.classList.toggle('footer-visible');
    });
});

function formatearRUT(rutInput) {
    // Eliminar puntos y guiones existentes
    let rut = rutInput.value.replace(/\./g, '').replace(/-/g, '');
    
    // Si tiene más de un dígito, insertamos el guion antes del dígito verificador
    if (rut.length > 1) {
        rut = rut.slice(0, -1) + '-' + rut.slice(-1);
    }
    
    // Insertar puntos según la longitud del RUT
    if (rut.length > 5) {
        rut = rut.slice(0, -5) + '.' + rut.slice(-5);
    }
    if (rut.length > 8) {
        rut = rut.slice(0, -9) + '.' + rut.slice(-9);
    }

    // Asignar el valor formateado al campo de texto
    rutInput.value = rut;
}
