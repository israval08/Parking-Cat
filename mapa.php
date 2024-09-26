<!DOCTYPE html>
<html lang="es">
<head>
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.7.1/dist/leaflet.css" />
    <style>
        #map {
            height: 400px; /* Ajusta la altura del mapa */
            width: 100%;    /* El mapa ocupará el ancho completo de su contenedor */
        }

        @media (max-width: 768px) {
            #map {
                height: 150px; /* Reduce la altura del mapa en pantallas más pequeñas */
            }
        }
    </style>
</head>
<body>
    <div id="map"></div>

    <script src="https://unpkg.com/leaflet@1.7.1/dist/leaflet.js"></script>
    <script>
        // Inicializar el mapa con las coordenadas de la Plaza de Armas de Curicó
        var map = L.map('map').setView([-34.9828, -71.2394], 15);

        // Añadir una capa de OpenStreetMap al mapa
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors'
        }).addTo(map);

        // Añadir un marcador en la Plaza de Armas de Curicó
        L.marker([-34.9828, -71.2394]).addTo(map)
            .bindPopup('Plaza de Armas de Curicó')
            .openPopup();
    </script>
</body>
</html>
