<?php
// Asegúrate de incluir el autoloader de Composer
include 'Back/Config/config.php';


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
    <style>
        body {
            font-family: 'Arial', sans-serif;
        }

        .carrusel-imagen {
            height: 500px;
            object-fit: cover;
        }

        .info-box {
            background-color: #f8f9fa;
            padding: 20px;
            border-radius: 10px;
        }

        .tarjeta {
            transition: 0.3s;
        }

        .tarjeta:hover {
            transform: scale(1.05);
        }
    </style>

</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>
    <!-- Carrusel -->
    <div id="carruselPaisajes" class="carousel slide" data-bs-ride="carousel">
        <div class="carousel-inner">
            <div class="carousel-item active">
                <img src="Back/Logos/fondo1.jpg" class="d-block w-100 carrusel-imagen" alt="Paisaje 1">
            </div>
            <div class="carousel-item">
                <img src="Back/Logos/fondo2.jpg" class="d-block w-100 carrusel-imagen" alt="Paisaje 2">
            </div>
            <div class="carousel-item">
                <img src="Back/Logos/fondo3.jpg" class="d-block w-100 carrusel-imagen" alt="Paisaje 3">
            </div>
        </div>
        <button class="carousel-control-prev" type="button" data-bs-target="#carruselPaisajes" data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Anterior</span>
        </button>
        <button class="carousel-control-next" type="button" data-bs-target="#carruselPaisajes" data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Siguiente</span>
        </button>
    </div>

    <!-- Sección de información financiera -->
    <section class="container mt-5">
        <div class="row text-center">
            <div class="col-md-4">
                <div class="info-box shadow-sm">
                    <?php
                    $Sql = "SELECT
                        SUM(AbonosTotales) AS TotalAbonos,
                        SUM(CargosTotales) AS TotalCargos
                    FROM (
                        -- Movimiento Santander: Suma de Abono y Cargo
                        SELECT 
                            SUM(Abono) AS AbonosTotales,
                            SUM(Cargo) AS CargosTotales
                        FROM movimientos_santander
                        WHERE WEEK(Fecha_Registro) = WEEK(NOW()) 
                        AND YEAR(Fecha_Registro) = YEAR(NOW())
                        
                        UNION ALL
                        
                        -- Movimiento Base: Suma de Abono y Cargo
                        SELECT 
                            SUM(Abono) AS AbonosTotales,
                            SUM(Cargo) AS CargosTotales
                        FROM movimientos_base
                        WHERE WEEK(Fecha_Registro) = WEEK(NOW()) 
                        AND YEAR(Fecha_Registro) = YEAR(NOW())
                        
                        UNION ALL

                        -- Movimiento Banamex: Suma de Depositos y Retiros
                        SELECT 
                            SUM(Depositos) AS AbonosTotales,
                            SUM(Retiros) AS CargosTotales
                        FROM movimientos_banamex
                        WHERE WEEK(Fecha_Registro) = WEEK(NOW()) 
                        AND YEAR(Fecha_Registro) = YEAR(NOW())

                        UNION ALL

                        -- Movimiento: Suma de Deposito y Credito
                        SELECT 
                            SUM(debito) AS AbonosTotales,
                            SUM(credito) AS CargosTotales
                        FROM movimientos
                        WHERE WEEK(Fecha_Registro) = WEEK(NOW()) 
                        AND YEAR(Fecha_Registro) = YEAR(NOW())
                    ) AS Totales";

                    $Sql = mysqli_query($conn, $Sql);
                    $Total = mysqli_fetch_array($Sql);

                    //print_r($Total);
                    $TotalAbonos = $Total['TotalAbonos'];
                    echo "<h5>Abonos totales</h5>";
                    echo "<h2>$" . number_format($TotalAbonos, 2) . " MXN</h2>";
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box shadow-sm">
                    <?php
                    // Obtener el total de Monto de Alta (Abonos)
                    $TotalCargos = $Total['TotalCargos'];
                    echo "<h5>Cargos totales</h5>";
                    echo "<h2>$" . number_format($TotalCargos, 2) . " MXN</h2>";
                    ?>
                </div>
            </div>
            <div class="col-md-4">
                <div class="info-box shadow-sm">
                    <h5>Pagos pendientes</h5>
                    <h2>24%</h2>
                    <p>Porcentaje de cuentas en espera de pago</p>
                </div>
            </div>
        </div>
    </section>
    <!-- Sección de resumen de cobranza -->
    <section class="container mt-5">
        <div class="row text-center">
            <div class="col-md-6">
                <div class="card tarjeta shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Cuentas por cobrar</h5>
                        <p class="card-text">Total de cuentas por cobrar en el sistema: <strong>$1,500,000 MXN</strong>
                        </p>
                        <a href="#" class="btn btn-primary">Ver más</a>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="card tarjeta shadow-sm">
                    <div class="card-body">
                        <h5 class="card-title">Última actualización de pagos</h5>
                        <p class="card-text">Última actualización: <strong>15 Octubre 2024</strong></p>
                        <a href="#" class="btn btn-primary">Ver detalles</a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="text-center mt-5 py-4 bg-dark text-white">
        <p>&copy; 2024 Sistema de Cobranza - ALEN - Todos los derechos reservados.</p>
    </footer>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>


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