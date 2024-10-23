<?php
// linea_pasta.php
session_start();

// Obtener el tipo desde GET
$tipo = $_GET['tipo'] ?? null;

// Obtener el turno actual (puedes modificar esto según tu lógica)
function getCurrentTurn() {
    date_default_timezone_set('America/Argentina/Buenos_Aires');
    $timestamp = date('Y-m-d H:i:s');

// Determinar el turno según la hora actual
    $hour = date('H:i');

    if ($hour >= 4 && $hour < 13) {
        return 1; // Turno 1
    } elseif ($hour >= 13 && $hour < 22) {
        return 2; // Turno 2
    } else {
        return 3; // Turno 3
    }
}

$currentTurn = getCurrentTurn();

// Reiniciar la sesión de revisión si el turno ha cambiado
if (!isset($_SESSION['last_shift']) || $_SESSION['last_shift'] !== $currentTurn) {
    $_SESSION['shift_reviewed_' . $tipo] = false; // Resetear revisión del turno
    $_SESSION['last_shift'] = $currentTurn; // Actualizar turno actual
}

// Verificar sesión de revisión
if (!isset($_SESSION['shift_reviewed_' . $tipo]) || $_SESSION['shift_reviewed_' . $tipo] !== true) {
    header("Location: shift_review.php?tipo=$tipo");
    exit();
}

$servername = "localhost";
$username = "root";  // Cambiar según tu configuración
$password = "";
$dbname = "maquinas";

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar la conexión
if ($conn->connect_error) {
    die("Error de conexión: " . $conn->connect_error);
}

// Consulta SQL para obtener la última modificación de cada parámetro
$sql = "SELECT pm.nombre_parametro, pm.valor, pm.fecha_modificacion
        FROM parametros_maquinas pm
        INNER JOIN (
            SELECT nombre_parametro, MAX(fecha_modificacion) as ultima_modificacion
            FROM parametros_maquinas
            WHERE tipo = '$tipo'
            GROUP BY nombre_parametro
        ) as ultimos ON pm.nombre_parametro = ultimos.nombre_parametro AND pm.fecha_modificacion = ultimos.ultima_modificacion
        ORDER BY pm.fecha_modificacion DESC";

$result = $conn->query($sql);

if ($result && $result->num_rows > 0) {
    // Array para almacenar los parámetros
    $parametros = [];

    // Recorrer los resultados
    while ($row = $result->fetch_assoc()) {
        // Almacenar los parámetros en el array asociativo
        if (isset($row['nombre_parametro']) && isset($row['valor'])) {
            $parametros[$row['nombre_parametro']] = [
                'ultima_modificacion' => $row['fecha_modificacion'],
                'valor' => $row['valor']
            ];
        }
    }
} else {
    echo "No se encontraron registros.";
}
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Parámetros de las Máquinas - Línea de Pasta</title>
    <style>
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f0f2f5;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }

        .container {
            background-color: #fff;
            padding: 20px 40px;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            max-width: 1000px;
            width: 100%;
        }

        h1 {
            text-align: center;
            margin-bottom: 40px;
            color: #333;
            font-size: 2em;
        }

        .success {
            color: green;
            text-align: center;
        }

        .error {
            color: red;
            text-align: center;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }

        th, td {
            padding: 15px;
            text-align: center;
            border: 1px solid #e0e0e0;
        }

        th {
            background-color: #4CAF50;
            color: white;
            font-size: 1.1em;
        }

        input[type="text"] {
            width: 100%;
            padding: 10px;
            margin: 5px 0;
            box-sizing: border-box;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 1em;
        }

        input[type="text"]:focus {
            border-color: #4CAF50;
            outline: none;
        }

        button {
            background-color: #4CAF50;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            transition: background-color 0.3s ease;
        }

        button:hover {
            background-color: #45a049;
        }

        a {
            text-decoration: none;
            color: inherit;
        }

        .btn-volver {
            display: block;
            width: 100%;
            text-align: center;
            margin-top: 20px;
        }
        .dosificadora{
            background-color: #dc3545;
        }
        .posicionador{
            background-color: #ffc107;
        }
        .etiquetadora{
            background-color: blueviolet;
        }
        .roscador{
            background-color: #28a745;
        }

        /* Responsividad */
        @media (max-width: 768px) {
            .container {
                padding: 15px 20px;
            }

            th, td {
                padding: 10px;
            }

            h1 {
                font-size: 1.5em;
            }
        }
    </style>
</head>
<body>

<div class="container">
    <h1>Parámetros de las Máquinas - Línea de Pasta</h1>

    <div id="responseMessage"></div> <!-- Mensaje de respuesta -->

    <form id="parametersForm">
        <table>
            <thead>
            <tr>
                <th colspan="2" class="dosificadora">DOSIFICADORA</th>
                <th colspan="2" class="posicionador">POSICIONADOR</th>
                <th colspan="2" class="etiquetadora">ETIQUETADORA</th>
                <th colspan="2" class="roscador">ROSCADOR</th>
            </tr>
            </thead>
            <tbody>
            <tr>
                <!-- Dosificadora -->
                <td>Tiempo columna de picos</td>
                <td><input type="text" name="dosificadora_tiempo_columna_picos"
                           value="<?= $parametros['dosificadora_tiempo_columna_picos']['valor'] ?? '01.90' ?>">
                </td>

                <!-- Posicionador -->
                <td>Tiempo sensor off activar ordenador</td>
                <td><input type="text" name="posicionador_sensor_off_activar_ordenador"
                           value="<?= $parametros['posicionador_sensor_off_activar_ordenador']['valor'] ?? '01.00' ?>"></td>

                <!-- Etiquetadora -->
                <td>Retardo codificador</td>
                <td><input type="text" name="etiquetadora_retardo_codificador"
                           value="<?= $parametros['etiquetadora_retardo_codificador']['valor'] ?? '00.08' ?>"></td>

                <!-- Roscador -->
                <td>Retardo roscador</td>
                <td><input type="text" name="roscador_retardo_roscador"
                           value="<?= $parametros['roscador_retardo_roscador']['valor'] ?? '00.40' ?>"></td>
            </tr>
            <tr>
                <!-- Dosificadora -->
                <td>Retardo de llenado</td>
                <td><input type="text" name="dosificadora_retardo_llenado"
                           value="<?= $parametros['dosificadora_retardo_llenado']['valor'] ?? '00.30' ?>"></td>

                <!-- Posicionador -->
                <td>Tiempo sensor on contar ordenador</td>
                <td><input type="text" name="posicionador_sensor_on_contar_ordenador"
                           value="<?= $parametros['posicionador_sensor_on_contar_ordenador']['valor'] ?? '01.00' ?>"></td>

                <!-- Etiquetadora -->
                <td>Tiempo codificador</td>
                <td><input type="text" name="etiquetadora_tiempo_codificador"
                           value="<?= $parametros['etiquetadora_tiempo_codificador']['valor'] ?? '00.11' ?>"></td>

                <!-- Roscador -->
                <td>Tiempo de roscador</td>
                <td><input type="text" name="roscador_tiempo_de_roscador"
                           value="<?= $parametros['roscador_tiempo_de_roscador']['valor'] ?? '00.30' ?>"></td>
            </tr>
            <tr>
                <!-- Dosificadora -->
                <td>Tiempo sensor off activar nivel</td>
                <td><input type="text" name="dosificadora_sensor_off_activar_nivel"
                           value="<?= $parametros['dosificadora_sensor_off_activar_nivel']['valor'] ?? '00.00' ?>"></td>

                <!-- Posicionador -->
                <td>Retardo cepo colocación de tapas</td>
                <td><input type="text" name="posicionador_retardo_cepo_colocacion_tapas"
                           value="<?= $parametros['posicionador_retardo_cepo_colocacion_tapas']['valor'] ?? '00.20' ?>"></td>

                <!-- Etiquetadora -->
                <td>Retardo etiquetadora</td>
                <td><input type="text" name="retardo_etiquetadora"
                           value="<?= $parametros['retardo_etiquetadora']['valor'] ?? '00.15' ?>"></td>

                <!-- Roscador -->
                <td>Retardo cepo roscador</td>
                <td><input type="text" name="retardo_cepo_roscador"
                           value="<?= $parametros['retardo_cepo_roscador']['valor'] ?? '00.20' ?>"></td>
            </tr>
            <tr>
                <!-- Dosificadora -->
                <td>Tiempo sensor on cortar nivel</td>
                <td><input type="text" name="dosificadora_sensor_on_cortar_nivel"
                           value="<?= $parametros['dosificadora_sensor_on_cortar_nivel']['valor'] ?? '00.00' ?>"></td>

                <!-- Posicionador -->
                <td>Tiempo cepo colocación de tapas</td>
                <td><input type="text" name="tiempo_cepo_colocacion_tapas"
                           value="<?= $parametros['tiempo_cepo_colocacion_tapas']['valor'] ?? '01.30' ?>"></td>

                <!-- Etiquetadora -->
                <td>Velocidad etiquetadora</td>
                <td><input type="text" name="etiquetadora_velocidad"
                           value="<?= $parametros['etiquetadora_velocidad']['valor'] ?? '26.00' ?>"></td>

                <!-- Roscador -->
                <td>Tiempo cepo roscador</td>
                <td><input type="text" name="roscador_tiempo_cepo"
                           value="<?= $parametros['roscador_tiempo_cepo']['valor'] ?? '00.60' ?>"></td>
            </tr>
            <tr>
                <!-- Dosificadora -->
                <td>Retardo tope de entrada</td>
                <td><input type="text" name="dosificadora_retardo_tope_entrada"
                           value="<?= $parametros['dosificadora_retardo_tope_entrada']['valor'] ?? '00.20' ?>"></td>

                <!-- Posicionador -->
                <td>Retardo colocación de tapas</td>
                <td><input type="text" name="posicionador_retardo_colocacion_tapas"
                           value="<?= $parametros['posicionador_retardo_colocacion_tapas']['valor'] ?? '00.25' ?>"></td>

                <!-- Etiquetadora -->
                <td>Tiempo rampa ascendente etiquetadora</td>
                <td><input type="text" name="etiquetadora_tiempo_rampa"
                           value="<?= $parametros['etiquetadora_tiempo_rampa']['valor'] ?? '01.50' ?>"></td>

                <!-- Roscador -->
                <td></td>
                <td></td>
            </tr>
            <tr>
                <!-- Dosificadora -->
                <td>Tiempo tope de entrada</td>
                <td><input type="text" name="dosificadora_tiempo_tope"
                           value="<?= $parametros['dosificadora_tiempo_tope']['valor'] ?? '01.10' ?>"></td>

                <!-- Posicionador -->
                <td>Tiempo colocación de tapas</td>
                <td><input type="text" name="posicionador_tiempo_colocacion_tapas"
                           value="<?= $parametros['posicionador_tiempo_colocacion_tapas']['valor'] ?? '01.25' ?>"></td>

                <!-- Etiquetadora -->
                <td>Tiempo rampa ascendente etiquetadora</td>
                <td><input type="text" name="etiquetadora_tiempo_rampa_ascendente"
                           value="<?= $parametros['etiquetadora_tiempo_rampa_ascendente']['valor'] ?? '01.50' ?>"></td>

                <!-- Roscador -->
                <td></td>
                <td></td>
            </tr>
            <tr>
                <!-- Dosificadora -->
                <td>Retardo tope de salida</td>
                <td><input type="text" name="dosificadora_retardo_tope_salida"
                           value="<?= $parametros['dosificadora_retardo_tope_salida']['valor'] ?? '00.20' ?>"></td>

                <!-- Posicionador -->
                <td></td>
                <td></td>

                <!-- Etiquetadora -->
                <td></td>
                <td></td>

                <!-- Roscador -->
                <td></td>
                <td></td>
            </tr>
            <tr>
                <!-- Dosificadora -->
                <td>Retardo columna de picos</td>
                <td><input type="text" name="dosificadora_retardo_columna_picos"
                           value="<?= $parametros['dosificadora_retardo_columna_picos']['valor'] ?? '01.35' ?>"></td>

                <!-- Posicionador -->
                <td></td>
                <td></td>

                <!-- Etiquetadora -->
                <td></td>
                <td></td>

                <!-- Roscador -->
                <td></td>
                <td></td>
            </tr>
            </tbody>
        </table>
        <input type="hidden" name="tipo" value="pasta"/>
        <a href="./index.php"><button type="button" style="background-color: red">Volver</button></a>
    </form>

</div>

<script>
    // Función para manejar el autoguardado
    function autoSave(inputElement) {
        const formData = new FormData();
        formData.append(inputElement.name, inputElement.value);
        formData.append('tipo', 'pasta'); // Agregar tipo siempre

        // Enviar la solicitud AJAX
        fetch('save_parameters_ajax.php', {
            method: 'POST',
            body: formData
        })
            .then(response => response.json())
            .then(data => {
                const responseMessage = document.getElementById("responseMessage");
                responseMessage.innerHTML = `<p class="${data.success ? 'success' : 'error'}">${data.message}</p>`;
            })
            .catch(error => {
                console.error('Error:', error);
            });
    }

    // Agregar eventos de cambio a todos los inputs
    document.querySelectorAll('input[type="text"]').forEach(input => {
        input.addEventListener('change', function() {
            autoSave(this);
        });
    });
</script>

</body>
</html>

