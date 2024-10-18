<?php
require_once '../Config/config.php';

$Nombre = $_POST['cliente'];
$Tipo = $_POST['tipo'];
$id_pago = $_POST['id_pago'];
$numero_factura = $_POST['numero_factura'];
$numero_pago = $_POST['numero_pago'];
$Usuario = "Usuario_2";
$Fecha = date("Y-m-d H:i:s");
$Banco = $_POST['Banco'];

// Verificar si se ha enviado un archivo
if (isset($_FILES['archivo_xml']) && $_FILES['archivo_xml']['error'] == UPLOAD_ERR_OK) {
    // Obtener información del archivo
    $nombreArchivo = $_FILES['archivo_xml']['name'];
    $tipoArchivo = $_FILES['archivo_xml']['type'];
    $tamanoArchivo = $_FILES['archivo_xml']['size'];
    $tmpArchivo = $_FILES['archivo_xml']['tmp_name'];

    // Verificar si el archivo es de tipo XML
    if ($tipoArchivo == 'text/xml' || $tipoArchivo == 'application/xml') {
        // Definir la ruta donde se guardará el archivo (ajusta la ruta según tu estructura)
        $rutaDestino = '../xmlFiles/' . basename($nombreArchivo);

        // Mover el archivo subido a la carpeta de destino
        if (move_uploaded_file($tmpArchivo, $rutaDestino)) {
            echo "El archivo XML se ha subido correctamente.";
            // Aquí puedes insertar la ruta del archivo en la base de datos si es necesario
            // Dia actual
            $Fecha = date("Y-m-d H:i:s");
            $sql = "INSERT INTO archivo_xml (Nombre, Id_Pago, Banco, Registrante, Fecha_Registro)
                    VALUES (?, ?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("sisss", $nombreArchivo, $id_pago, $Banco, $Usuario, $Fecha);
            if ($stmt->execute()) {
                echo "Registro de archivo XML exitoso.";
            } else {
                echo "Error al registrar el archivo XML: " . $stmt->error;
            }
            $stmt->close();
        } else {
            echo "Hubo un error al mover el archivo a su destino.";
        }
    } else {
        echo "El archivo no es un XML válido.";
    }
} else {
    echo "No se ha subido ningún archivo XML o hubo un error en la subida.";
}

// Asegúrate de que estás comparando correctamente el banco con 'Banistmo'
if ($Banco == 'Banistmo') {
    echo "<br>----------------------<br>";
    echo "Nombre: " . $Nombre . "<br>";
    echo "Tipo: " . $Tipo . "<br>";
    echo "ID Pago: " . $id_pago . "<br>";
    echo "Solicitante: " . $Usuario . "<br>";
    echo "Número de Factura: " . $numero_factura . "<br>";
    echo "Número de Pago: " . $numero_pago . "<br>";
    echo "Fecha: " . $Fecha . "<br>";
    echo "<br>----------------------<br>";

    // Preparar la consulta
    $sql = "INSERT INTO capturadepago (Nombre, Tipo, id_pago, Solicitante, Numero_Factura, Numero_Pago, Fecha_Registro, Banco , Cliente, XML_FILE) 
    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";

    // Asegurarte de que estás usando la conexión a la base de datos
    // Supongamos que $conn es tu conexión MySQLi, asegúrate de tenerla definida antes
    if ($stmt = $conn->prepare($sql)) {
        // Vincular parámetros a la consulta
        $stmt->bind_param("ssisssssss", $Nombre, $Tipo, $id_pago, $Usuario, $numero_factura, $numero_pago, $Fecha, $Banco, $Nombre, $rutaDestino);

        // Ejecutar la declaración
        if ($stmt->execute()) {
            echo "Pago registrado correctamente.";
        } else {
            echo "Error al registrar el pago: " . $stmt->error;
        }

        // Cerrar la declaración
        $stmt->close();


        // Cambiar el estado del registro de la tabla  movimientos con id = id_pago
    $sql = "UPDATE movimientos SET Estado = 'Capturado' WHERE id = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id_pago);

    if ($stmt->execute()) {
        echo "Estado actualizado correctamente.";
    } else {
        echo "Error al actualizar el estado: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

    } else {
        // Error al preparar la consulta
        echo "Error en la preparación de la consulta: " . $conn->error;
    }

    //header ("Location: /Front/ListadoDeRegistros.php?Banco=Banistmo");
}
 elseif ($Banco == 'Santander') {
    echo "<br>----------------------<br>";
    echo "Nombre: " . $Nombre . "<br>";
    echo "Tipo: " . $Tipo . "<br>";
    echo "ID Pago: " . $id_pago . "<br>";
    echo "Solicitante: " . $Usuario . "<br>";
    echo "Número de Factura: " . $numero_factura . "<br>";
    echo "Número de Pago: " . $numero_pago . "<br>";
    echo "Fecha: " . $Fecha . "<br>";
    echo "<br>----------------------<br>";

    /// Insertar en la base de datos
    $sql = "INSERT INTO capturadepago (Id_Pago, Numero_Pago, Numero_Factura, Nombre, Tipo, Solicitante, Fecha_Registro, Cliente, Banco ,XML_FILE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("iissssssss", $id_pago, $numero_pago, $numero_factura, $Nombre, $Tipo, $Usuario, $Fecha, $Nombre, $Banco, $rutaDestino);

    if ($stmt->execute()) {
        echo "Pago registrado correctamente.";
    } else {
        echo "Error al registrar el pago: " . $stmt->error;
    }

    // Cambiar el estado del registro de la tabla  movimientos con id = id_pago
    $sql = "UPDATE movimientos_santander SET Estado = 'Capturado' WHERE id = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id_pago);

    if ($stmt->execute()) {
        echo "Estado actualizado correctamente.";
    } else {
        echo "Error al actualizar el estado: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
    //header("Location: /Front/ListadoDeRegistros.php?Banco=Santander");
} 

elseif ($Banco == 'Banamex') {
    echo "<br>----------------------<br>";
    echo "Nombre: " . $Nombre . "<br>";
    echo "Tipo: " . $Tipo . "<br>";
    echo "ID Pago: " . $id_pago . "<br>";
    echo "Solicitante: " . $Usuario . "<br>";
    echo "Número de Factura: " . $numero_factura . "<br>";
    echo "Número de Pago: " . $numero_pago . "<br>";
    echo "Fecha: " . $Fecha . "<br>";
    echo "<br>----------------------<br>";

    /// Insertar en la base de datos
    $sql = "INSERT INTO capturadepago (Id_Pago, Numero_Pago, Numero_Factura, Nombre, Tipo, Solicitante, Fecha_Registro, Cliente, Banco ,XML_FILE) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    

    $stmt = $conn->prepare($sql);

    $stmt->bind_param("iissssssss", $id_pago, $numero_pago, $numero_factura, $Nombre, $Tipo, $Usuario, $Fecha, $Nombre, $Banco, $rutaDestino);

    if ($stmt->execute()) {
        echo "Pago registrado correctamente.";
    } else {
        echo "Error al registrar el pago: " . $stmt->error;
    }

    // Cambiar el estado del registro de la tabla  movimientos con id = id_pago
    $sql = "UPDATE movimientos_banamex SET Estado = 'Capturado' WHERE id = ?";
    $stmt = $conn->prepare($sql);

    $stmt->bind_param("i", $id_pago);

    if ($stmt->execute()) {
        echo "Estado actualizado correctamente.";
    } else {
        echo "Error al actualizar el estado: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();

} else {
    echo "Error al registrar el pago: " . $conn->error;
}

header("Location: /Front/ListadoDeRegistros.php?Banco=$Banco");

?>