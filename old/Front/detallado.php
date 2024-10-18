<?php
// Database connection
include  '../Back/Config/config.php';
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get id_pago from URL
$id_pago = $_GET['id_pago'];

// Fetch record
$sql = "SELECT * FROM movimientos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pago);
$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();

// Obtener información adicional de la captura de pago
$sql = "SELECT * FROM capturadepago WHERE Id_Pago = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id_pago);
$stmt->execute();
$result_Pago = $stmt->get_result();
$record_Pago = $result_Pago->fetch_assoc();


$stmt->close();
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detalle del Pago</title>
    <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .fade-in {
            animation: fadeIn 1s;
        }

        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }
    </style>
</head>

<body>
    <?php include '../navbar.php'; ?>
    <!-- Boton para regresar al listado de registros -->
    <div class="container mt-5">
        <a href="/index.php" class="btn btn-primary">Regresar al inicio</a>
    </div>
    <div class="container mt-5 fade-in">
        <h2 class="mb-4">Detalle del Pago</h2>
        <hr>
        <?php if ($record): 
            $Banco = $record['Banco'];
            ?>
            <div class="row">
                <div class="col-md-6">
                    <div class="card-body">
                        <h5 class="card-title"><strong>ID Pago:</strong> <?php echo $record['id']; ?></h5>
                        <hr>
                        <p class="card-text"><strong>Descripción:</strong> <?php echo $record['descripcion']; ?></p>
                        <?php if ($record['debito']): ?>
                            <p class="card-text"><strong>Monto Débito:</strong> <?php echo $record['debito']; ?></p>
                        <?php endif; ?>
                        <?php if ($record['credito']): ?>
                            <p class="card-text"><strong>Monto Crédito:</strong> <?php echo $record['credito']; ?></p>
                        <?php endif; ?>
                        <p class="card-text"><strong>Estado:</strong> <?php echo $record['Estado']; ?></p>
                        <p class="card-text"><strong>Fecha de Registro:</strong> <?php echo $record['Fecha_Registro']; ?></p>
                        <p class="card-text"><strong>Registrante:</strong> <?php echo $record['Registrante']; ?></p>
                        <p class="card-text"><strong>Saldo: </strong> <?php echo $record['saldo']; ?></p>
                        <p class="card-text"><strong>Banco: </strong> <?php echo $record['Banco']; ?></p>
                        
                    </div>
                </div>
                <div class="col-md-6">
                        <div class="card-body">
                            <h5 class="card-title">Información Adicional</h5>
                            <hr>
                            <p class="card-text"><strong>Cliente:</strong> <?php echo $record_Pago['Cliente']; ?></p>
                            <p class="card-text"><strong>Número de Factura:</strong> <?php echo $record_Pago['Numero_Factura']; ?></p>
                            <p class="card-text"><strong>Tipo:</strong> <?php echo $record_Pago['Tipo']; ?></p>
                            <p class="card-text"><strong>Número de Pago:</strong> <?php echo $record_Pago['Numero_Pago']; ?></p>
                            <p class="card-text"><strong>Fecha de Registro:</strong> <?php echo $record_Pago['Fecha_Registro']; ?></p>
                            <p class="card-text"><strong>Solicitante:</strong> <?php echo $record_Pago['Solicitante']; ?></p>
                            <!-- boton para descargar una copia del archivo XML  href="/Back/xmlFiles/ echo -->
                            <a href="/Back/xmlFiles/<?php echo $record_Pago['XML_FILE']; ?>" class="btn btn-info">Descargar XML</a>
                        </div>
                    </div>
            </div>
        <?php else: ?>
            <div class="alert alert-danger">No se encontró el registro.</div>
        <?php endif; ?>

            <hr>
        <a href="ListadoDeRegistros.php?Banco=<?php echo $Banco; ?>" class="btn btn-primary">Regresar al listado de registros</a>
    </div>
    <br>
</body>

</html>