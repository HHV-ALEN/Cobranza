<?php 
require '../../Back/Config/config.php';

$Banco = $_GET['Banco'];
$Nombre_Archivo = $_GET['Archivo'];
//echo "<br>Se está recibiendo el Archivo: " . $Nombre_Archivo . "<br>";

$FechaActual = date("Y-m-d H:i:s");
//echo "<br>Se está recibiendo el Banco: " . $Banco . "<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Inicializar los arrays para Cargos y Abonos
    $registrosCargos = [];
    $registrosAbonos = [];

    // Verificar si el array de registros está definido
    if (isset($_POST['registros'])) {
        $registros = $_POST['registros']; // Todos los registros enviados
    } else {
        //echo "No se encontraron registros.";
        exit;
    }

    // Verificar los registros seleccionados como 'Cargos' a través de los checkboxes
    if (isset($_POST['registros_Abono'])) {
        $abonosSeleccionados = $_POST['registros_Abono']; // Registros seleccionados (por checkbox)
    } else {
        $abonosSeleccionados = []; // Si no hay seleccionados
    }

    // Separar los registros en Cargos y Abonos
    foreach ($registros as $key => $registro) {
        if (in_array($key, $abonosSeleccionados)) {
            // Si el registro está en los seleccionados (Abonos)
            $registrosAbonos[] = $registro;
        } else {
            // Si no está seleccionado (Cargos)
            $registrosCargos[] = $registro;
        }
    }

    // Aquí ya tienes dos arrays separados: $registrosCargos y $registrosAbonos
    // Puedes procesarlos como sea necesario.
/*
    // Mostrar los resultados (opcional, para debug)
    //echo "<h2>Registros de Cargos:</h2>";
    foreach ($registrosCargos as $cargo) {
        //echo "<pre>";
        print_r($cargo);
        //echo "</pre>";
    }

    //echo "<h2>Registros de Abonos:</h2>";
    foreach ($registrosAbonos as $abono) {
        //echo "<pre>";
        print_r($abono);
        //echo "</pre>";
    }
*/
    // Aquí puedes guardar los registros en la base de datos o realizar la lógica adicional que necesites.

    // Guardar los registros de Cargos en BD
    foreach ($registrosCargos as $key => $registro) {
        //echo "<br>------------------ Registro de Cargo ------------------<br>";
        $folio = $registro['folio'] ?? null;
        // Procesar los datos de cada registro
        $fecha = $registro['fecha'] ?? null;
        $fechaOriginal = $registro['fecha_original'] ?? null;
        $descripcion = $registro['operacion'] ?? null;
    
        // Formatear Monto y Saldo a Float
        //echo "Monto Previo a la conversión: " . $registro['monto'] . "<br>";
        
        // Limpiar el monto de caracteres no numéricos excepto el punto decimal
        $monto = $registro['monto'] ?? null;
        $monto = preg_replace('/[^\d.]/', '', $monto); // Eliminar todo excepto dígitos y punto
        $monto = floatval($monto); // Convertir a float
    
        //echo "Monto Convertido: " . $monto . "<br>"; // Debug para ver si la conversión es correcta
        
        $saldo = $registro['saldo'] ?? null;
        $saldo = preg_replace('/[^\d.]/', '', $saldo); // Eliminar todo excepto dígitos y punto
        $saldo = floatval($saldo); // Convertir a float
    
        //echo "Saldo Convertido: " . $saldo . "<br>"; // Debug para ver si la conversión es correcta
        
        $cliente = $registro['destinatario'] ?? null;
        $archivoProcesado = $Nombre_Archivo;
    
        //echo "<BR> -- Registro de Cargo: $folio -- <BR>";
        //echo "Fecha: $fecha<BR>";
        //echo "Descripción: $descripcion<BR>";
        //echo "Monto: $monto<BR>";
        //echo "Saldo: $saldo<BR>";
        //echo "Cliente: $cliente<BR>";
        //echo "Archivo Procesado: $archivoProcesado<BR>";
        //echo "<br>--------------------------------------------<br>";
        // Insertar el registro en la base de datos
        $sql = "INSERT INTO movimientos_base (Folio, Fecha, Operacion, Destinatario, Cargo, Saldo, Estado, Registrante, Fecha_Registro, Archivo, Banco)
                VALUES ('$folio', '$fecha', '$descripcion', '$cliente', '$monto', '$saldo', 'Pendiente', 'Usuario', NOW(), '$archivoProcesado', '$Banco')";
        if ($conn->query($sql) === TRUE) {
            //echo "Registro insertado correctamente en la tabla movimientos_santander.<br>";
        } else {
            //echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }

    // Guardar los registros de Abonos en BD
    foreach ($registrosAbonos as $key => $registro) {
        //echo "<br>------------------ Registro de Abono ------------------<br>";
        $folio = $registro['folio'] ?? null;
        // Procesar los datos de cada registro
        $fecha = $registro['fecha'] ?? null;
        $fechaOriginal = $registro['fecha_original'] ?? null;
        $descripcion = $registro['operacion'] ?? null;
    
        // Formatear Monto y Saldo a Float
        //echo "Monto Previo a la conversión: " . $registro['monto'] . "<br>";
        
        // Limpiar el monto de caracteres no numéricos excepto el punto decimal
        $monto = $registro['monto'] ?? null;
        $monto = preg_replace('/[^\d.]/', '', $monto); // Eliminar todo excepto dígitos y punto
        $monto = floatval($monto); // Convertir a float
    
        //echo "Monto Convertido: " . $monto . "<br>"; // Debug para ver si la conversión es correcta
        
        $saldo = $registro['saldo'] ?? null;
        $saldo = preg_replace('/[^\d.]/', '', $saldo); // Eliminar todo excepto dígitos y punto
        $saldo = floatval($saldo); // Convertir a float
    
        //echo "Saldo Convertido: " . $saldo . "<br>"; // Debug para ver si la conversión es correcta
        
        $cliente = $registro['destinatario'] ?? null;
        $archivoProcesado = $Nombre_Archivo;
    
        //echo "<BR> -- Registro de Abono: $folio -- <BR>";
        //echo "Fecha: $fecha<BR>";
        //echo "Descripción: $descripcion<BR>";
        //echo "Monto: $monto<BR>";
        //echo "Saldo: $saldo<BR>";
        //echo "Cliente: $cliente<BR>";
        //echo "Archivo Procesado: $archivoProcesado<BR>";
        //echo "<br>--------------------------------------------<br>";

        // Insertar el registro en la base de datos
        $sql = "INSERT INTO movimientos_base (Folio, Fecha, Operacion, Destinatario, Abono, Saldo, Estado, Registrante, Fecha_Registro, Archivo, Banco)
                VALUES ('$folio', '$fecha', '$descripcion', '$cliente', '$monto', '$saldo', 'Pendiente', 'Usuario', NOW(), '$archivoProcesado', '$Banco')";

        if ($conn->query($sql) === TRUE) {
            //echo "Registro insertado correctamente en la tabla movimientos_base.<br>";
        } else {
            //echo "Error: " . $sql . "<br>" . $conn->error;
        }
    }
} else {
    //echo "Método de solicitud no válido.";
}

$conn->close();

// Limpiar cabeceras 
ob_end_clean();


header ("Location: Listado.php?Banco=$Banco");

?>