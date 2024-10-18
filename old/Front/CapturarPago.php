<?php
// Database connection
include '../Back/Config/config.php';
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get id_pago from URL
$id_pago = $_GET['id_pago'];
$Banco = $_GET['Banco'];

if ($Banco == 'Banistmo') {
    // Fetch record
    $sql = "SELECT * FROM movimientos WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pago);
} elseif ($Banco == 'Santander') {
    $sql = "SELECT * FROM movimientos_santander WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pago);
} elseif ($Banco == 'Banamex') {
    $sql = "SELECT * FROM movimientos_banamex WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pago);
}elseif ($Banco == 'BASE'){
    $sql = "SELECT * FROM movimientos_base WHERE Id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id_pago);
}
 else {
    echo "No se encontró el registro";
} 

$stmt->execute();
$result = $stmt->get_result();
$record = $result->fetch_assoc();




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
    <!-- Navbar -->
    <?php include '../navbar.php'; ?>
    <!-- Boton para regresar al listado de registros -->
    <div class="container mt-5">
        <a href="/index.php" class="btn btn-primary">Regresar al inicio</a>
    </div>
    <div class="container mt-5 fade-in">
        <h2 class="mb-4 text-center">Detalle del Pago</h2>

        <?php
        switch ($Banco) {
            case 'Banistmo':
                if ($record): ?>
                    <div class="card mb-4">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <div class="card-body">
                                    <h5 class="card-title">ID Pago: <?php echo $record['id']; ?></h5>
                                    <p class="card-text"><strong>Descripción:</strong> <?php echo $record['descripcion']; ?></p>
                                    <?php if ($record['debito']): ?>
                                        <p class="card-text"><strong>Monto Débito:</strong> <?php echo $record['debito']; ?></p>
                                    <?php endif; ?>

                                    <?php if ($record['credito']): ?>
                                        <p class="card-text"><strong>Monto Crédito:</strong> <?php echo $record['credito']; ?></p>
                                    <?php endif; ?>
                                    <p class="card-text"><strong>Registrante:</strong> <?php echo $record['Registrante']; ?></p>
                                    <p class="card-text"><strong>Fecha:</strong> <?php echo $record['fecha']; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body">
                                    <p class="card-text"><strong>Archivo Procesado:</strong>
                                        <?php echo $record['archivo_procesado']; ?></p>
                                    <p class="card-text"><strong>Estado:</strong> <?php echo $record['Estado']; ?></p>
                                    <p class="card-text"><strong>Banco:</strong> <?php echo $record['Banco']; ?></p>
                                    <p class="card-text"><strong>Cliente:</strong> <?php echo $record['Cliente']; ?></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php else: ?>
                        <div class="alert alert-danger">No se encontró el registro.</div>
                    <?php endif; ?>
                </div>
                <?php break;
            case 'Santander':
                if ($record): ?>
                    <div class="card mb-4">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <div class="card-body">
                                    <h5 class="card-title text-center">ID Pago: <?php echo $record['Id']; ?></h5>
                                    <p class="card-text"><strong>Fecha:</strong> <?php echo $record['Fecha']; ?></p>
                                    <p class="card-text"><strong>Descripción:</strong> <?php echo $record['Descripcion']; ?></p>
                                    <?php if ($record['Abono']): ?>
                                        <p class="card-text"><strong>Monto Abono:</strong> <?php echo $record['Abono']; ?></p>
                                    <?php endif; ?>
                                    <?php if ($record['Cargo']): ?>
                                        <p class="card-text"><strong>Monto Cargo:</strong> <?php echo $record['Cargo']; ?></p>
                                    <?php endif; ?>
                                    <p class="card-text"><strong>Saldo:</strong> <?php echo $record['Saldo']; ?></p>
                                    <p class="card-text"><strong>Registrante:</strong> <?php echo $record['Registrante']; ?></p>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body">
                                    <p class="card-text"><strong>Estado:</strong> <?php echo $record['Estado']; ?></p>
                                    <p class="card-text"><strong>Fecha de Registro:</strong>
                                        <?php echo $record['Fecha_Registro']; ?></p>
                                    <p class="card-text"><strong>Banco:</strong> <?php echo "Santander" ?></p>
                                    <p class="card-text"><strong>Cliente:</strong> <?php echo $record['Cliente']; ?></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php 
                $record['Banco'] = 'Santander';
                else: ?>
                        <div class="alert alert-danger">No se encontró el registro.</div>
                    <?php endif; ?>
                </div>
                <?php break;
            case 'Banamex':
                if ($record): ?>
                    <div class="card mb-4">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <div class="card-body">
                                    <h5 class="card-title text-center">ID Pago: <?php echo $record['Id']; ?></h5>
                                    <p class="card-text"><strong>Fecha:</strong> <?php echo $record['Fecha']; ?></p>
                                    <p class="card-text"><strong>Descripción:</strong> <?php echo $record['Descripcion']; ?></p>
                                    <?php if ($record['Depositos']): ?>
                                        <p class="card-text"><strong>Depositos:</strong> <?php echo $record['Depositos']; ?></p>
                                    <?php endif; ?>
                                    <?php if ($record['Retiros']): ?>
                                        <p class="card-text"><strong>Monto Cargo:</strong> <?php echo $record['Retiros']; ?></p>
                                    <?php endif; ?>
                                    <p class="card-text"><strong>Saldo:</strong> <?php echo $record['Saldo']; ?></p>

                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="card-body">
                                    <p class="card-text"><strong>Estado:</strong> <?php echo $record['Estado']; ?></p>
                                    <p class="card-text"><strong>Fecha de Registro:</strong>
                                        <?php echo $record['Fecha_Registro']; ?></p>
                                    <p class="card-text"><strong>Banco:</strong> <?php echo "Banamex" ?></p>
                                    <p class="card-text"><strong>Responsable:</strong> <?php echo $record['Responsable']; ?></p>
                                    <p class="card-text"><strong>Cliente:</strong> <?php echo $record['Cliente']; ?></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php else: ?>
                        <div class="alert alert-danger">No se encontró el registro.</div>
                    <?php endif; ?>
                </div>
                <?php break;
            case 'BASE':
                if ($record): ?>
                    <div class="card mb-4">
                        <div class="row text-center">
                            <div class="col-md-6">
                                <div class="card-body">
                                    <h5 class="card-title text-center">ID Pago: <?php echo $record['Id']; ?></h5>
                                    <p class="card-text"><strong>Fecha:</strong> <?php echo $record['Fecha']; ?></p>
                                    <p class="card-text"><strong>Operación:</strong> <?php echo $record['Operacion']; ?></p>
                                    <?php if ($record['Cargo']): ?>
                                        <p class="card-text"><strong>Cargo:</strong> <?php echo $record['Cargo']; ?></p>
                                    <?php endif; ?>
                                    <?php if ($record['Abono']): ?>
                                        <p class="card-text"><strong>Abono:</strong> <?php echo $record['Abono']; ?></p>
                                    <?php endif; ?>
                                    <p class="card-text"><strong>Saldo:</strong> <?php echo $record['Saldo']; ?></p>

                                </div>
                            </div>

                            <div class="col-md-6">
                                <div class="card-body">
                                    <p class="card-text"><strong>Registrante:</strong> <?php echo $record['Registrante']; ?></p>
                                    <p class="card-text"><strong>Estado:</strong> <?php echo $record['Estado']; ?></p>
                                    <p class="card-text"><strong>Banco:</strong> <?php echo "BASE" ?></p>
                                    <p class="card-text"><strong>Cliente:</strong> <?php echo $record['Destinatario']; ?></p>
                                </div>
                            </div>
                        </div>
                        <hr>
                    <?php else: ?>
                        <div class="alert alert-danger">No se encontró el registro.</div>
                    <?php endif; ?>
                </div>
                <?php
               $record['Cliente'] = $record['Destinatario'];
                break;

        }
        ?>

        <!-- Información Adicional -->
        <div class="container mt-5 fade-in">
            <h2 class="mb-4 text-center">Registrar Información Adicional</h2>
            <form action="/Back/Procesamiento/RegistrarPago.php?id_pago=<?php echo $record['Id']; ?>" method="POST"
                enctype="multipart/form-data">
                <input type="hidden" name="Banco" value="<?php echo $record['Banco']; ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="cliente" name="cliente"
                                value="<?php echo $record['Cliente']; ?>" Disabled>
                            <input type="hidden" name="cliente" value="<?php echo $record['Cliente']; ?>">
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select class="form-control" id="tipo" name="tipo" required>
                                <option value="PUE">PUE</option>
                                <option value="PPD">PPD</option>
                            </select>
                        </div>
                    </div>
                    <div class="col-md-6 text-center">
                        <div class="form-group">
                            <label for="numero_pago">Número de Pago</label>
                            <input type="text" class="form-control" id="numero_pago" name="numero_pago" required>
                        </div>
                        <div class="form-group">
                            <label for="numero_factura">Número de Factura</label>
                            <input type="text" class="form-control" id="numero_factura" name="numero_factura" required>
                        </div>
                    </div>
                </div>

                <!-- Campo para cargar el archivo XML, inicialmente oculto -->
                <div class="row" id="campo_xml" style="display: none;">
                    <div class="col-md-12">
                        <div class="form-group text-center">
                            <label for="archivo_xml">Subir XML</label>
                            <input type="file" class="form-control" id="archivo_xml" name="archivo_xml" accept=".xml">
                        </div>
                    </div>
                </div>

                <input type="hidden" name="id_pago" value="<?php echo $id_pago; ?>">
                <button type="submit" class="btn btn-success">Registrar</button>

            </form>
        </div>
        <br>
    </div>
    <br>
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
// Mostrar u ocultar el campo XML según la selección del tipo de pago
document.getElementById('tipo').addEventListener('change', function() {
    var tipoPago = this.value;
    var campoXML = document.getElementById('campo_xml');
    
    if (tipoPago === 'PPD') {
        // Mostrar campo XML
        campoXML.style.display = 'block';
    } else {
        // Ocultar campo XML
        campoXML.style.display = 'none';
    }
});
</script>

</body>

</html>