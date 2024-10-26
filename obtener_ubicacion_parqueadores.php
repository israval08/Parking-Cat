<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Ubicación del Parqueador</title>
    <script>
        function iniciarEnvioUbicacion() {
            if (navigator.geolocation) {
                navigator.geolocation.watchPosition(enviarUbicacion, mostrarError, {
                    enableHighAccuracy: true,
                    maximumAge: 0
                });
            } else {
                alert("La geolocalización no está soportada en este navegador.");
            }
        }

        function enviarUbicacion(position) {
            const latitud = position.coords.latitude;
            const longitud = position.coords.longitude;

            // Enviar datos al servidor mediante una solicitud POST
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "enviar_ubicacion.php", true);
            xhr.setRequestHeader("Content-type", "application/x-www-form-urlencoded");
            xhr.send("latitud=" + latitud + "&longitud=" + longitud);
        }

        function mostrarError(error) {
            switch (error.code) {
                case error.PERMISSION_DENIED:
                    alert("Permiso denegado para obtener la ubicación.");
                    break;
                case error.POSITION_UNAVAILABLE:
                    alert("La información de ubicación no está disponible.");
                    break;
                case error.TIMEOUT:
                    alert("La solicitud para obtener la ubicación ha expirado.");
                    break;
                case error.UNKNOWN_ERROR:
                    alert("Ha ocurrido un error desconocido.");
                    break;
            }
        }
    </script>
</head>
<body onload="iniciarEnvioUbicacion()">
    <h1>Ubicación del Parqueador</h1>
    <p>Esta página está enviando tu ubicación en tiempo real al servidor para que el administrador pueda monitorear tu posición en el mapa.</p>
</body>
</html>
