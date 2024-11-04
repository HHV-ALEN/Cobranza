<?php

$Banco = $_GET['Banco'] ?? $_POST['Banco'] ?? 'Sin Selección';
require '../../vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

use Smalot\PdfParser\Parser;
/// Arreglo de Operaciones (Conceptos)
$Operaciones = array('TRANSFERENCIA INTERNACIONAL', 'SPID RECIBIDO', 'TRASPASO A TERCEROS', 'SPID ENVIADO');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si se ha subido un archivo
    if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
        $pdfFile = $_FILES['pdf_file']['tmp_name'];
        $nombreArchivo = $_FILES['pdf_file']['name'];

        //echo "Archivo recibido: " . htmlspecialchars($nombreArchivo);
        //echo "<br>";

        // Asegúrate de que el archivo existe en la ubicación temporal
        if (file_exists($pdfFile)) {
            //echo "El archivo fue subido exitosamente.";
            //echo "<br>";
        } else {
            //echo "El archivo no se pudo subir.";
            exit;
        }

        // Continuar con el procesamiento del archivo PDF
        // Inicializar el parser de PDF
        $parser = new Parser();
        try {
            // Parsear el archivo PDF
            $pdf = $parser->parseFile($pdfFile);

            // Extraer el texto por líneas
            $lineas = explode("\n", $pdf->getText());

            $registros = [];
            $registroActual = null; // Inicializar el registro actual como null al inicio

            foreach ($lineas as $linea) {
                $linea = trim($linea); // Limpiar espacios en blanco
                //echo $linea;
                // Detectar si la línea contiene un folio pegado con la fecha
                if (preg_match('/^(\d+)(\d{2}-\d{2}-\d{4})/', $linea, $coincidencias)) {
                    $registroActual = [
                        'folio' => '',
                        'fecha' => '',
                        'operacion' => '',
                        'destinatario' => '',
                        'monto' => '',
                        'saldo' => '',
                        'archivo_procesado' => $nombreArchivo
                    ];

                    // Extraer el folio y la fecha (para casos donde están juntos)
                    preg_match('/^(\d{8,})(\d{2}-\d{2}-\d{4})/', $linea, $coincidencias);
                    if (!empty($coincidencias)) {
                        $registroActual['folio'] = $coincidencias[1]; // Folio
                        $registroActual['fecha'] = $coincidencias[2]; // Fecha

                        // Verificar si la operación está en la misma línea
                        foreach ($Operaciones as $operacion) {
                            if (strpos($linea, $operacion) !== false) {
                                $registroActual['operacion'] = $operacion;

                                // Obtener la parte después de la operación (el posible destinatario)
                                $parteRestante = substr($linea, strpos($linea, $operacion) + strlen($operacion));

                                // Verificar si hay un símbolo de $ en la misma línea (destinatario en una línea)
                                if (strpos($parteRestante, '$') !== false) {
                                    // Extraer el destinatario hasta el primer símbolo de $
                                    $registroActual['destinatario'] = trim(substr($parteRestante, 0, strpos($parteRestante, '$')));

                                    // Extraer la parte restante después del primer símbolo $
                                    $parteRestante = substr($parteRestante, strpos($parteRestante, '$'));

                                    // Eliminar caracteres en blanco adicionales (tabs, espacios dobles, etc.)
                                    $parteRestante = trim($parteRestante);

                                    // Usar explode para separar la cadena en dos partes, dividiendo por el espacio o cualquier otro separador
                                    $montos = preg_split('/\s+/', $parteRestante);

                                    // Asignar a variables asegurando que solo se obtiene un monto por variable
                                    $registroActual['monto'] = $montos[0];  // Primer monto
                                    $registroActual['saldo'] = isset($montos[1]) ? $montos[1] : null;  // Segundo monto (si existe)
                                }
                                break; // Salimos del bucle ya que hemos encontrado la operación
                            }
                        }
                        if ($registroActual) {
                            $registros[] = $registroActual;
                            /// En el ultimo elemento del arreglo de registros
                            /// Se guardará el nombre del archivo procesado
                            $registros[count($registros) - 1]['archivo_procesado'] = $nombreArchivo;

                        }
                    }
                } elseif (preg_match('/^\d{4,}/', $linea) && preg_match('/\d{2}-\d{2}-\d{4}/', $linea)) {
                    // Detectar si la línea contiene un folio y fecha
                    if (strpos($linea, 'TOTAL') !== false || strpos($linea, 'CUENTA') !== false || strpos($linea, 'RESUMEN') !== false) {
                        // Ignorar las líneas que contienen 'TOTAL', 'CUENTA', 'RESUMEN', etc.
                        continue;
                    }

                    $registroActual = [
                        'folio' => '',
                        'fecha' => '',
                        'operacion' => '',
                        'destinatario' => '',
                        'monto' => '',
                        'saldo' => '',
                        'archivo_procesado' => $nombreArchivo
                    ];

                    // Obtener la primera cadena de texto de esa linea
                    $cadena = explode(" ", $linea);
                    $folio = $cadena[0];
                    $fecha = $cadena[1];

                    // Verificar si la operación está en la misma línea
                    foreach ($Operaciones as $operacion) {
                        if (strpos($linea, $operacion) !== false) {
                            $registroActual['operacion'] = $operacion;
                            // Obtener la parte después de la operación (el posible destinatario)
                            $parteRestante = substr($linea, strpos($linea, $operacion) + strlen($operacion));
                            // Verificar si hay un símbolo de $ en la misma línea
                            if (strpos($parteRestante, '$') !== false) {
                                // Extraer el destinatario hasta el primer símbolo de $
                                $registroActual['destinatario'] = trim(substr($parteRestante, 0, strpos($parteRestante, '$')));
                                $parteRestante = substr($parteRestante, strpos($parteRestante, '$'));
                                $parteRestante = trim($parteRestante);
                                $montos = preg_split('/\s+/', $parteRestante);
                                $registroActual['monto'] = $montos[0];  // Primer monto
                                $registroActual['saldo'] = isset($montos[1]) ? $montos[1] : null;  // Segundo monto (si existe)

                            } else {
                                // Si no hay $, es posible que el destinatario esté en varias líneas
                                $destinatario = trim($parteRestante);

                                // Buscar en las líneas siguientes hasta encontrar el monto ($)
                                while (($siguienteLinea = next($lineas)) !== false) {
                                    // Concatenar el destinatario con las siguientes líneas
                                    if (strpos($siguienteLinea, '$') !== false) {
                                        // Agregar parte del destinatario hasta el primer símbolo de $
                                        $destinatario .= ' ' . trim(substr($siguienteLinea, 0, strpos($siguienteLinea, '$')));
                                        $parteRestante = substr($siguienteLinea, strpos($siguienteLinea, '$'));
                                        break;
                                    } else {
                                        $destinatario .= ' ' . trim($siguienteLinea);
                                    }
                                }

                                $registroActual['destinatario'] = $destinatario;

                            }

                            // Terminar el procesamiento de la operación y detener la búsqueda
                            break; // Salimos del bucle ya que hemos encontrado la operación
                        }
                    }

                    // Asignar valores de folio y fecha
                    $registroActual['folio'] = $folio;
                    $registroActual['fecha'] = $linea; // La línea completa que contiene la fecha y el posible texto adicional

                    // Utilizar una expresión regular para extraer solo la fecha en formato dd-mm-yyyy
                    if (preg_match('/\d{2}-\d{2}-\d{4}/', $registroActual['fecha'], $coincidenciasFecha)) {
                        $registroActual['fecha'] = $coincidenciasFecha[0]; // Guardar solo la fecha extraída
                        //echo "<br>" . $registroActual['fecha']; // Mostrar la fecha formateada
                    } else {
                        //echo "No se encontró una fecha válida en la línea.";
                    }

                    $fecha = $registroActual['fecha'];

                    // Agregar el registro actual a la lista de registros
                    if ($registroActual) {
                        $registros[] = $registroActual;

                    }
                }
            }
            // Mostrar los registros (opcional)
            //echo '<pre>';
            //print_r($registros);
            //echo '</pre>';

        } catch (Exception $e) {
            echo 'Error al procesar el archivo PDF: ' . $e->getMessage();
        }
        try {
            $folios = array_column($registros, 'folio');
            // Parsear el archivo PDF
            $pdf = $parser->parseFile($pdfFile);
            // agrear un texto al arreglo de folios
            $folios[] = 'Pasión';

            // Obtener el texto crudo completo del PDF
            $textoCrudo = $pdf->getText();

            //echo $textoCrudo;

            for ($i = 0; $i < count($folios) - 1; $i++) {
                $folioActual = $folios[$i];
                $siguienteFolio = $folios[$i + 1];
                //echo "<br>Siguiente Folio: " . $siguienteFolio;


                // Buscar el índice de inicio del primer folio
                $inicio = strpos($textoCrudo, $folioActual);

                // Buscar el índice del siguiente folio
                $fin = strpos($textoCrudo, $siguienteFolio, $inicio);

                // Verificar si el siguiente folio está pegado a una fecha (detectamos el patrón de fecha con formato dd-mm-yyyy)
                if ($fin !== false) {
                    // Obtener los caracteres antes del siguiente folio para ver si están pegados a una fecha
                    $antesDelFolio = substr($textoCrudo, $fin - 10, 10); // Tomamos los 10 caracteres antes del siguiente folio

                    // Verificar si hay una fecha en el formato dd-mm-yyyy pegada
                    if (preg_match('/\d{2}-\d{2}-\d{4}/', $antesDelFolio)) {
                        // Ajustar el índice del fin quitando 2 caracteres y el guion
                        $fin = $fin - 3;
                    }
                }

                // Extraer el texto entre ambos folios
                $textoEntreFolios = substr($textoCrudo, $inicio + strlen($folioActual), $fin - $inicio - strlen($folioActual));

                // Limpiar el texto extraído
                $textoEntreFolios = trim($textoEntreFolios);
                //echo "<br>Texto entre folios: " . $textoEntreFolios;


                // Guardar el texto procesado en un arreglo
                $textoProcesado[$folioActual] = $textoEntreFolios;
                //echo "<br>-----------------------------------<br>";
            }
            $num_registro = 0;
            foreach ($textoProcesado as $folio => $texto) {
                //echo "<br>Folio: " . $texto;

                // Usar una expresión regular para encontrar los valores de monto y saldo
                preg_match_all('/\$\d{1,3}(?:,\d{3})*(?:\.\d{2})?/', $texto, $matches);

                // Verificar que haya al menos dos valores (monto y saldo)
                if (isset($matches[0]) && count($matches[0]) >= 2) {
                    // Asignar los valores a variables
                    $monto = $matches[0][0];  // Primer valor es el monto
                    $saldo = $matches[0][1];  // Segundo valor es el saldo

                    /// Obtener el registro del arreglo de registros correspondiente
                    //echo "<br>-----------------------------------<br>";
                    $registro = $registros[$num_registro];
                    //echo "<br>Folio: " . $registro['folio'];
                    // Imprimir las variables
                    //echo "<br>Monto: " . $monto;
                    //echo "<br>Saldo: " . $saldo;
                    //echo "<br>-----------------------------------<br>";

                    /// Guardar Monto y saldo en el registro actual
                    $registros[$num_registro]['monto'] = $monto;
                    $registros[$num_registro]['saldo'] = $saldo;
                    $registros[$num_registro]['archivo_procesado'] = $nombreArchivo;


                    // Guardar los valores en el Arreglo de registros correspondiente
                    /// Guardar en una variable el folio actual

                    $num_registro++;
                } else {
                    // Si no se encuentran dos montos, mostrar un mensaje de error
                    //echo "<br>Error: No se encontraron ambos valores de monto y saldo.";
                }
            }
            // Mostrar los registros con montos y saldos
            //echo '<pre>';
            //print_r($registros);
            //echo '</pre>';

        } catch (Exception $e) {
            echo 'Error al procesar el archivo PDF: ' . $e->getMessage();
        }
    } else {
        echo 'Error: No se pudo subir el archivo o no se seleccionó ninguno.';
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
                                <label for="pdf_file" class="form-label">Seleccione Archivo PDF. a procesar</label>
                                <input type="file" class="form-control" name="pdf_file" id="pdf_file" accept=".pdf"
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
                        <img src="../../Back/Logos/BASE_SINFONDO.PNG" alt="<?php echo $Banco ?>"
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
        <?php if ($Banco == 'Base' && !empty($registros)): ?>
            <div class="table-container text-center">
                <h2>Registros Extraídos del PDF</h2>
                <hr>
                <form action="Registro.php?Banco=Base&Archivo=<?php echo $nombreArchivo; ?>" method="post">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Abono</th>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Operación</th>
                                <th>Destinatario</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $key => $registro):
                                // Formateamos la fecha
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="registros_Abono[]" value="<?php echo $key; ?>">
                                    </td>
                                    <!-- Mostrar Los Datos en Inputs para poder modificarlos -->
                                    <td><input type="text" name="registros[<?php echo $key; ?>][folio]"
                                            value="<?php echo htmlspecialchars($registro['folio']); ?>" class="form-control"
                                            required></td>
                                    <!-- Formatear la fecha, para que sea compatible con el campo de tipo date dd-mm-yyyy-->

                                    <td><input type="date" name="registros[<?php echo $key; ?>][fecha]"
                                            value="<?php echo date('Y-m-d', strtotime(str_replace('-', '/', $registro['fecha']))); ?>"
                                            class="form-control" required></td>
                                    <td><input type="text" name="registros[<?php echo $key; ?>][operacion]"
                                            value="<?php echo htmlspecialchars($registro['operacion']); ?>" class="form-control"
                                            required></td>
                                    <td>
                                        <!-- Campo para agregar o modificar el cliente -->
                                        <!-- Utilizar TextArea para Mostrar el Campo de destinatario -->
                                        <textarea class="form-control" name="registros[<?php echo $key; ?>][destinatario]"
                                            id="destinatario" rows="3"
                                            required><?php echo isset($registro['destinatario']) ? htmlspecialchars($registro['destinatario']) : ''; ?></textarea>

                                    </td>
                                    <td>
                                        <input type="text" name="registros[<?php echo $key; ?>][monto]"
                                            value="<?php echo isset($registro['monto']) ? htmlspecialchars($registro['monto']) : ''; ?>"
                                            class="form-control currency-input" required oninput="formatCurrencyInput(this)">
                                    </td>
                                    <td>
                                        <input type="text" name="registros[<?php echo $key; ?>][saldo]"
                                            value="<?php echo isset($registro['saldo']) ? htmlspecialchars($registro['saldo']) : ''; ?>"
                                            class="form-control currency-input" required oninput="formatCurrencyInput(this)">
                                    </td>


                                </tr>
                                <?php if (isset($key) && isset($fechaFormateada) && isset($registro)): ?>
                                    <input type="hidden" name="registros[<?php echo $key; ?>][fecha]"
                                        value="<?php echo htmlspecialchars($fechaFormateada); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][folio]"
                                        value="<?php echo htmlspecialchars($registro['folio']); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][operacion]"
                                        value="<?php echo htmlspecialchars($registro['operacion']); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][monto]"
                                        value="<?php echo htmlspecialchars($registro['monto']); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][destinatario]"
                                        value="<?php echo htmlspecialchars($registro['destinatario']); ?>">

                                    <input type="hidden" name="registros[<?php echo $key; ?>][saldo]"
                                        value="<?php echo htmlspecialchars($registro['saldo']); ?>">

                                    <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]"
                                        value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">

                                        <input type="hidden" name="Nombre_Archivo" value="<?php echo $nombreArchivo; ?>">

                                <?php else: ?>
                                    <!-- Opcionalmente, puedes agregar un mensaje de error o debug -->
                                    <?php error_log('Los valores del registro no están completos.'); ?>
                                <?php endif; ?>


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