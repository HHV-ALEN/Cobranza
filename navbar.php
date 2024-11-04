<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Barra de navegación -->
<nav class="navbar navbar-expand-lg navbar-light bg-light custom-navbar">
    <div class="container">
        <!-- Toggler para dispositivos móviles -->
        <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
            <span class="navbar-toggler-icon"></span>
        </button>
        <!-- Items de navegación centrados -->
        <div class="collapse navbar-collapse justify-content-center" id="navbarNav">
            <ul class="navbar-nav">
                <!-- Home -->
                <li class="nav-item active">
                    <a class="nav-link" href="/dashboard.php">
                    <img src="/Back/Logos/home.PNG" alt="Home Ico">
                    Home</a>
                </li>
                <!-- Banistmo con logo -->
                <li class="nav-item">
                    <a class="nav-link" href="/Bancos/Banistmo/ProcesarArchivo.php?Banco=Banistmo">
                        <img src="/Back/Logos/BANISTMO.PNG" alt="Banistmo">
                        Banistmo
                    </a>
                </li>
                <!-- Banamex con logo -->
                <li class="nav-item">
                    <a class="nav-link" href="/Bancos/Banamex/ProcesarArchivo.php?Banco=Banamex">
                        <img src="/Back/Logos/BANAMEX_SINFONDO.PNG" alt="Banamex">
                        Banamex
                    </a>
                </li>
                <!-- Santander con logo -->
                <li class="nav-item">
                    <a class="nav-link" href="/Bancos/Santander/ProcesarArchivo.php?Banco=Santander">
                        <img src="/Back/Logos/SANTANDER.PNG" alt="Santander">
                        Santander
                    </a>
                </li>
                <!-- Base con logo -->
                <li class="nav-item">
                    <a class="nav-link" href="/Bancos/Base/ProcesarArchivo.php?Banco=Base">
                        <img src="/Back/Logos/BASE_SINFONDO.PNG" alt="Base">
                        Base
                    </a>
                </li>
                <!-- Guatemala con logo -->
                <li class="nav-item">
                    <a class="nav-link" href="/Bancos/Guatemala/ProcesarArchivo.php?Banco=Guatemala">
                        <img src="/Back/Logos/guatemala.png" alt="Guatemala">
                        Guatemala
                    </a>
            </ul>
        </div>
    </div>
</nav>

<!-- Estilos CSS personalizados -->
<style>
    /* Ajuste de altura de la barra de navegación */
    .custom-navbar {
        padding: 20px 0; /* Incrementa el padding vertical */
        font-size: 1.25rem; /* Incrementa el tamaño del texto */
    }

    /* Ajuste del tamaño de los íconos/logos */
    .navbar-nav img {
        height: 30px; /* Aumenta el tamaño de los logos */
        margin-right: 10px;
    }

    /* Efecto hover */
    .navbar-nav .nav-link {
        transition: transform 0.3s ease, color 0.3s ease; /* Transición suave */
    }

    /* Efecto hover: Cambia el color y la escala al pasar el mouse */
    .navbar-nav .nav-link:hover {
        color: #007bff; /* Cambia el color del texto al azul */
        transform: scale(1.1); /* Incrementa ligeramente el tamaño al hacer hover */
        text-shadow: 1px 1px 5px rgba(0, 0, 0, 0.2); /* Añade un efecto de sombra en el texto */
    }
</style>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>