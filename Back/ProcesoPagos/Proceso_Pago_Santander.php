<?php 

require '../Config/config.php';

// Asegurarse de que se recibió una solicitud POST
$FechaActual = date('Y-m-d H:i:s');
// Asegurarse de que se recibió una solicitud POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Recibir los datos enviados por el formulario

    // ID del pago (desde la URL)
    $id_pago = $_GET['id_pago'] ?? null;

    // Banco (input hidden)
    $banco = $_POST['Banco'] ?? null;

    // Cliente (input hidden)
    $cliente = $_POST['cliente'] ?? null;

    // Tipo de pago (PUE o PPD)
    $tipo = $_POST['tipo'] ?? null;

    // Número de pago
    $numero_pago = $_POST['numero_pago'] ?? null;

    // Número de factura
    $numero_factura = $_POST['numero_factura'] ?? null;

    $Metodo = $_GET['Metodo'] ?? null;



    // Archivo XML (si fue subido)
    if (isset($_FILES['archivo_xml']) && $_FILES['archivo_xml']['error'] == 0) {
        // Datos del archivo subido
        $archivo_xml = $_FILES['archivo_xml']['tmp_name'];
        $nombre_archivo_xml = $_FILES['archivo_xml']['name'];
        echo "Archivo XML subido: $nombre_archivo_xml<br>";

        // Aquí puedes mover el archivo a una carpeta específica si lo necesitas
        $ruta_destino = '../xmlFiles/' . $nombre_archivo_xml;
        move_uploaded_file($archivo_xml, $ruta_destino);
    }

    // Archivo XML (si fue subido)
    if (isset($_FILES['archivo_xml']) && $_FILES['archivo_xml']['error'] == 0) {
        // Datos del archivo subido
        $archivo_xml = $_FILES['archivo_xml']['tmp_name'];
        $nombre_archivo_xml = $_FILES['archivo_xml']['name'];
        echo "Archivo XML subido: $nombre_archivo_xml<br>";

        // Aquí puedes mover el archivo a una carpeta específica si lo necesitas
        $ruta_destino = '../xmlFiles/' . $nombre_archivo_xml;
        move_uploaded_file($archivo_xml, $ruta_destino);
    }

    // Aquí puedes procesar los datos recibidos y guardarlos en la base de datos o realizar las acciones necesarias
    // Ejemplo de cómo mostrar los valores recibidos:
    echo "ID Pago: $id_pago<br>";
    echo "Banco: $banco<br>";
    echo "Cliente: $cliente<br>";
    echo "Tipo: $tipo<br>";
    echo "Número de Pago: $numero_pago<br>";
    echo "Número de Factura: $numero_factura<br>";

    /// EL metodo corresponde a si se va a actualizar o insertar
    echo "Metodo: $Metodo<br>";
    if ($Metodo == 'Registrar') {
        echo "<br>-------------------<br>";
        echo "Registro del Pago<br>";
        // Verificar si se subió el archivo XML
        if (isset($archivo_xml)) {
            echo "Archivo XML subido: $nombre_archivo_xml<br>";
        } else {
            echo "No se subió ningún archivo XML.<br>";
        }

        /// Insersción A la base de datos
        $Insert_SQL = "INSERT INTO capturadepago (Id_Pago, Numero_Pago, Numero_Factura, Nombre, Tipo, Solicitante, Fecha_Registro, Cliente, Banco, XML_FILE)
        VALUES ('$id_pago', '$numero_pago', '$numero_factura', 'Santander', '$tipo', 'Usuario', '$FechaActual', '$cliente', '$banco', '$nombre_archivo_xml')";

        if ($conn->query($Insert_SQL) === TRUE) {
            echo "Registro creado correctamente";
            /// Actualizar estado del pago en la tabla de movimientos
            $Update_SQL = "UPDATE movimientos_santander SET Estado = 'Procesado' WHERE id = '$id_pago'";
            if ($conn->query($Update_SQL) === TRUE) {
                echo "Estado actualizado correctamente";
            } else {
                echo "Error: " . $Update_SQL . "<br>" . $conn->error;
            }
        } else {
            echo "Error: " . $Insert_SQL . "<br>" . $conn->error;
        }

    } elseif ($Metodo == 'Actualizar') {
        echo "Se va a actualizar el pago<br>";
        $Update_SQL = "UPDATE capturadepago SET Numero_Pago = '$numero_pago', Numero_Factura = '$numero_factura',Tipo = '$tipo', XML_FILE = '$nombre_archivo_xml' WHERE Id_Pago = '$id_pago'";
        if ($conn->query($Update_SQL) === TRUE) {
            echo "Registro actualizado correctamente";
        } else {
            echo "Error: " . $Update_SQL . "<br>" . $conn->error;
        }
    } else {
        echo "No se especificó un método válido<br>";
    }
} else {
    echo "Método de solicitud no válido.";
}

//Regresar Al listado
header ("Location: ../../Bancos/Santander/Listado.php");

?>