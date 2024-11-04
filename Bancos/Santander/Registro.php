<?php 
require '../../Back/Config/config.php';

$Banco = $_GET['Banco'];

echo "<br>Se está recibiendo el Banco: " . $Banco . "<br>";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Verificar si existe el array 'registros' en la variable $_POST
    if (isset($_POST['registros']) && is_array($_POST['registros'])) {
        $registros = $_POST['registros'];

        // Recorrer cada registro y procesar sus datos
        foreach ($registros as $key => $registro) {
            // Obtener los valores enviados
            $fecha = $registro['fecha'] ?? null;
            $fechaOriginal = $registro['fecha_original'] ?? null;
            $descripcion = $registro['descripcion'] ?? null;
            /// Las variables del Monto y Saldo, Cambiarlas a Float
            $monto = $registro['monto'] ?? null;
            $monto = str_replace(',', '', $monto);
            $monto = floatval($monto);


            $saldo = $registro['saldo'] ?? null;
            $saldo = str_replace(',', '', $saldo);
            $saldo = floatval($saldo);
            
            $cliente = $registro['Cliente'] ?? null;
            $CargoAbono = $registro['CargoAbono'] ?? null;
            $archivoProcesado = $registro['archivo_procesado'] ?? null;

            // Aquí puedes procesar o almacenar los datos, por ejemplo:
            echo "Registro $key:<br>";
            echo "Fecha: $fecha<br>";
            echo "Fecha Original: $fechaOriginal<br>";
            echo "Descripción: $descripcion<br>";
            echo "Monto: $monto<br>";
            echo "Saldo: $saldo<br>";
            echo "Cliente: $cliente<br>";
            echo "Cargo/Abono: $CargoAbono<br>";
            echo "Archivo Procesado: $archivoProcesado<br><br>";
            

            // Puedes hacer algo con los datos, como insertarlos en una base de datos.

            if ($CargoAbono == "+"){
                /// Abono -> Tabla movimientos_santander
                // Atributos: id, Fecha, Descripcion, Referencia, Concepto, Abono, Saldo, Fecha_Registro, Registrante, Estado, Cliente, Archivo_Procesado
                $sql = "INSERT INTO movimientos_santander (Fecha, Descripcion, Abono, Saldo, Fecha_Registro, Registrante, Estado, Cliente, Archivo_Procesado) 
                VALUES ('$fecha', '$descripcion', '$monto', '$saldo', NOW(), 'Usuario', 'Pendiente', '$cliente', '$archivoProcesado')";
                if ($conn->query($sql) === TRUE) {
                    echo "Registro insertado correctamente en la tabla movimientos_santander.<br>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
            }
            elseif($CargoAbono == "-"){
                /// Cargo -> Tabla movimientos_santander
                // Atributos: id, Fecha, Descripcion, Referencia, Concepto, Cargo, Saldo, Fecha_Registro, Registrante, Estado, Cliente, Archivo_Procesado
                $sql = "INSERT INTO movimientos_santander (Fecha, Descripcion, Cargo, Saldo, Fecha_Registro, Registrante, Estado, Cliente, Archivo_Procesado) 
                VALUES ('$fecha', '$descripcion', '$monto', '$saldo', NOW(), 'Usuario', 'Pendiente', '$cliente', '$archivoProcesado')";
                if ($conn->query($sql) === TRUE) {
                    echo "Registro insertado correctamente en la tabla movimientos_santander.<br>";
                } else {
                    echo "Error: " . $sql . "<br>" . $conn->error;
                }
        }
        echo "<br>--------------------------------------------<br>";
    }
    } else {
        echo "No se recibieron registros.";
    }
} else {
    echo "Método de solicitud no permitido.";
}
header ("Location: Listado.php?Banco=$Banco");
?>