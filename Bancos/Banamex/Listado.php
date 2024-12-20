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
    <!-- Custom CSS -->
    <style>
        /* Estilos personalizados */
        body {
            background-color: #f4f7f6;
            font-family: 'Arial', sans-serif;
        }

        .navbar {
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .btn-primary {
            background-color: #007bff;
            border: none;
            transition: background-color 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #0056b3;
        }

        .table {
            animation: fadeIn 1s ease;
        }

        .table-hover tbody tr:hover {
            background-color: #f1f1f1;
            transition: background-color 0.3s ease;
        }

        /* Animación suave para la tabla */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }
            to {
                opacity: 1;
            }
        }

        /* Espacio entre las filas */
        .table-striped tbody tr {
            height: 50px;
        }

        /* Estilos del footer */
        footer {
            background-color: #333;
            color: white;
            padding: 10px;
            text-align: center;
            position: fixed;
            width: 100%;
            bottom: 0;
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include '../../navbar.php'; ?>

    <!-- Botón para regresar -->
    <div class="container mt-4 text-center">
        <a href="/index.php" class="btn btn-primary btn-lg">
            <i class="fas fa-arrow-left"></i> Home Page
        </a>
        <a href="ProcesarArchivo.php?Banco=Banamex" class="btn btn-success btn-lg">
            <i class="fas fa-arrow-left"></i> Registrar nuevo documento
        </a>
    </div>

    <!-- Tabla de registros -->
    <div class="container-fluid mt-5">
        <div class="container text-center">
        <img src="../../Back/Logos/BANAMEX_SINFONDO.PNG" alt="<?php echo $Banco ?>" class="img-fluid mb-4 w-25">
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
                    <th>Cliente</th>
                    <th>Acciones</th>
                </tr>
            </thead>
            <tbody class="text-center">
                <?php
                // Consulta para obtener los movimientos de Banistmo
                $sql = "SELECT * FROM movimientos_banamex WHERE Banco = 'Banamex'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    // Mostrar los registros
                    while ($row = $result->fetch_assoc()) {
                        // Dependiendo el color del estado, se mostrará en la tabla
                        if ($row['Estado'] == 'Pendiente') {
                            echo "<tr class='table-warning'>";
                        } elseif ($row['Estado'] == 'Capturado') {
                            echo "<tr class='table-success'>";
                        } else {
                            echo "<tr class='table-danger'>";
                        }
                        echo "<td>" . $row['Id'] . "</td>";
                        echo "<td>" . $row['Fecha'] . "</td>";
                        echo "<td>" . $row['Descripcion'] . "</td>";
                        // Mostrar el valor con formato de moneda
                        echo "<td>$" . number_format($row['Depositos'], 2) . "</td>";
                        echo "<td>$ " . number_format($row['Retiros'], 2) . "</td>";
                        echo "<td>$ " . number_format($row['Saldo'], 2) . "</td>";
                        echo "<td>" . $row['Fecha_Registro'] . "</td>";
                        echo "<td>" . $row['Responsable'] . "</td>";
                        echo "<td>" . $row['Banco'] . "</td>";
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
