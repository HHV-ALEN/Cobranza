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
            <a href="ProcesarArchivo.php?Banco=Banistmo" class="btn btn-success btn-lg">
                <i class="fas fa-arrow-left"></i> Registrar nuevo documento
            </a>
        </div>
    </div>

    <!-- Tabla de registros -->
    <div class="container-fluid mt-5">
        <h2 class="mb-4 text-center">Registro de Movimientos</h2>
        <div class="container text-center">
        <img src="../../Back/Logos/BANISTMO.PNG" alt="<?php echo $Banco ?>" class="img-fluid mb-4 w-25">
    </div>
        <table class="table table-bordered table-hover table-responsive-lg table-striped">
            <thead class="thead-dark">
                <tr class="text-center">
                    <th>ID</th>
                    <th>Fecha</th>
                    <th>Descripción</th>
                    <th>Débito</th>
                    <th>Crédito</th>
                    <th>Saldo</th>
                    <th>Fecha de Registro</th>
                    <th>Registrante</th>
                    <th>Banco</th>
                    <th>Estado</th>
                    <th>Cliente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                // Consulta para obtener los movimientos de Banistmo
                $sql = "SELECT * FROM movimientos WHERE Banco = 'Banistmo'";
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
                        echo "<td>" . $row['id'] . "</td>";
                        echo "<td>" . $row['fecha'] . "</td>";
                        echo "<td>" . $row['descripcion'] . "</td>";
                        // Formatear los montos a moneda
                        echo "<td>$" . number_format($row['debito'], 2) . "</td>";
                        echo "<td>$" . number_format($row['credito'], 2) . "</td>";
                        echo "<td>$" . number_format($row['saldo'], 2) . "</td>";
                        echo "<td>" . $row['Fecha_Registro'] . "</td>";
                        echo "<td>" . $row['Registrante'] . "</td>";
                        echo "<td>" . $row['Banco'] . "</td>";
                        echo "<td>" . $row['Estado'] . "</td>";
                        echo "<td>" . $row['Cliente'] . "</td>";
                        echo "<td class='text-center'>
                                <a href='ProcesarPago.php?id=" . $row['id'] . "' class='btn btn-primary'>
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
