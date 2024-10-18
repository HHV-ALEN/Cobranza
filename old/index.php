<?php

include 'Back/Procesamiento/procesar_archivo.php';
$Banco = $_POST['Banco'] ?? '';

?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Procesar PDF</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Custom CSS for animations -->
    <style>
        /* Estilos de animación y diseño minimalista */
        body {
            background-color: #f8f9fa;
            font-family: 'Arial', sans-serif;
        }

        .container {
            margin-top: 50px;
        }

        .form-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            animation: fadeIn 0.5s ease-in-out;
        }

        .file-upload-btn {
            transition: background-color 0.3s ease;
        }

        .file-upload-btn:hover {
            background-color: #0d6efd;
            color: white;
        }

        .result-container {
            background-color: #fff;
            border-radius: 10px;
            padding: 30px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: 20px;
            animation: fadeInUp 0.5s ease-in-out;
        }

        /* Animaciones */
        @keyframes fadeIn {
            from {
                opacity: 0;
            }

            to {
                opacity: 1;
            }
        }

        @keyframes fadeInUp {
            from {
                opacity: 0;
                transform: translateY(20px);
            }

            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>

<body>
    <!-- Navbar -->
    <?php include 'navbar.php'; ?>
    <div class="form-container p-4">
        <h3 class="text-center">Bancos disponibles:</h3>
        <hr>
        <div class="row">
            <div class="col-md-3 text-center">
                <a href="Front/ListadoDeRegistros.php?Banco=Banistmo" class="btn btn-primary">Banistmo</a>
            </div>
            <div class="col-md-3 text-center">
                <a href="Front/ListadoDeRegistros.php?Banco=Banamex" class="btn btn-danger">Banamex</a>
            </div>
            <div class="col-md-3 text-center">
                <a href="Front/ListadoDeRegistros.php?Banco=Santander" class="btn btn-danger">Santander</a>
            </div>
            <div class="col-md-3 text-center">
                <a href="Front/ListadoDeRegistros.php?Banco=BASE" class="btn btn-warning">BASE</a>

            </div>

        </div>
    </div>

    <div class="container">
        <!-- Formulario para subir el archivo PDF -->
        <div class="form-container p-4">
            <h2 class="text-center mb-4">Subir y Procesar PDF</h2>
            <form action="" method="post" enctype="multipart/form-data" class="text-center">

                <div class="row">
                    <div class="col-md-6">
                        <label for="pdf_file" class="form-label">Selecciona un archivo :</label>
                        <select name="Banco" id="Banco" class="form-select mb-3" required>
                            <option value="Banistmo">Banistmo</option>
                            <option value="Santander">Santander</option>
                            <option value="Banamex">Banamex</option>
                            <option value="BASE">BASE</option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <label for="pdf_file" class="form-label">Selecciona un archivo PDF:</label>
                        <input type="file" class="form-control" name="pdf_file" id="pdf_file" accept="application/pdf"
                            required>
                    </div>
                </div>
                <button type="submit" class="btn btn-primary file-upload-btn">Subir y Procesar</button>
            </form>
        </div>

        <?php
        if ($Banco == 'Banistmo' && !empty($registros)) {
            ?>
            <div class="result-container">
                <h2>Registros Extraídos del PDF</h2>
                <form action="Back/Procesamiento/procesar_registros.php?Banco=Banistmo" method="post">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Crédito</th>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                                <th>Cliente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $key => $registro): ?>
                                <?php
                                // Formateamos la fecha
                                $fechaOriginal = $registro['fecha'];
                                $fechaObj = DateTime::createFromFormat('dmY', $fechaOriginal);
                                $fechaFormateada = $fechaObj ? $fechaObj->format('Y-m-d') : $fechaOriginal;
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="registros_credito[]" value="<?php echo $key; ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($fechaFormateada); ?></td>
                                    <td><?php echo htmlspecialchars($registro['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['monto']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['saldo']); ?></td>
                                    <td>
                                        <!-- Campo para agregar o modificar el cliente -->
                                        <input type="text" name="registros[<?php echo $key; ?>][Cliente]"
                                            value="<?php echo isset($registro['Cliente']) ? htmlspecialchars($registro['Cliente']) : ''; ?>"
                                            class="form-control" required>
                                    </td>
                                    
                                </tr>
                                <input type="hidden" name="registros[<?php echo $key; ?>][fecha]"
                                    value="<?php echo htmlspecialchars($fechaFormateada); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][descripcion]"
                                    value="<?php echo htmlspecialchars($registro['descripcion']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][monto]"
                                    value="<?php echo htmlspecialchars($registro['monto']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][saldo]"
                                    value="<?php echo htmlspecialchars($registro['saldo']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]"
                                    value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">
                            <?php endforeach; ?>

                        </tbody>

                    </table>

                    <br>
                    <button type="submit" class="btn btn-success w-100">Enviar registros seleccionados</button>
                </form>
            </div>


        <?php } elseif ($Banco == 'Santander' && !empty($registros)) { ?>
            <div class="result-container">
                <h2>Registros Extraídos del PDF</h2>
                <form action="Back/Procesamiento/Proceso_Santander.php?Banco=Santander" method="POST">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Cargo/Abono</th>
                                <th>Importe</th>
                                <th>Saldo</th>
                                <th>Concepto</th>
                                <th>Nombre Ordenante</th>

                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $key => $registro):
                                $FormattedDate = date("Y-m-d", strtotime($registro['fecha']));
                                ?>
                                <tr>
                                    <td><?php echo $FormattedDate ?></td>
                                    <td><?php echo htmlspecialchars($registro['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['CargoAbono']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['importe']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['saldo']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['concepto']); ?></td>
                                    <td>
                                        <!-- Campo Cliente será parte del array de registros -->
                                        <input type="text" name="registros[<?php echo $key; ?>][nombre_ordenante]"
                                            value="<?php echo isset($registro['nombre_ordenante']) ? htmlspecialchars($registro['nombre_ordenante']) : ''; ?>"
                                            class="form-control" required>
                                    </td>
                                </tr>
                                <!-- Mantén solo los campos ocultos que no necesiten ser editados -->
                                <input type="hidden" name="registros[<?php echo $key; ?>][fecha]"
                                    value="<?php echo $FormattedDate; ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][descripcion]"
                                    value="<?php echo htmlspecialchars($registro['descripcion']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][CargoAbono]"
                                    value="<?php echo htmlspecialchars($registro['CargoAbono']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][importe]"
                                    value="<?php echo htmlspecialchars($registro['importe']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][saldo]"
                                    value="<?php echo htmlspecialchars($registro['saldo']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][referencia]"
                                    value="<?php echo htmlspecialchars($registro['referencia']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][concepto]"
                                    value="<?php echo htmlspecialchars($registro['concepto']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]"
                                    value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <br>
                    <button type="submit" class="btn btn-success w-100">Enviar registros seleccionados</button>
                </form>
            </div>

        <?php } elseif ($Banco == 'Banamex' && !empty($registros)) { ?>
            <div class="result-container">
                <h2>Registros Extraídos del PDF</h2>
                <form action="Back/Procesamiento/Proceso_Banamex.php?Banco=Banamex" method="POST">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Fecha</th>
                                <th>Descripción</th>
                                <th>Depósitos</th>
                                <th>Saldo</th>
                                <th>Retiros</th>
                                <th>Moneda</th>
                                <th>Cliente</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $key => $registro):
                                $FormattedDate = date("Y-m-d", strtotime($registro['fecha']));
                                ?>
                                <tr>
                                    <td><?php echo $FormattedDate ?></td>
                                    <td><?php echo htmlspecialchars($registro['descripcion']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['depositos']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['saldo']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['retiros']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['moneda']); ?></td>
                                    <td>
                                        <!-- El nombre del campo Cliente será parte del array de registros -->
                                        <input type="text" name="registros[<?php echo $key; ?>][Cliente]"
                                            value="<?php echo isset($registro['Cliente']) ? htmlspecialchars($registro['Cliente']) : ''; ?>"
                                            class="form-control" required>
                                    </td>
                                </tr>
                                <input type="hidden" name="registros[<?php echo $key; ?>][fecha]"
                                    value="<?php echo $FormattedDate; ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][descripcion]"
                                    value="<?php echo htmlspecialchars($registro['descripcion']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][depositos]"
                                    value="<?php echo htmlspecialchars($registro['depositos']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][saldo]"
                                    value="<?php echo htmlspecialchars($registro['saldo']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][retiros]"
                                    value="<?php echo htmlspecialchars($registro['retiros']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][moneda]"
                                    value="<?php echo htmlspecialchars($registro['moneda']); ?>">
                                <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]"
                                    value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">


                            <?php endforeach; ?>
                        </tbody>
                        <button type="submit" class="btn btn-success w-100">Enviar registros seleccionados</button>
                </form>
            </div>

        <?php } elseif ($Banco == 'BASE') {
            ?>

            <div class="result-container">
                <h2>Registros Extraídos del PDF</h2>
                <form action="Back/Procesamiento/procesar_base.php?Banco=BASE" method="post">
                    <table class="table table-striped table-bordered text-center">
                        <thead class="table-dark">
                            <tr>
                                <th>Abono</th>
                                <th>Folio</th>
                                <th>Fecha</th>
                                <th>Operación</th>
                                <th>Destinatario</th>
                                <th>Monto</th>
                                <th>Saldo</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($registros as $key => $registro):
                                // Formateamos la fecha
                                $fechaOriginal = $registro['fecha'];
                                $fechaObj = DateTime::createFromFormat('dmY', $fechaOriginal);
                                $fechaFormateada = $fechaObj ? $fechaObj->format('Y-m-d') : $fechaOriginal;
                                ?>
                                <tr>
                                    <td>
                                        <input type="checkbox" name="registros_Abono[]" value="<?php echo $key; ?>">
                                    </td>
                                    <td><?php echo htmlspecialchars($registro['folio']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['fecha']); ?></td>
                                    <td><?php echo htmlspecialchars($registro['operacion']); ?></td>
                                    <td>
                                        <!-- Campo para agregar o modificar el cliente -->
                                        <input type="text" name="registros[<?php echo $key; ?>][destinatario]"
                                            value="<?php echo isset($registro['destinatario']) ? htmlspecialchars($registro['destinatario']) : ''; ?>"
                                            class="form-control" required>
                                    </td>
                                    <td>
                                        <input type="text" name="registros[<?php echo $key; ?>][monto]"
                                            value="<?php echo isset($registro['monto']) ? htmlspecialchars($registro['monto']) : ''; ?>"
                                            class="form-control currency-input" required oninput="formatCurrencyInput(this)">
                                    </td>
                                    <td>
                                        <input type="text" name="registros[<?php echo $key; ?>][saldo]"
                                            value="<?php echo isset($registro['saldo']) ? htmlspecialchars($registro['saldo']) : ''; ?>"
                                            class="form-control currency-input" required oninput="formatCurrencyInput(this)">
                                    </td>


                                </tr>
                                <?php if (isset($key) && isset($fechaFormateada) && isset($registro)): ?>
                                    <input type="hidden" name="registros[<?php echo $key; ?>][fecha]"
                                        value="<?php echo htmlspecialchars($fechaFormateada); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][folio]"
                                        value="<?php echo htmlspecialchars($registro['folio']); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][operacion]"
                                        value="<?php echo htmlspecialchars($registro['operacion']); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][monto]"
                                        value="<?php echo htmlspecialchars($registro['monto']); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][destinatario]"
                                        value="<?php echo htmlspecialchars($registro['destinatario']); ?>">

                                    <input type="hidden" name="registros[<?php echo $key; ?>][saldo]"
                                        value="<?php echo htmlspecialchars($registro['saldo']); ?>">
                                    <input type="hidden" name="registros[<?php echo $key; ?>][archivo_procesado]"
                                        value="<?php echo htmlspecialchars($registro['archivo_procesado']); ?>">
                                <?php else: ?>
                                    <!-- Opcionalmente, puedes agregar un mensaje de error o debug -->
                                    <?php error_log('Los valores del registro no están completos.'); ?>
                                <?php endif; ?>


                            <?php endforeach; ?>

                        </tbody>

                    </table>

                    <br>
                    <button type="submit" class="btn btn-success w-100">Enviar registros seleccionados</button>
                </form>
            </div>

        <?php } ?>


        <!-- Mostrar el texto crudo extraído del PDF -->
        <div class="result-container">
            <h2>Texto Crudo Extraído</h2>
            <pre><?php echo htmlspecialchars($textoExtraido); ?></pre>
        </div>
    </div>
    <!-- Bootstrap JS (optional, for animations and interactivity) -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha1/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        function formatCurrencyInput(input) {
            // Obtener el valor sin formato del input
            let value = input.value.replace(/[^\d]/g, ''); // Eliminar todo excepto números

            if (value) {
                // Formatear el número como moneda
                let formattedValue = new Intl.NumberFormat('en-US', {
                    style: 'currency',
                    currency: 'USD'
                }).format(value / 100); // Dividir entre 100 para los decimales

                // Actualizar el valor mostrado en el input
                input.value = formattedValue;
            }
        }

        function parseCurrencyValue(input) {
            // Retornar el valor numérico puro, sin formato de moneda
            return input.value.replace(/[^\d.-]/g, ''); // Solo números, puntos y guiones
        }

        // Al enviar el formulario, convierte todos los inputs de moneda a valores numéricos
        document.querySelector('form').addEventListener('submit', function (event) {
            let currencyInputs = document.querySelectorAll('.currency-input');

            currencyInputs.forEach(input => {
                // Cambiar el valor formateado por el valor numérico antes de enviar el formulario
                input.value = parseCurrencyValue(input);
            });
        });
    </script>

</body>

</html>