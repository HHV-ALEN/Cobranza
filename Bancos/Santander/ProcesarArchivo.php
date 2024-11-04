<?php

$Banco = $_GET['Banco'] ?? $_POST['Banco'] ?? 'Sin Selección';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

use Smalot\PdfParser\Parser;

if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
    $fileType = mime_content_type($_FILES['pdf_file']['tmp_name']);

    if ($fileType == 'text/plain' || $fileType == 'text/csv') {
        $archivoCSV = $_FILES['pdf_file']['tmp_name'];

        if (($handle = fopen($archivoCSV, 'r')) !== false) {
            fgetcsv($handle); // Saltar la primera fila de encabezados
            $registros = [];

            while (($datos = fgetcsv($handle, 1000, ",")) !== false) {
                if (count($datos) >= 15) {
                    $fecha = $datos[1];

                    // Eliminar caracteres que no sean dígitos
                    $fecha = preg_replace('/\D/', '', $fecha); // Solo deja los números

                    // Intentar formatearla como 'dmy'
                    $fechaObj = DateTime::createFromFormat('dmY', $fecha);

                    if ($fechaObj !== false) {
                        // Si es válida, formatearla a 'Y-m-d'
                        $fechaFormateada = $fechaObj->format('Y-m-d');
                    } else {
                        // Si no es válida, asignar un valor por defecto o manejar el error
                        $fechaFormateada = 'Fecha no válida';
                    }

                    // Ahora puedes usar $fechaFormateada
                    //echo $fechaFormateada;
                    $descripcion = $datos[4];
                    $CargoAbono = trim($datos[5]); // Verificar que el valor sea '-' o '+'
                    $importe = str_replace(['$', ','], '', $datos[6]); // Eliminar símbolos
                    $saldo = str_replace(['$', ','], '', $datos[7]);
                    $referencia = $datos[8];
                    $concepto = $datos[9];
                    $nombreOrdenante = $datos[14];
                    $archivo_procesado = $_FILES['pdf_file']['name'];
                    

                    $registros[] = [
                        'fecha' => $fecha,
                        'descripcion' => $descripcion,
                        'CargoAbono' => $CargoAbono,
                        'importe' => $importe,
                        'saldo' => $saldo,
                        'referencia' => $referencia,
                        'concepto' => $concepto,
                        'nombre_ordenante' => $nombreOrdenante,
                        'archivo_procesado' => $archivo_procesado
                    ];
                }
            }


            fclose($handle);
            //echo "<pre>";
            //print_r($registros);
            //echo "</pre>";
        } else {
            echo "No se pudo abrir el archivo CSV.";
        }
    } else {
        echo "El archivo debe ser un archivo CSV válido.";
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
                                <label for="pdf_file" class="form-label">Seleccione el archivo CSV a procesar</label>
                                <input type="file" class="form-control" name="pdf_file" id="pdf_file" accept=".csv"
                                    required>
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
                        <!-- Mostrar Logo del banco -->
                        <img src="../../Back/Logos/SANTANDER.PNG" alt="<?php echo $Banco ?>"
                            class="img-fluid mb-4 w-25">
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
        <?php if ($Banco == 'Santander' && !empty($registros)): ?>
            <div class="table-container text-center">
                <h2>Registros Extraídos del PDF</h2>
                <hr>
                <form action="Registro.php?Banco=Banistmo" method="post">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                                <th>Cliente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $key => $registro): ?>
                                <?php
                                // Conversión de la fecha
                                $fechaOriginal = $registro['fecha'];
                                $fechaObj = DateTime::createFromFormat('dmY', $fechaOriginal);
                                $fechaFormateada = $fechaObj ? $fechaObj->format('Y-m-d') : '';

                                // Formatear los montos de "importe" y "saldo"
                                $importe = number_format($registro['importe'], 2);
                                $saldo = number_format($registro['saldo'], 2);

                                // Determinar el color de fondo basado en "CargoAbono"
                                $rowClass = ($registro['CargoAbono'] == '-') ? 'bg-warning' : 'bg-success';
                                //$importeFormateado = ($registro['CargoAbono'] == '-') ? '-$' . $importe : '+$' . $importe;
                                // El importe solo sera formateado a un numero float
                                $importeFormateado = $importe;
                                // El saldo solo sera formateado a un numero float, sin el signo de $
                                $saldo = str_replace('$', '', $saldo);
                               

                                ?>
                                <tr class="<?php echo $rowClass; ?>">
                                    <td>
                                        <input type="date" name="registros[<?php echo $key; ?>][fecha]"
                                            value="<?php echo htmlspecialchars($fechaFormateada); ?>" class="form-control"
                                            required>
                                        <input type="hidden" name="registros[<?php echo $key; ?>][fecha_original]"
                                            value="<?php echo htmlspecialchars($fechaFormateada); ?>">
                                    </td>
                                    <td>
                                        <textarea name="registros[<?php echo $key; ?>][descripcion]" class="form-control"
                                            required><?php echo htmlspecialchars($registro['descripcion']); ?></textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="registros[<?php echo $key; ?>][monto]"
                                            value="<?php echo htmlspecialchars($importeFormateado); ?>" class="form-control"
                                            required>
                                    </td>
                                    <td>
                                        <input type="text" name="registros[<?php echo $key; ?>][saldo]"
                                            value="<?php echo htmlspecialchars($saldo); ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="text" placeholder="Ingresa nombre del cliente..."
                                            name="registros[<?php echo $key; ?>][Cliente]"
                                            value="<?php echo isset($registro['nombre_ordenante']) ? htmlspecialchars($registro['nombre_ordenante']) : ''; ?>"
                                            class="form-control" required>
                                    </td>
                                    
                                </tr>
                                <input type="hidden" name="registros[<?php echo $key; ?>][CargoAbono]"
                                    value="<?php echo htmlspecialchars($registro['CargoAbono']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]"
                                    value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                    <button type="submit" class="btn btn-success mt-3">Enviar registros seleccionados</button>
                </form>
            </div>
        <?php endif; ?>
    </div>
    <br>
    <br>
    <br>
    <br>
    <!-- Footer -->
    <footer>
        <p>&copy; 2024 ALEN INTELLIGENT</p>
    </footer>

</body>



</html>