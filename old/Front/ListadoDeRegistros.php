<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movimientos</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
</head>

<body>
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>
    <!-- Boton para regresar a la página anterior -->
    <div class="container mt-4">
        <a href="/index.php" class="btn btn-primary">Regresar</a>
    </div>
    <div class="container-fluid mt-5">
    <h2 class="mb-4 text-center">Registro de Movimientos</h2>
    <table class="table table-bordered table-hover table-responsive-lg table-striped">
        <thead class="thead-dark">
            <tr class="text-center">
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
            <tbody>
                <?php
                include '../Back/Config/config.php';
                    $Banco = $_GET['Banco'];
                    if ($Banco == NULL){
                        $Banco = 'Ninguno';
                    }
                switch ($Banco) {
                    case 'Banistmo':
                        $sql = "SELECT id, fecha, Estado, descripcion, debito, credito, saldo, Fecha_Registro, Registrante, Banco, Cliente FROM movimientos";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Definir la clase de color según si es crédito o débito
                                $claseFila = $row['debito'] ? 'table-success' : 'table-warning';
                        
                                echo "<tr class='text-center $claseFila'>"; // Aplicamos la clase de color a la fila
                                echo "<td>" . $row['fecha'] . "</td>";
                                echo "<td>" . $row['descripcion'] . "</td>";
                                echo "<td>" . ($row['debito'] ? $row['debito'] : "N/A") . "</td>";
                                echo "<td>" . ($row['credito'] ? $row['credito'] : "N/A") . "</td>";
                                echo "<td>" . $row['saldo'] . "</td>";
                                echo "<td>" . $row['Fecha_Registro'] . "</td>";
                                echo "<td>" . $row['Registrante'] . "</td>";
                                echo "<td>" . $row['Banco'] . "</td>";
                                echo "<td>" . $row['Cliente'] . "</td>";
                                //echo "<a href='capturar_pago.php?id_pago=" . $row['id'] . "' class='btn btn-primary'>Capturar Pago</a>";
                                if ($row['Estado'] == 'Pendiente'){
                                    echo "<td><a href='CapturarPago.php?id_pago=" . $row['id'] . "&Banco=Banistmo' class='btn btn-primary'>Capturar Pago</a></td>";
                                } elseif ($row['Estado'] == 'Capturado') {
                                    echo "<td><a href='detallado.php?id_pago=" . $row['id'] . "&Banco=Banistmo' class='btn btn-success '>Ver Información</a></td>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No records found</td></tr>";
                        }

                        break;
                    case 'Santander':
                        $sql = "SELECT * FROM movimientos_santander";
                        $result = $conn->query($sql);
                        if ($result->num_rows > 0) {
                            while ($row = $result->fetch_assoc()) {
                                // Definir la clase de color según si es crédito o débito
                                $claseFila = $row['Abono'] ? 'table-success' : 'table-warning';
                        
                                echo "<tr class='text-center $claseFila'>"; // Aplicamos la clase de color a la fila
                                echo "<td>" . $row['Fecha'] . "</td>";
                                echo "<td>" . $row['Descripcion'] . "</td>";
                                echo "<td>" . ($row['Abono'] ? $row['Abono'] : "N/A") . "</td>";
                                echo "<td>" . ($row['Cargo'] ? $row['Cargo'] : "N/A") . "</td>";
                                echo "<td>" . $row['Saldo'] . "</td>";
                                echo "<td>" . $row['Fecha_Registro'] . "</td>";
                                echo "<td>" . $row['Registrante'] . "</td>";
                                echo "<td>" . $Banco . "</td>";
                                echo "<td>" . $row['Cliente'] . "</td>";
                                /// Imprimir Información del cliente en un input por si se desea modificar
                                //echo "<td><input type='text' value='" . $row['Cliente'] . "'></td>";
                                //echo "<a href='capturar_pago.php?id_pago=" . $row['id'] . "' class='btn btn-primary'>Capturar Pago</a>";
                                if ($row['Estado'] == 'Pendiente'){
                                    echo "<td><a href='CapturarPago.php?id_pago=" . $row['Id'] . "&Banco=Santander' class='btn btn-primary'>Capturar Pago</a></td>";
                                } elseif ($row['Estado'] == 'Capturado') {
                                    echo "<td><a href='detallado.php?id_pago=" . $row['Id'] . "&Banco=Santander' class='btn btn-success '>Ver Información</a></td>";
                                }
                                echo "</tr>";
                            }
                        } else {
                            echo "<tr><td colspan='10'>No records found</td></tr>";
                        }
                        break;
                    
                case 'Banamex': 
                    $sql = "SELECT * FROM movimientos_banamex";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Definir la clase de color según si es crédito o débito
                            $claseFila = $row['Depositos'] ? 'table-success' : 'table-warning';
                    
                            echo "<tr class='text-center $claseFila'>"; // Aplicamos la clase de color a la fila
                            echo "<td>" . $row['Fecha'] . "</td>";
                            echo "<td>" . $row['Descripcion'] . "</td>";
                            echo "<td>" . ($row['Depositos'] ? $row['Depositos'] : "N/A") . "</td>";
                            echo "<td>" . ($row['Retiros'] ? $row['Retiros'] : "N/A") . "</td>";
                            echo "<td>" . $row['Saldo'] . "</td>";
                            echo "<td>" . $row['Fecha_Registro'] . "</td>";
                            echo "<td>" . $row['Responsable'] . "</td>";
                            echo "<td>" . $Banco . "</td>";
                            echo "<td>" . $row['Cliente'] . "</td>";
                            //echo "<a href='capturar_pago.php?id_pago=" . $row['id'] . "' class='btn btn-primary'>Capturar Pago</a>";
                            if ($row['Estado'] == 'Pendiente'){
                                echo "<td><a href='CapturarPago.php?id_pago=" . $row['Id'] . "&Banco=Banamex' class='btn btn-primary'>Capturar Pago</a></td>";
                            } elseif ($row['Estado'] == 'Capturado') {
                                echo "<td><a href='detallado.php?id_pago=" . $row['Id'] . "&Banco=Banamex' class='btn btn-success '>Ver Información</a></td>";
                            }
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='10'>No records found</td></tr>";
                    }
                    break;
                
                case 'BASE':
                    $sql = "SELECT * FROM movimientos_base";
                    $result = $conn->query($sql);
                    if ($result->num_rows > 0) {
                        while ($row = $result->fetch_assoc()) {
                            // Definir la clase de color según si es crédito o débito
                            $claseFila = $row['Cargo'] ? 'table-success' : 'table-warning';
                    
                            echo "<tr class='text-center $claseFila'>"; // Aplicamos la clase de color a la fila
                            echo "<td>" . $row['Fecha'] . "</td>";
                            echo "<td>" . $row['Operacion'] . "-" . $row['Destinatario'] . "</td>";
                            echo "<td>" . ($row['Cargo'] ? $row['Cargo'] : "N/A") . "</td>";
                            echo "<td>" . ($row['Abono'] ? $row['Abono'] : "N/A") . "</td>";
                            echo "<td>" . $row['Saldo'] . "</td>";
                            echo "<td></td>";
                            echo "<td>" . $row['Registrante'] . "</td>";
                            echo "<td>" . $Banco . "</td>";
                            
                            echo "<td>" . $row['Estado'] . "</td>";
                            //echo "<a href='capturar_pago.php?id_pago=" . $row['id'] . "' class='btn btn-primary'>Capturar Pago</a>";
                            if ($row['Estado'] == 'Pendiente'){
                                echo "<td><a href='CapturarPago.php?id_pago=" . $row['Id'] . "&Banco=BASE' class='btn btn-primary'>Capturar Pago</a></td>";
                            } elseif ($row['Estado'] == 'Capturado') {
                                echo "<td><a href='detallado.php?id_pago=" . $row['Id'] . "&Banco=BASE' class='btn btn-success '>Ver Información</a></td>";
                            }
                            echo "</tr>";
                        }
                        break;
                    } else {
                        echo "<tr><td colspan='10'>No records found</td></tr>";
                        break;
                    }

                }
                    
                
                // Close connection
                $conn->close();
                ?>
            </tbody>
        </table>
    </div>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
</body>

</html>