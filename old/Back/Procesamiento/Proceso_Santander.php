<?php 
include '../Config/config.php';

$Banco = trim($_GET['Banco']);

echo "<br>Se está recibiendo el Banco: " . $Banco;

/// Obtener la información del arreglo registros
$registros = $_POST['registros']; // Todos los registros

foreach ($registros as $registro => $value) {
    
    /// Insertar A base de datos: cobranza | Tabla: movimientos_santander
    $nombre_ordenante = $value['nombre_ordenante']; // Capturando correctamente el nombre del cliente

    $fecha = $value['fecha'];
    $descripcion = $value['descripcion'];
    $CargoAbono = $value['CargoAbono'];
    $importe = $value['importe'];
    $saldo = $value['saldo'];
    $referencia = $value['referencia'];
    $concepto = $value['concepto'];
    $fecha_registro = date('Y-m-d H:i:s');
    $registrante = 'Usuario';
    $Estado = 'Pendiente';
    $Cliente = $value['nombre_ordenante'];
    $archivo_procesado = $value['archivo_procesado'];
    echo "<br>*******************************************";
    echo "<br>Fecha: " . $fecha;
    echo "<br>Descripción: " . $descripcion;
    echo "<br>Cargo/Abono: " . $CargoAbono;
    echo "<br>Importe: " . $importe;
    echo "<br>Saldo: " . $saldo;
    echo "<br>Referencia: " . $referencia;
    echo "<br>Concepto: " . $concepto;
    echo "<br>Nombre Ordenante: " . $nombre_ordenante;
    echo "<br>Fecha de Registro: " . $fecha_registro;
    echo "<br>Registrante: " . $registrante;
    echo "<br>Archivo Procesado: " . $archivo_procesado;
    echo "<br>Estado: " . $Estado;
    echo "<br>Banco: " . $Banco;
    echo "<br>Cliente: " . $Cliente;
    echo "<br>-----------------------------------";

    if ($CargoAbono == "+") {
        echo "<br>Es un abono";
        $HELPER_CARGO_ABONO = "Abono";
    } else {
        echo "<br>Es un cargo";
        $HELPER_CARGO_ABONO = "Cargo";
    }

    /// Insertar en la base de datos
    $sql = "INSERT INTO movimientos_santander (Fecha, Descripcion, $HELPER_CARGO_ABONO, Saldo, Referencia, Concepto, Cliente, Fecha_Registro, Registrante, Estado, Archivo_procesado) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssddsssssss", $fecha, $descripcion, $importe, $saldo, $referencia, $concepto, $nombre_ordenante, $fecha_registro, $registrante, $Estado, $archivo_procesado);
    
    
    $stmt->execute();
    $stmt->close();

}

$conn->close();

header ("Location: /Front/ListadoDeRegistros.php?Banco=Santander");

/// Cuando "Cargo/Abono" es "+" Es un abono
/// Cuando "Cargo/Abono" es "-" Es un cargo




?>