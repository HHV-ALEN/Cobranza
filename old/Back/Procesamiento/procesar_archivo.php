<?php
// Asegúrate de incluir el autoloader de Composer
require 'vendor/autoload.php';

use PhpOffice\PhpSpreadsheet\IOFactory;

use Smalot\PdfParser\Parser;

$saldoInicial = null;
$saldoFinal = null;
$textoExtraido = ""; // Variable donde se almacenará el texto extraído
$registros = []; // Aquí almacenaremos cada registro de la tabla

/// Arreglo de Operaciones (Conceptos)
$Operaciones = array('TRANSFERENCIA INTERNACIONAL', 'SPID RECIBIDO', 'TRASPASO A TERCEROS', 'SPID ENVIADO');

if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
    // Código para procesar el archivo

    // Variable donde se almacenará el banco seleccionado, Si no se selecciona ninguno, se mostrará "Sin Selección"
    $Banco = isset($_POST['Banco']) ? $_POST['Banco'] : 'Sin Selección';

    if ($Banco == 'Banistmo') {
        // Verificar si se ha enviado el formulario
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar si se ha subido un archivo
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
        }
    }
    
    
    else if ($Banco == 'Santander') {
        // Verifica que el archivo ha sido subido correctamente
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
            // Obtén el archivo subido
            $archivoCSV = $_FILES['pdf_file']['tmp_name'];

            // Abre el archivo CSV para lectura
            if (($handle = fopen($archivoCSV, 'r')) !== false) {
                // Saltar la primera fila (encabezados)
                fgetcsv($handle);

                // Array para guardar los registros procesados
                $registros = [];

                // Leer cada fila del archivo CSV
                while (($datos = fgetcsv($handle, 1000, ",")) !== false) {
                    // Asegúrate de que la fila tenga al menos el número de columnas que necesitas
                    if (count($datos) >= 15) {
                        // Asignar valores a las variables con base en las columnas del CSV
                        $fecha = $datos[1]; // Columna B - Fecha
                        $descripcion = $datos[4]; // Columna E - Descripción
                        $CargoAbono = $datos[5]; // Columna F - Cargo/Abono
                        $importe = $datos[6]; // Columna H - Importe
                        $saldo = $datos[7]; // Columna I - Saldo
                        $referencia = $datos[8]; // Columna J - Referencia
                        $concepto = $datos[9]; // Columna K - Concepto
                        $nombreOrdenante = $datos[14]; // Columna O - Nombre Ordenante
                        $archivo_procesado = $_FILES['pdf_file']['name']; // Nombre del archivo procesado

                        // Guardamos cada registro en el array
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
                // Cerrar el archivo
                fclose($handle);

                // Mostrar los registros procesados
                //print_r($registros);
            } else {
                echo "No se pudo abrir el archivo CSV.";
            }
        } else {
            echo "Error al subir el archivo.";
        }
    } else if ($Banco == 'Banamex') {
        if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == UPLOAD_ERR_OK) {
            // Obtén el archivo subido
            $archivoCSV = $_FILES['pdf_file']['tmp_name'];

            /// Obtener Nombre del Archivo
            $nombreArchivo = $_FILES['pdf_file']['name'];

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
                    } elseif ($rowIndex >= 14 && $rowIndex < 14 + $num_registros) {
                        // Fila 14 en adelante: Registros (hasta la cantidad especificada en A9)
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
                        }
                    }
                }
                fclose($handle);
            }
        }
    } elseif ($Banco == 'BASE') {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Verificar si se ha subido un archivo
            if (isset($_FILES['pdf_file']) && $_FILES['pdf_file']['error'] == 0) {
                $pdfFile = $_FILES['pdf_file']['tmp_name'];
                $nombreArchivo = $_FILES['pdf_file']['name'];
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
                            $registroActual['fecha'] = $fecha;

                            // Agregar el registro actual a la lista de registros
                            if ($registroActual) {
                                $registros[] = $registroActual;
                                /// En el ultimo elemento del arreglo de registros
                                    /// Se guardará el nombre del archivo procesado
                                    $registros[count($registros) - 1]['archivo_procesado'] = $nombreArchivo;
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
    } else {
        echo 'No se ha seleccionado un archivo para procesar.';
    }
}
?>