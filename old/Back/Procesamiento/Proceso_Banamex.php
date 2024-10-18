<?php

include '../Config/config.php';

$Banco = trim($_GET['Banco']);

echo "<br>Se está recibiendo el Banco: " . $Banco;

/// Obtener la información del arreglo registros
$registros = $_POST['registros']; // Todos los registros

echo "<br>Registros: ";
print_r($registros);

// Recorrer el arreglo de registros
foreach ($registros as $registro) {
    $Fecha = $registro['fecha'];
    $Descripcion = $registro['descripcion'];
    
    // Eliminar comas de los valores de depositos, retiros y saldo
    $Depositos = isset($registro['depositos']) ? str_replace(',', '', $registro['depositos']) : 0;
    $Retiros = isset($registro['retiros']) ? str_replace(',', '', $registro['retiros']) : 0;
    $Saldo = isset($registro['saldo']) ? str_replace(',', '', $registro['saldo']) : 0;
    
    $Archivo_Procesado = $registro['archivo_procesado'];
    $Cliente = isset($registro['Cliente']) ? $registro['Cliente'] : 'Sin Cliente';

    echo "<br>Fecha: " . $Fecha;
    echo "<br>Descripcion: " . $Descripcion;
    echo "<br>Depositos: " . $Depositos;
    echo "<br>Retiros: " . $Retiros;
    echo "<br>Saldo: " . $Saldo;
    echo "<br>Archivo_Procesado: " . $Archivo_Procesado;
    echo "<br>Cliente: " . $Cliente;

    // Insertar los registros en la base de datos
    $sql = "INSERT INTO movimientos_banamex (Fecha, Descripcion, Depositos, Retiros, Saldo, Fecha_Registro, Responsable, Archivo_Procesado, 
    Estado, Banco, Cliente) 
    VALUES ('$Fecha', '$Descripcion', '$Depositos', '$Retiros', '$Saldo', NOW(), 'Usuario', '$Archivo_Procesado', 'Pendiente', '$Banco', '$Cliente')";

    if ($conn->query($sql) === TRUE) {
        echo "<br>Registro insertado correctamente";
    } else {
        echo "<br>Error: " . $sql . "<br>" . $conn->error;
    }
}

header ("Location: /Front/ListadoDeRegistros.php?Banco=Banamex");
?>