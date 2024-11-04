<?php
$id = $_GET['id'];
require '../../Back/Config/config.php';
// Consulta para obtener el movimiento de Banamex
$sql = "SELECT * FROM movimientos_banamex WHERE Id = $id";
$result = $conn->query($sql);
$record = $result->fetch_assoc();
if ($record):
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Proceso de Pago - Banamex</title>
        <link href="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css" rel="stylesheet">
    
    </head>

    <body>
        <?php include '../../navbar.php'; ?>
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
    <!-- Información Adicional -->
    <div class="container mt-5 fade-in">
        <h2 class="mb-4 text-center">Registrar Información Adicional</h2>
        <form action="/Back/ProcesoPagos/Proceso_Pago_Banamex.php?id_pago=<?php echo $record['Id']; ?>" method="POST"
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
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.5.4/dist/umd/popper.min.js"></script>
    <script src="https://stackpath.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>

    <script>
        // Mostrar u ocultar el campo XML según la selección del tipo de pago
        document.getElementById('tipo').addEventListener('change', function () {
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