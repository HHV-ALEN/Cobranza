<?php

use function PHPSTORM_META\type;

include '../Config/config.php';

$Banco = isset($_GET['Banco']) ? $_GET['Banco'] : 'Sin Selección';

if ($Banco == 'BASE') {
    // Verificar si los datos han sido enviados correctamente
    if (isset($_POST['registros'])) {

        // Obtener todos los registros enviados
        $todosLosRegistros = $_POST['registros'];

        // Verificar si hay registros seleccionados como "Abono"
        $registrosSeleccionados = isset($_POST['registros_Abono']) ? $_POST['registros_Abono'] : [];

        // Si NO hay registros seleccionados como Abono, procesar TODOS como "Cargo"
        if (empty($registrosSeleccionados)) {
            // Aquí iteramos todos los registros para procesarlos como "Cargo"
            foreach ($todosLosRegistros as $key => $registro) {
                // Procesar cada registro como Cargo (débito)
                $folio = $registro['folio'];
                $fechaCompleta = $registro['fecha'];

                // Usar expresión regular para extraer el formato de fecha (dd-mm-yyyy)
                if (preg_match('/\d{2}-\d{2}-\d{4}/', $fechaCompleta, $matches)) {
                    $fecha = $matches[0]; // Asignar solo la parte que es la fecha
                }

                // Imprime para verificar
                echo $fecha; // Ejemplo: 04-10-2024
                $operacion = $registro['operacion'];
                $destinatario = $registro['destinatario'];
                $monto = $registro['monto'];
                $monto = str_replace(['$', ','], '', $monto);

                // Convertir el valor a tipo float
                $montoFloat = floatval($monto);

                $saldo = $registro['saldo'];
                $saldo = str_replace(['$', ','], '', $saldo);

                $Archivo = $registro['archivo_procesado'];

                // Lógica para insertar o procesar el Cargo en la base de datos
                echo "Procesando Cargo: Folio $folio, Monto: $montoFloat, Destinatario: $destinatario<br>";

                $sql = "INSERT INTO movimientos_base (folio, fecha, operacion, destinatario, Cargo, saldo, estado, registrante, archivo, banco)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                // Preparar la consulta
                $stmt = $conn->prepare($sql);

                // Vincular los parámetros

                $estado = 'Pendiente';
                $registrante = 'Usuario';
                $banco = 'BASE';

                $stmt->bind_param("ssssdsssss", $folio, $fecha, $operacion, $destinatario, $montoFloat, $saldo, $estado, $registrante, $Archivo, $banco);
                // Ejecutar la consulta

                if (!$stmt->execute()) {
                    // Manejo del error
                    echo "Error al insertar el registro con folio $folio: " . $stmt->error;
                } else {
                    echo "<br>Registro con folio $folio insertado correctamente.<br>";
                }
            }
        } else {
            // Si hay registros seleccionados, separarlos en Abonos y Cargos
            foreach ($registrosSeleccionados as $key) {
                if (isset($todosLosRegistros[$key])) {
                    $registro = $todosLosRegistros[$key];

                    // Procesar el registro como Abono
                    $folio = $registro['folio'];
                    $fechaCompleta = $registro['fecha'];

                    // Usar expresión regular para extraer el formato de fecha (dd-mm-yyyy)
                    if (preg_match('/\d{2}-\d{2}-\d{4}/', $fechaCompleta, $matches)) {
                        $fecha = $matches[0]; // Asignar solo la parte que es la fecha
                    }

                    // Imprime para verificar
                    echo $fecha; // Ejemplo: 04-10-2024
                    $operacion = $registro['operacion'];
                    $destinatario = $registro['destinatario'];
                    $monto = $registro['monto'];
                    $saldo = $registro['saldo'];
                    $saldo = str_replace(['$', ','], '', $saldo);
                    $Archivo = $registro['archivo_procesado'];

                    $monto = str_replace(['$', ','], '', $monto);

                    // Convertir el valor a tipo float
                    $montoFloat = floatval($monto);


                    // Lógica para insertar o procesar el Abono en la base de datos
                    echo "Procesando Abono: Folio $folio, Monto: $montoFloat, Destinatario: $destinatario<br>";

                    $sql = "INSERT INTO movimientos_base (folio, fecha, operacion, destinatario, abono, saldo, estado, registrante, archivo, banco)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    // Preparar la consulta
                    $stmt = $conn->prepare($sql);

                    // Vincular los parámetros

                    $estado = 'Pendiente';
                    $registrante = 'Usuario';
                    $banco = 'BASE';

                    $stmt->bind_param("ssssdsssss", $folio, $fecha, $operacion, $destinatario, $montoFloat, $saldo, $estado, $registrante, $Archivo, $banco);
                    // Ejecutar la consulta

                    if (!$stmt->execute()) {
                        // Manejo del error
                        echo "Error al insertar el registro con folio $folio: " . $stmt->error;
                    } else {
                        echo "Registro con folio $folio insertado correctamente.";
                    }
                }
            }

            // Procesar los restantes como "Cargo"
            foreach ($todosLosRegistros as $key => $registro) {
                // Si el registro no está en los seleccionados, procesarlo como Cargo
                if (!in_array($key, $registrosSeleccionados)) {
                    $folio = $registro['folio'];
                    $fechaCompleta = $registro['fecha'];

                    // Usar expresión regular para extraer el formato de fecha (dd-mm-yyyy)
                    if (preg_match('/\d{2}-\d{2}-\d{4}/', $fechaCompleta, $matches)) {
                        $fecha = $matches[0]; // Asignar solo la parte que es la fecha
                    }

                    // Imprime para verificar
                    echo $fecha; // Ejemplo: 04-10-2024
                    $operacion = $registro['operacion'];
                    $destinatario = $registro['destinatario'];
                    $monto = $registro['monto'];
                    $saldo = $registro['saldo'];
                    $Archivo = $registro['archivo_procesado'];

                    $monto = str_replace(['$', ','], '', $monto);

                    $saldo = str_replace(['$', ','], '', $saldo);

                    // Convertir el valor a tipo float
                    $montoFloat = floatval($monto);


                    // Lógica para insertar o procesar el Cargo en la base de datos
                    echo "Procesando Cargo: Folio $folio, Monto: $montoFloat, Destinatario: $destinatario<br>";

                    $sql = "INSERT INTO movimientos_base (folio, fecha, operacion, destinatario, Cargo, saldo, estado, registrante, archivo, banco)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

                    // Preparar la consulta
                    $stmt = $conn->prepare($sql);

                    // Vincular los parámetros

                    $estado = 'Pendiente';
                    $registrante = 'Usuario';
                    $banco = 'BASE';

                    $stmt->bind_param("ssssdsssss", $folio, $fecha, $operacion, $destinatario, $montoFloat, $saldo, $estado, $registrante, $Archivo, $banco);
                    // Ejecutar la consulta

                    if (!$stmt->execute()) {
                        // Manejo del error
                        echo "Error al insertar el registro con folio $folio: " . $stmt->error;
                    } else {
                        echo "Registro con folio $folio insertado correctamente.";
                    }

                }
            }
        }
    } else {
        echo "No se enviaron registros.";
    }
}


/*
if ($Banco == 'BASE'){
      // Verificar si los datos han sido enviados correctamente
      if (isset($_POST['registros'])) {

        // Obtener todos los registros enviados
        $todosLosRegistros = $_POST['registros'];
// Verificar si los datos han sido enviados correctamente
if (isset($_POST['registros_Abono']) && isset($_POST['registros'])) {

    // Obtener los registros seleccionados
    $registrosSeleccionados = $_POST['registros_Abono']; // Array con los keys seleccionados

    // Obtener todos los registros enviados
    $todosLosRegistros = $_POST['registros'];

    // Recorrer los registros seleccionados
    foreach ($registrosSeleccionados as $key) {
        // Asegurarse de que el key existe en los registros enviados
        if (isset($todosLosRegistros[$key])) {
            $registro = $todosLosRegistros[$key];

            print_r($registro);

            // Extraer los datos del registro
            $folio = $registro['folio']; // Folio del registro
            $fecha = $registro['fecha']; // Fecha del registro
            $operacion = $registro['operacion']; // Operación del registro
            $destinatario = $registro['destinatario']; // Destinatario
            $monto = $registro['monto']; // Monto (en el valor numérico sin formato)
            $monto = floatval(str_replace(',', '', str_replace('$', '', $monto)));
            $saldo = $registro['saldo']; // Saldo
            $saldo = floatval(str_replace(',', '', str_replace('$', '', $saldo)));
            $Archivo = $registro['archivo_procesado']; // Archivo procesado

            echo "<br><br> ------- ELEMENTO A REGISTRAR --------- <br><br>";
            echo "Folio: $folio<br>";
            echo "Fecha: $fecha<br>";
            echo "Operación: $operacion<br>";
            echo "Destinatario: $destinatario<br>";
            echo "Abono: $monto<br>";
            echo "Saldo: $saldo<br>";
            echo "Archivo: $Archivo<br>";


            // Aquí puedes realizar el procesamiento que necesites con los datos.
            // Por ejemplo, almacenarlos en una base de datos o procesarlos según tu lógica.

            /// Se realizara una inserción a la bd con los siguientes campos
            /// Folio, Fecha, Operación, Destinatario, Abono, Saldo, Estado, Registrante, Archivo, Banco

            $sql = "INSERT INTO movimientos_base (folio, fecha, operacion, destinatario, abono, saldo, estado, registrante, archivo, banco)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

            // Preparar la consulta
            $stmt = $conn->prepare($sql);

            // Vincular los parámetros

            $estado = 'Pendiente';
            $registrante = 'Usuario';
            $banco = 'BASE';

            $stmt->bind_param("ssssdsssss", $folio, $fecha, $operacion, $destinatario, $monto, $saldo, $estado, $registrante, $Archivo, $banco);
            // Ejecutar la consulta

            if (!$stmt->execute()) {
                // Manejo del error
                echo "Error al insertar el registro con folio $folio: " . $stmt->error;
            } else {
                echo "Registro con folio $folio insertado correctamente.";
            }

            /*
            echo "<br>-------------- Registros Abonados --------------<br>";
            echo "<br>Procesando registro con folio $folio:<br>";
            echo "Fecha: $fecha<br>";
            echo "Operación: $operacion<br>";
            echo "Destinatario: $destinatario<br>";
            echo "Monto: $monto<br>";
            echo "Saldo: $saldo<br><br>"; //*
        }
    }
    /// Iterar todos los registros para compararlos con los seleccionados,
    /// Y los que no estén seleccionados, se procesarán como débito.
    foreach ($todosLosRegistros as $key => $registro) {
        // Si el registro no está en los seleccionados, se procesará como débito
        if (!in_array($key, $registrosSeleccionados)) {
            $folio = $registro['folio']; // Folio del registro
            $fecha = $registro['fecha']; // Fecha del registro
            $operacion = $registro['operacion']; // Operación del registro
            $destinatario = $registro['destinatario']; // Destinatario
            $monto = $registro['monto']; // Monto (en el valor numérico sin formato)
            $monto = floatval(str_replace(',', '', str_replace('$', '', $monto)));
            $saldo = $registro['saldo']; // Saldo
            $saldo = floatval(str_replace(',', '', str_replace('$', '', $saldo)));
            $Archivo = $registro['archivo_procesado']; // Archivo procesado

            $sql = "INSERT INTO movimientos_base (folio, fecha, operacion, destinatario, Cargo, saldo, estado, registrante, archivo, banco)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
            // Preparar la consulta
            $stmt = $conn->prepare($sql);

            // Vincular los parámetros

            $estado = 'Pendiente';
            $registrante = 'Usuario';
            $banco = 'BASE';

            $stmt->bind_param("ssssdsssss", $folio, $fecha, $operacion, $destinatario, $monto, $saldo, $estado, $registrante, $Archivo, $banco);
            // Ejecutar la consulta

            if (!$stmt->execute()) {
                // Manejo del error
                echo "Error al insertar el registro con folio $folio: " . $stmt->error;
            } else {
                echo "Registro con folio $folio insertado correctamente.";
            }

            echo "<br>-------------- Registros Debito --------------<br>";
            echo "<br>Procesando registro con folio $folio:<br>";
            echo "Fecha: $fecha<br>";
            echo "Operación: $operacion<br>";
            echo "Destinatario: $destinatario<br>";
            echo "Monto: $monto<br>";
            echo "Saldo: $saldo<br><br>";
        }
    }
} 

}

*/

?>