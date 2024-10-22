<?php
// Asegúrate de incluir el autoloader de Composer

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar PDF</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-EVSTQN3/azprG1Anm3QDgpJLIm9Nao0Yz1ztcQTwFspd3yD65VohhpuuCOmLASjC" crossorigin="anonymous">
    <!-- Custom CSS for animations -->

</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>
    <div class="form-container p-4">
        <div class="container">
            <div class="row justify-content-center align-items-center text-center">
                <div class="col-6 col-sm-4 col-md-3">
                    <a href="Bancos/Banistmo/ProcesarArchivo.php?Banco=Banistmo" class="d-block btn-bank">
                        <img src="Back/Logos/banitsmo.png" alt="Banistmo" class="img-fluid btn-bank-img">
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3">
                    <a href="Front/procesador.php?Banco=Banamex" class="d-block btn-bank">
                        <img src="Back/Logos/Banamex.png" alt="Banamex" class="img-fluid btn-bank-img">
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3">
                    <a href="Front/ListadoDeRegistros.php?Banco=Santander" class="d-block btn-bank">
                        <img src="Back/Logos/santander.jpg" alt="Santander" class="img-fluid btn-bank-img">
                    </a>
                </div>
                <div class="col-6 col-sm-4 col-md-3">
                    <a href="Front/ListadoDeRegistros.php?Banco=BASE" class="d-block btn-bank">
                        <img src="Back/Logos/base.png" alt="BASE" class="img-fluid btn-bank-img">
                    </a>
                </div>
            </div>
        </div>
        <hr>



        <!-- Bootstrap JS (optional, for animations and interactivity) -->
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"
            integrity="sha384-IQsoLXl5PILFhosVNubq5LC7Qb9DXgDA9i+tQ8Zj3iwWAwPtgFTxbJ8NT4GN1R8p"
            crossorigin="anonymous"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.min.js"
            integrity="sha384-cVKIPhGWiC2Al4u+LWgxfKTRIcfu0JTxR+EQDz/bgldoEyl4H0zUF0QKbrJ0EcQF"
            crossorigin="anonymous"></script>


</body>

</html>