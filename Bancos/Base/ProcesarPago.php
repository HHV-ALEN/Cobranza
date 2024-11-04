<?php
$id = $_GET['id'];
require '../../Back/Config/config.php';
// Consulta para obtener el movimiento de Banamex
$sql = "SELECT * FROM movimientos_base WHERE Id = $id";
$result = $conn->query($sql);
$record = $result->fetch_assoc();

$Cliente = $record['Destinatario'];
if ($record):
    ?>

    <!DOCTYPE html>
    <html lang="en">

    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Proceso de Pago - Base</title>
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
                        <p class="card-text"><strong>Operación:</strong> <?php echo $record['Operacion']; ?></p>
                        <?php if ($record['Abono']): ?>
                            <p class="card-text"><strong>Abono:</strong> <?php echo $record['Abono']; ?></p>
                        <?php endif; ?>
                        <?php if ($record['Cargo']): ?>
                            <p class="card-text"><strong>Cargo:</strong> <?php echo $record['Cargo']; ?></p>
                        <?php endif; ?>
                        <p class="card-text"><strong>Saldo:</strong> <?php
                        // Mostrar saldo en formato moneda
                        echo "$" . number_format($record['Saldo'], 2); ?></p>

                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card-body">
                        <p class="card-text"><strong>Estado:</strong> <?php echo $record['Estado']; ?></p>
                        <p class="card-text"><strong>Fecha de Registro:</strong>
                            <?php echo $record['Fecha_Registro']; ?></p>
                        <p class="card-text"><strong>Banco:</strong> <?php echo "BASE" ?></p>
                        <p class="card-text"><strong>Responsable:</strong> <?php echo $record['Registrante']; ?></p>
                        <p class="card-text"><strong>Cliente:</strong> <?php echo $record['Destinatario']; ?></p>
                    </div>
                </div>
            </div>
            <hr>
        <?php else: ?>
            <div class="alert alert-danger">No se encontró el registro.</div>
        <?php endif; ?>
    </div>
    <!-- Verificar si el registro del pago existe, si no mostrar el formulario para registrar el pago -->
    <?php
    // Consulta para verificar si el pago ya ha sido registrado
    $sql = "SELECT * FROM capturadepago WHERE Id_Pago = $id";
    $result = $conn->query($sql);
    $record = $result->fetch_assoc();

    if ($record):
        ?>
        <!-- Mostrar pequeña card cuando el pago ya ha sido registrado -->
        <div class="container mt-5 fade-in">
            <div class="card text-dark bg-warning  mb-3">
                <div class="card-body">
                    <h5 class="card-title text-center">Pago Registrado</h5>
                </div>
            </div>
        </div>

        <div class="container mt-5 fade-in">
            <h2 class="mb-4 text-center">Información del Pago</h2>
            <hr>
            <form action="/Back/ProcesoPagos/Proceso_Pago_Base.php?id_pago=<?php echo $id ?> &Metodo=Actualizar"
                method="POST" enctype="multipart/form-data">
                <input type="hidden" name="Banco" value="<?php echo $record['Banco']; ?>">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="cliente" name="cliente"
                                value="<?php echo $record['Cliente']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="tipo">Tipo</label>
                            <select class="form-control" id="tipo" name="tipo" required>
                                <option value="PUE" <?php if ($record['Tipo'] === 'PUE')
                                    echo 'selected'; ?>>PUE</option>
                                <option value="PPD" <?php if ($record['Tipo'] === 'PPD')
                                    echo 'selected'; ?>>PPD</option>
                            </select>

                            <!-- Campo para cargar el archivo XML, inicialmente oculto -->
                            <div class="row" id="campo_xml" style="display: none;">
                                <div class="col-md-12">
                                    <div class="form-group text-center">
                                        <label for="archivo_xml">Subir XML</label>
                                        <input type="file" class="form-control" id="archivo_xml" name="archivo_xml"
                                            accept=".xml">
                                    </div>
                                </div>
                            </div>

                            <?php if ($record['Tipo'] === 'PPD'): ?>
                                <!-- Show download button for existing XML -->
                                <a href="../../Back/xmlFiles/<?php echo $record['XML_FILE']; ?>"
                                    class="btn btn-primary mt-2">Descargar XML Actual</a>
                                <p class="mt-2">Cambiar XML:</p>
                                <input type="file" class="form-control" id="archivo_xml" name="archivo_xml" accept=".xml">
                            <?php endif; ?>

                        </div>
                    </div>

                    <div class="col-md-6 text-center">
                        <div class="form-group">
                            <label for="numero_pago">Número de Pago</label>
                            <input type="text" class="form-control" id="numero_pago" name="numero_pago"
                                value="<?php echo $record['Numero_Pago']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label for="numero_factura">Número de Factura</label>
                            <input type="text" class="form-control" id="numero_factura" name="numero_factura"
                                value="<?php echo $record['Numero_Factura']; ?>" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn btn-warning">Guardar Cambios</button>
            </form>
        </div>

    <?php else: ?>

        <!-- Información Adicional -->
        <div class="container mt-5 fade-in">
            <h2 class="mb-4 text-center">Registrar Pago Realizado</h2>
            <form action="/Back/ProcesoPagos/Proceso_Pago_Base.php?id_pago=<?php echo $id ?>&Metodo=Registrar" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="Banco" value="Base">

                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group text-center">
                            <label for="nombre">Nombre</label>
                            <input type="text" class="form-control" id="cliente" name="cliente"
                                value="<?php echo $Cliente ?>" Disabled>
                            <input type="hidden" name="cliente" value="<?php echo $Cliente ?>">
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
                            <input type="text" class="form-control" id="numero_pago" name="numero_pago"
                                placeholder="Ingrese Número de Pago..." required>
                        </div>
                        <div class="form-group">

                            <label for="numero_factura">Número de Factura</label>
                            <input type="text" placeholder="Ingrese Número de Factura..." class="form-control"
                                id="numero_factura" name="numero_factura" required>
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

    <?php endif; ?>


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