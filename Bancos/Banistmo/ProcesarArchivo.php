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
                        echo $linea . '<br>';

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
        echo 'Error: No se recibió un archivo PDF.';
    }


?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $Banco ?></title>
</head>

<body>
    <?php include '../../navbar.php'; ?>
    <div class="container">
        <div class="text-center">
            <br>
            <h1>Procesar archivo de <?php echo $Banco ?></h1>
        </div>
    </div>
    <div class="container">
        <div class="row">
            <div class="col-12">
                <form action="" method="post" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="file">Seleccione el archivo a procesar</label>
                        <input type="file" class="form-control-file" name="pdf_file" id="pdf_file">
                    </div>
                    <input type="hidden" name="Banco" value="<?php echo $Banco ?>">
                    <button type="submit" class="btn btn-primary">Procesar</button>
                </form>
            </div>
        </div>

        <hr>

    <?php
        if ($Banco == 'Banistmo' && !empty($registros)) {
            ?>
            <div class="result-container">
                <h2>Registros Extraídos del PDF</h2>
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
                            <?php foreach ($registros as $key => $registro): ?>
                                <?php
                                // Formateamos la fecha
                                $fechaOriginal = $registro['fecha'];
                                $fechaObj = DateTime::createFromFormat('dmY', $fechaOriginal);
                                $fechaFormateada = $fechaObj ? $fechaObj->format('Y-m-d') : $fechaOriginal;
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="registros_credito[]" value="<?php echo $key; ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($fechaFormateada); ?></td>
                                    <td><?php echo htmlspecialchars($registro['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['monto']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['saldo']); ?></td>
                                    <td>
                                        <!-- Campo para agregar o modificar el cliente -->
                                        <input type="text" name="registros[<?php echo $key; ?>][Cliente]"
                                            value="<?php echo isset($registro['Cliente']) ? htmlspecialchars($registro['Cliente']) : ''; ?>"
                                            class="form-control" required>
                                    </td>
                                    
                                </tr>
                                <input type="hidden" name="registros[<?php echo $key; ?>][fecha]"
                                    value="<?php echo htmlspecialchars($fechaFormateada); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][descripcion]"
                                    value="<?php echo htmlspecialchars($registro['descripcion']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][monto]"
                                    value="<?php echo htmlspecialchars($registro['monto']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][saldo]"
                                    value="<?php echo htmlspecialchars($registro['saldo']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]"
                                    value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">
                            <?php endforeach; ?>

                        </tbody>

                    </table>

                    <br>
                    <button type="submit" class="btn btn-success w-100">Enviar registros seleccionados</button>
                </form>
            </div>


        <?php } ?>
<hr>
<br>

</div>

</body>

</html>