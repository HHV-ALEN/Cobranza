<?php

$Banco = $_GET['Banco'] ?? $_POST['Banco'] ?? 'Sin Selección';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

use Smalot\PdfParser\Parser;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $pdfFile = $_FILES['pdf_file']['tmp_name'];
        $nombreArchivo = $_FILES['pdf_file']['name'];

        // Inicializar el parser de PDF
        $parser = new Parser();

        try {
            // Parsear el archivo PDF
            $pdf = $parser->parseFile($pdfFile);

            // Extraer el texto
            $textoExtraido = $pdf->getText();

            // Procesar el texto para extraer los registros
            $lineas = explode("\n", $textoExtraido);

            foreach ($lineas as $linea) {
                //echo $linea . '<br>';

                // Revisamos si la línea contiene un registro con formato de fecha al inicio
                if (preg_match('/^\d{4}-\d{2}-\d{2}/', $linea)) {
                    // Usamos una expresión regular para identificar y capturar las partes relevantes
                    if (preg_match('/^(\d{4}-\d{2}-\d{2})\s+(.+?)\s+([\d,]+\.\d{2})\s+([\d,]+\.\d{2})$/', $linea, $matches)) {
                        // Asignamos las capturas a las variables
                        $fecha = $matches[1];
                        $descripcion = $matches[2];
                        $monto = $matches[3];
                        $saldo = $matches[4];

                        // Quitamos las comas de los montos y saldos para almacenarlos correctamente como números
                        $monto = str_replace(',', '', $monto);
                        $saldo = str_replace(',', '', $saldo);

                        // Guardar el registro con los datos capturados y el nombre del archivo procesado
                        $registros[] = [
                            'fecha' => $fecha,
                            'descripcion' => trim($descripcion),
                            'monto' => $monto,  // Monto procesado sin comas
                            'saldo' => $saldo,  // Saldo procesado sin comas
                            'archivo_procesado' => $nombreArchivo
                        ];
                    }
                }

                // Buscar y almacenar saldo inicial y final
                if (strpos($linea, 'SALDO INICIAL') !== false) {
                    preg_match('/SALDO INICIAL : ([\d,.]+)/', $linea, $matches);
                    if (isset($matches[1])) {
                        $saldoInicial = $matches[1];
                    }
                }
                if (strpos($linea, 'SALDO FINAL') !== false) {
                    preg_match('/SALDO FINAL : ([\d,.]+)/', $linea, $matches);
                    if (isset($matches[1])) {
                        $saldoFinal = $matches[1];
                    }
                }
            }
        } catch (Exception $e) {
            // Manejar el error si ocurre
            echo 'Error al procesar el PDF: ' . $e->getMessage();
        }
    } else {
        echo 'Error: No se pudo subir el archivo o no se seleccionó ninguno.';
    }
} else {
    //echo 'Error: No se recibió un archivo PDF.';
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
            background-color: #f8f9fa;
        }

        .container {
            margin-top: 30px;
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
    </style>
</head>

<body>
    <?php include '../../navbar.php'; ?>
    <div class="row">
        <div class="col-md-12 table-container  text-center">
            <div class="row justify-content-center">
                <div class="col-md-6">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                        <h1 class="my-4">Procesar archivo de <?php echo $Banco ?></h1>
                            <label for="file" class="form-label">Seleccione el archivo a procesar</label>
                            <input type="file" class="form-control-file form-control" name="pdf_file" id="pdf_file">
                        </div>
                        <input type="hidden" name="Banco" value="<?php echo $Banco ?>">
                        <button type="submit" class="btn btn-primary">Procesar</button>
                    </form>
                </div>

                <div class="col-md-6">
                    <form action="" method="post" enctype="multipart/form-data">
                        <div class="form-group mb-3">
                        <h1 class="my-">Visitar el listado de <?php echo $Banco ?></h1>
                        <a href="Listado.php?Banco=<?php echo $Banco ?>" class="btn btn-primary">Listado</a>
                        </div>
                    </form>
                </div>

            </div>
        </div>
        <br>
    </div>
    <div class="container">
        <hr>

        <?php if ($Banco == 'Banistmo' && !empty($registros)) : ?>
            <div class="table-container text-center">
                <h2>Registros Extraídos del PDF</h2>
                <hr>
                <form action="Registro.php?Banco=Banistmo" method="post">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Crédito</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                                <th>Cliente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $key => $registro) : ?>
                                <?php
                                $fechaOriginal = $registro['fecha'];
                                $fechaObj = DateTime::createFromFormat('dmY', $fechaOriginal);
                                $fechaFormateada = $fechaObj ? $fechaObj->format('Y-m-d') : $fechaOriginal;
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="registros_credito[]" value="<?php echo $key; ?>">
                                    </td>
                                    <td>
                                        <input type="date" name="registros[<?php echo $key; ?>][fecha]" value="<?php echo $fechaFormateada; ?>" class="form-control" required>
                                        <input type="hidden" name="registros[<?php echo $key; ?>][fecha_original]" value="<?php echo htmlspecialchars($fechaOriginal); ?>">
                                    </td>
                                    <td>
                                        <textarea name="registros[<?php echo $key; ?>][descripcion]" class="form-control" required><?php echo htmlspecialchars($registro['descripcion']); ?></textarea>
                                    </td>
                                    <td>
                                        <input type="text" name="registros[<?php echo $key; ?>][monto]" value="<?php echo "$" . htmlspecialchars($registro['monto']); ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="text" name="registros[<?php echo $key; ?>][saldo]" value="<?php echo "$" . htmlspecialchars($registro['saldo']); ?>" class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="text" placeholder="Ingresa nombre del cliente..." name="registros[<?php echo $key; ?>][Cliente]" value="<?php echo isset($registro['Cliente']) ? htmlspecialchars($registro['Cliente']) : ''; ?>" class="form-control" required>
                                    </td>
                                </tr>
                                <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]" value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <button type="submit" class="btn btn-success mt-3">Enviar registros seleccionados</button>
                </form>
            </div>
        <?php endif; ?>
    </div>

    <!-- Mostrar una tabla de solo 10 registros para mostrar los últimos 10 registros, si no hay registros mostrar un mensaje de que no hay registros -->
    <div class="container">
        <div class="table-container text-center">
            <h2>Últimos 10 registros</h2>
            <hr>
            <table class="table table-striped table-bordered text-center">
                <thead class="table-dark">
                    <?php
                    $include = include '../../Back/Config/config.php';
                    $sql = "SELECT * FROM movimientos ORDER BY id DESC LIMIT 10";
                    $resultado = $conn->query($sql);
                    $registros = $resultado->fetch_all(MYSQLI_ASSOC);
                    ?>
                    <tr>
                        <th>Fecha</th>
                        <th>Descripción</th>
                        <th>Debito</th>
                        <th>Crédito</th>
                        <th>Saldo</th>
                        <th>Registrante</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Hacer consulta la base de datos en la tabla movimientos para obtener los últimos 10 registros -->
                    <?php
                    if ($registros) {
                        foreach ($registros as $registro) {
                            echo '<tr>';
                            echo '<td>'   . $registro['fecha'] . '</td>';
                            echo '<td>' . $registro['descripcion'] . '</td>';
                            // Cuando los campos sean nulos mostrar un "No disponible"
                            // Cuando si existan mostrarlos con un formato de dinero $ 1,000.00 - debito, credito y saldo
                            if ($registro['debito'] == NULL) {
                                echo '<td>No disponible</td>';
                            } else {
                                echo '<td>$ ' . number_format($registro['debito'], 2) . '</td>';
                            }
                            if ($registro['credito'] == NULL) {
                                echo '<td>No disponible</td>';
                            } else {
                                echo '<td>$ ' . number_format($registro['credito'], 2) . '</td>';
                            }
                            if ($registro['saldo'] == NULL) {
                                echo '<td>No disponible</td>';
                            } else {
                                echo '<td>$ ' . number_format($registro['saldo'], 2) . '</td>';
                            }
                            echo '<td>' . $registro['Registrante'] . '</td>';
                            echo '</tr>';
                        }
                    } else {
                        echo '<tr><td colspan="2">No hay registros para mostrar.</td></tr>';
                    }

                    $conn->close();
                    ?>
                    <tr>
                        <td colspan="8"></td>
                    </tr>

                </tbody>
            </table>
        </div>
    </div>
</body>



</html>