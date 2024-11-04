<?php
require '../../Back/Config/config.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Registro de Movimientos</title>
    <!-- Bootstrap CSS -->
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome (Opcional para iconos) -->
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.1/css/all.min.css" rel="stylesheet">
    <!-- CSS Personalizado -->
    <link rel="stylesheet" href="../style.css">
</head>

<body>
    <!-- Navbar -->
    <?php include '../../navbar.php'; ?>
    <br>

    <!-- Botón para regresar -->
    <div class="row text-center">
       
        <div class="col-md-6">
            <a href="../../index.php" class="btn btn-primary btn-lg">
                <i class="fas fa-arrow-left"></i> Inicio 
            </a>
        </div>
        <div class="col-md-6">
            <a href="ProcesarArchivo.php?Banco=Santander" class="btn btn-success btn-lg">
                <i class="fas fa-arrow-left"></i> Registrar nuevo documento
            </a>
        </div>
    </div>

    <!-- Tabla de registros -->
    <div class="container-fluid mt-5">
        <h2 class="mb-4 text-center">Registro de Movimientos</h2>
        <div class="container text-center">
        <img src="../../Back/Logos/SANTANDER.PNG" alt="<?php echo $Banco ?>" class="img-fluid mb-4 w-25">
    </div>
        <table class="table table-bordered table-hover table-responsive-lg table-striped">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Abono</th>
                    <th>Cargo</th>
                    <th>Saldo</th>
                    <th>Fecha de Registro</th>
                    <th>Registrante</th>
                    <th>Estado</th>
                    <th>Cliente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                // Consulta para obtener los movimientos de Santander
                $sql = "SELECT * FROM movimientos_santander";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Mostrar los registros
                    while ($row = $result->fetch_assoc()) {
                        // Dependiendo el color del estado, se mostrará en la tabla
                        if ($row['Estado'] == 'Pendiente') {
                            echo "<tr class='table-warning'>";
                        } elseif ($row['Estado'] == 'Procesado') {
                            echo "<tr class='table-success'>";
                        } else {
                            echo "<tr class='table-danger'>";
                        }

                        echo "<td>" . $row['Id'] . "</td>";
                        echo "<td>" . $row['Fecha'] . "</td>";
                        echo "<td>" . $row['Descripcion'] . "</td>";
                        // Formatear los montos a moneda
                        /// Si el monto DE Abono y/o Cargo es 0, Que se muestre "N/A"
                        if ($row['Abono'] == 0) {
                            $row['Abono'] = "N/A";
                            echo "<td>" . $row['Abono'] . "</td>";
                        } else {
                            echo "<td>$" . number_format($row['Abono'], 2) . "</td>";
                        }

                        if ($row['Cargo'] == 0) {
                            $row['Cargo'] = "N/A";
                            echo "<td>" . $row['Cargo'] . "</td>";
                        } else {
                            echo "<td>$" . number_format($row['Cargo'], 2) . "</td>";
                        }

                        echo "<td>$" . number_format($row['Saldo'], 2) . "</td>";
                        echo "<td>" . $row['Fecha_Registro'] . "</td>";
                        echo "<td>" . $row['Registrante'] . "</td>";
                        echo "<td>" . $row['Estado'] . "</td>";
                        echo "<td>" . $row['Cliente'] . "</td>";
                        echo "<td class='text-center'>
                                <a href='ProcesarPago.php?id=" . $row['Id'] . "' class='btn btn-primary'>
                                    <i class='fas fa-edit'></i> Procesar Pago
                                </a>
                            </td>";
                        echo "</tr>";
                    }
                    
                
                } else {
                    echo "<tr><td colspan='10' class='text-center'>No hay registros</td></tr>";
                }

                ?>
            </tbody>
        </table>
    </div>

    <br>
    <br>
    <br>

    <!-- Footer -->
    <footer>
        <p>&copy; 2024 ALEN INTELLIGENT</p>
    </footer>

    <!-- Bootstrap JS y jQuery -->
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@4.5.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
