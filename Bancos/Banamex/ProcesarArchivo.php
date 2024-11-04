<?php

$Banco = $_GET['Banco'] ?? $_POST['Banco'] ?? 'Sin Selección';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

use Smalot\PdfParser\Parser;

if ($Banco == 'Banamex') {
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
        // Obtén el archivo subido
        $archivoCSV = $_FILES['pdf_file']['tmp_name'];

        /// Obtener Nombre del Archivo
        $nombreArchivo = $_FILES['pdf_file']['name'];

        //echo "Nombre del archivo: " . $nombreArchivo . "<br>";

        // Abre el archivo CSV para lectura
        if (($handle = fopen($archivoCSV, 'r')) !== false) {
            // Array para guardar los registros procesados
            $registros = [];
            $rowIndex = 0; // Contador de filas

            // Variables para almacenar los datos
            $sucursal = '';
            $cuenta = '';
            $moneda = '';
            $saldo_inicial = '';
            $saldo_final = '';
            $num_registros = 0; // Se tomará de la celda A9
            $headers = []; // Guardar encabezados para validar después
            $Cliente = '';

            // Leer cada fila del archivo CSV
            while (($datos = fgetcsv($handle, 1000, ",")) !== false) {
                $rowIndex++; // Incrementa el índice de fila

                // Leer los valores en función de la fila actual
                if ($rowIndex == 4) {
                    // Fila 4: Sucursal
                    $sucursal = $datos[1]; // Columna B
                } elseif ($rowIndex == 5) {
                    // Fila 5: Cuenta y Moneda
                    $cuenta = $datos[1]; // Columna B - Cuenta
                    $moneda = $datos[4]; // Columna E - Moneda
                } elseif ($rowIndex == 8) {
                    // Fila 8: Saldo Inicial y Saldo Final
                    $saldo_inicial = $datos[1]; // Columna B - Saldo Inicial
                    $saldo_final = $datos[4]; // Columna E - Saldo Final
                } elseif ($rowIndex == 9) {
                    // Fila 9: Depósitos (1)
                    preg_match('/\((\d+)\)/', $datos[0], $matches);
                    if (isset($matches[1])) {
                        $num_registros = (int) $matches[1]; // Número de registros
                    }
                } elseif ($rowIndex == 13) {
                    // Fila 13: Encabezados del listado de registros
                    $headers = $datos; // Guardamos los encabezados
                } elseif ($rowIndex >= 14) { // Comenzar a leer desde la fila 14 en adelante
                    if (count($datos) >= 5) { // Asegúrate de que tenga al menos las columnas necesarias
                        // Asignar valores a las variables con base en las columnas del CSV
                        $fecha = $datos[0]; // Columna A - Fecha
                        $descripcion = $datos[1]; // Columna B - Descripción
                        $depositos = $datos[2]; // Columna C - Depósitos
                        $retiros = $datos[3]; // Columna D - Retiros
                        $saldo = $datos[4]; // Columna E - Saldo
                
                        // Guardar los registros en el array
                        $registros[] = [
                            'fecha' => $fecha,
                            'descripcion' => $descripcion,
                            'depositos' => $depositos,
                            'retiros' => $retiros,
                            'saldo' => $saldo,
                            'moneda' => $moneda,
                            'archivo_procesado' => $nombreArchivo,
                            'Cliente' => $Cliente
                        ];
                    } else {
                        // Si encontramos una fila que no tiene suficientes columnas, dejamos de procesar
                        break;
                    }
                }
                
            }
            fclose($handle);
        }
    }
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $Banco ?></title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS -->
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }

        .navbar {
            margin: 0;
            padding: 0;
        }


        .form-control-file {
            margin-top: 10px;
        }

        .table-container {
            margin-top: 20px;
            background-color: white;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        textarea.form-control {
            height: 100px;
        }

        .btn-primary,
        .btn-success {
            width: 100%;
        }

        .table th,
        .table td {
            vertical-align: middle;
        }

        /* Efecto de hover y animación de zoom */
        .table tbody tr {
            transition: transform 0.2s, background-color 0.3s;
        }

        .table tbody tr:hover {
            transform: scale(1.03);
            /* Aumenta el tamaño de la fila */
            background-color: #f0f8ff;
            /* Cambia el color de fondo al pasar el mouse */
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
    <?php include '../../navbar.php'; ?>

    <div class="container mt-5">
        <div class="row justify-content-center">
            <!-- Formulario de procesamiento de archivo -->
            <div class="col-lg-5 col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body">
                        <h3 class="card-title text-center mb-4">Procesar archivo de <?php echo $Banco ?></h3>
                        <form action="" method="post" enctype="multipart/form-data">
                            <div class="form-group mb-4">
                                <label for="pdf_file" class="form-label">Seleccione el archivo a procesar</label>
                                <input type="file" class="form-control" name="pdf_file" id="pdf_file" required>
                            </div>
                            <input type="hidden" name="Banco" value="<?php echo $Banco ?>">
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary">Procesar</button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>

            <!-- Botón para visitar el listado -->
            <div class="col-lg-5 col-md-6 mb-4">
                <div class="card shadow-sm">
                    <div class="card-body text-center">
                    <img src="../../Back/Logos/BANAMEX_SINFONDO.PNG" alt="<?php echo $Banco ?>" class="img-fluid mb-4 w-25">
                        <h3 class="card-title mb-4">Visitar el listado de <?php echo $Banco ?></h3>
                        <a href="Listado.php?Banco=<?php echo $Banco ?>" class="btn btn-outline-primary btn-lg">Ver
                            Listado</a>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="container">
        <hr>

        <?php if ($Banco == 'Banamex' && !empty($registros)): ?>
            <div class="result-container">
                <h2>Registros Extraídos del PDF</h2>
                <form action="registro.php?Banco=Banamex" method="POST">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Depósitos</th>
                                <th>Saldo</th>
                                <th>Retiros</th>
                                <th>Moneda</th>
                                <th>Cliente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $key => $registro):
                                $FormattedDate = date("Y-m-d", strtotime($registro['fecha']));
                                ?>
                                <tr>
                                    <td><?php echo $FormattedDate ?></td>
                                    <td><?php echo htmlspecialchars($registro['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['depositos']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['saldo']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['retiros']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['moneda']); ?></td>
                                    <td>
                                        <!-- El nombre del campo Cliente será parte del array de registros -->
                                        <input type="text" name="registros[<?php echo $key; ?>][Cliente]"
                                            value="<?php echo isset($registro['Cliente']) ? htmlspecialchars($registro['Cliente']) : ''; ?>"
                                            class="form-control" required>
                                    </td>
                                </tr>
                                <input type="hidden" name="registros[<?php echo $key; ?>][fecha]"
                                    value="<?php echo $FormattedDate; ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][descripcion]"
                                    value="<?php echo htmlspecialchars($registro['descripcion']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][depositos]"
                                    value="<?php echo htmlspecialchars($registro['depositos']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][saldo]"
                                    value="<?php echo htmlspecialchars($registro['saldo']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][retiros]"
                                    value="<?php echo htmlspecialchars($registro['retiros']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][moneda]"
                                    value="<?php echo htmlspecialchars($registro['moneda']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]"
                                    value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">


                            <?php endforeach; ?>
                        </tbody>
                        <br>
                        <button type="submit" class="btn btn-success w-100">Enviar registros seleccionados</button>
                        <br>
                        <br>
                </form>
            </div>
        <?php endif; ?>
    </div>
        <!-- Footer -->
    <footer>
        <p>&copy; 2024 ALEN INTELLIGENT</p>
    </footer>

</body>



</html>