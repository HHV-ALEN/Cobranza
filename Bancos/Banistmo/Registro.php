<?php 
require '../../Back/Config/config.php';

$Banco = $_GET['Banco'];

echo "<br>Se está recibiendo el Banco: " . $Banco;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['registros']) && isset($_POST['registros_credito'])) {
        $registros = $_POST['registros']; // Todos los registros
        $registros_credito_indices = $_POST['registros_credito']; // Índices de los registros seleccionados (Crédito)

        $registros_credito = [];
        $registros_debito = [];

        // Iteramos sobre todos los registros
        foreach ($registros as $key => $registro) {
            // Si el índice del registro está en registros_credito, lo añadimos al array de crédito
            if (in_array($key, $registros_credito_indices)) {
                $registros_credito[] = $registro;
            } else {
                // Si no está seleccionado, lo añadimos al array de débito
                $registros_debito[] = $registro;
            }
        }

        // Procesamos los registros de crédito
        foreach ($registros_credito as $registro) {
            // Insertamos en la base de datos
            $fecha = $registro['fecha'];
            $descripcion = $registro['descripcion'];
            $monto = $registro['monto'];
            $saldo = $registro['saldo'];
            $fecha_registro = date('Y-m-d H:i:s');
            $registrante = 'Usuario';
            $archivo_procesado = $registro['archivo_procesado'];
            $estado = 'Pendiente';
            $banco = 'Banistmo';
            $Cliente = $registro['Cliente']; // Cliente del registro

            $sql = "INSERT INTO movimientos (fecha, descripcion, credito, saldo, Fecha_Registro, Registrante, archivo_procesado, Estado, Banco, Cliente)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssddssssss", $fecha, $descripcion, $monto, $saldo, $fecha_registro, $registrante, $archivo_procesado, $estado, $banco, $Cliente);

            if (!$stmt->execute()) {
                // Manejo del error
            }
            $stmt->close();
        }

        // Procesamos los registros de débito
        foreach ($registros_debito as $registro) {
            $fecha = $registro['fecha'];
            $descripcion = $registro['descripcion'];
            $monto = $registro['monto'];
            $saldo = $registro['saldo'];
            $fecha_registro = date('Y-m-d H:i:s');
            $registrante = 'Usuario';
            $archivo_procesado = $registro['archivo_procesado'];
            $estado = 'Pendiente';
            $banco = 'Banistmo';
            $Cliente = $registro['Cliente']; // Cliente del registro

            $sql = "INSERT INTO movimientos (fecha, descripcion, debito, saldo, Fecha_Registro, Registrante, archivo_procesado, Estado, Banco, Cliente)
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("ssddssssss", $fecha, $descripcion, $monto, $saldo, $fecha_registro, $registrante, $archivo_procesado, $estado, $banco, $Cliente);

            if (!$stmt->execute()) {
                // Manejo del error
            }
            $stmt->close();
        }

        echo "<br>Se han procesado los registros correctamente";
    } else {
        echo "<br>No se han recibido los datos correctamente";
    }
} else {
    echo "<br>No se ha enviado el formulario";
}

// Una vez cumplidas las inserciónes en la base de datos, redirigimos a la tabla de registros del banco:
header("Location: Listado.php?Banco=$Banco");

?>