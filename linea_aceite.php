<?php
// linea_aceite.php
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



// Cerrar la conexión
$conn->close();

// Rest of your existing code for linea_aceite.php
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Parámetros de las Máquinas - Línea de Aceite</title>
    <style>
        /* Estilos mejorados */
        body {
            font-family: 'Roboto', sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 20px;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
        }
        .container {
            width: 90%;
            max-width: 1200px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
            padding: 20px;
            overflow-y: auto;
            max-height: 90vh;
        }
        h1 {
            text-align: center;
            font-size: 2em;
            color: #ff6f00;
            margin-bottom: 20px;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 30px;
        }
        th, td {
            border: 1px solid #e0e0e0;
            padding: 15px;
            text-align: center;
        }
        th {
            background-color: #fafafa;
            color: #333;
            font-weight: 600;
        }
        td input {
            width: 100%;
            padding: 10px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            font-size: 14px;
            text-align: center;
            transition: border-color 0.3s ease;
        }
        td input:focus {
            border-color: #ff6f00;
            outline: none;
        }
        button {
            display: block;
            width: 150px;
            margin: 20px auto 0;
            padding: 10px 20px;
            font-size: 16px;
            color: #fff;
            background-color: #4CAF50;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #e65c00;
        }
        .success, .error {
            text-align: center;
            margin-top: 10px;
        }
        .success {
            color: #28a745;
        }
        .error {
            color: #dc3545;
        }
        a {
            text-decoration: none;
            color: white;
        }
    </style>
</head>
<body>
<div class="container">
    <h1>Parámetros de las Máquinas - Línea de Aceite</h1>

    <div id="responseMessage"></div> <!-- Mensaje de respuesta -->

    <form id="parametersFormAceite">
        <table>
            <tr>
                <th colspan="2">DOSIFICADORA</th>
                <th colspan="2">ROSCADOR</th>
                <th colspan="2">CEPO</th>
            </tr>
            <tr>
                <td>Velocidad de llenado (Envase 360)</td>
                <td><input type="text" name="dosificadora_vel_llenado_360" value="<?php echo isset($parametros['dosificadora_vel_llenado_360']['valor']) ? $parametros['dosificadora_vel_llenado_360']['valor'] : '30.00'; ?>"></td>
                <td>Retardo roscador</td>
                <td><input type="text" name="roscador_retardo" value="<?php echo isset($parametros['roscador_retardo']['valor']) ? $parametros['roscador_retardo']['valor'] : '34'; ?>"></td>
                <td>Retardo cepo</td>
                <td><input type="text" name="cepo_retardo" value="<?php echo isset($parametros['cepo_retardo']['valor']) ? $parametros['cepo_retardo']['valor'] : '27'; ?>"></td>
            </tr>
            <tr>
                <td>Velocidad de llenado (Envase 200)</td>
                <td><input type="text" name="dosificadora_vel_llenado_200" value="<?php echo isset($parametros['dosificadora_vel_llenado_200']['valor']) ? $parametros['dosificadora_vel_llenado_200']['valor'] : '45.00'; ?>"></td>
                <td>Tiempo de roscador</td>
                <td><input type="text" name="roscador_tiempo" value="<?php echo isset($parametros['roscador_tiempo']['valor']) ? $parametros['roscador_tiempo']['valor'] : '50'; ?>"></td>
                <td>Planchado</td>
                <td><input type="text" name="cepo_planchado" value="<?php echo isset($parametros['cepo_planchado']['valor']) ? $parametros['cepo_planchado']['valor'] : '20'; ?>"></td>
            </tr>
            <tr>
                <td>Velocidad de recarga</td>
                <td><input type="text" name="dosificadora_vel_recarga" value="<?php echo isset($parametros['dosificadora_vel_recarga']['valor']) ? $parametros['dosificadora_vel_recarga']['valor'] : '40.00'; ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Tiempo de rechupado</td>
                <td><input type="text" name="dosificadora_rechupado" value="<?php echo isset($parametros['dosificadora_rechupado']['valor']) ? $parametros['dosificadora_rechupado']['valor'] : '00.00'; ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Tiempo de goteo</td>
                <td><input type="text" name="dosificadora_goteo" value="<?php echo isset($parametros['dosificadora_goteo']['valor']) ? $parametros['dosificadora_goteo']['valor'] : '00.00'; ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Retardo arranque llenadora</td>
                <td><input type="text" name="dosificadora_ret_arranque" value="<?php echo isset($parametros['dosificadora_ret_arranque']['valor']) ? $parametros['dosificadora_ret_arranque']['valor'] : '5'; ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Tiempo Dosificador</td>
                <td><input type="text" name="dosificadora_tiempo_dosif" value="<?php echo isset($parametros['dosificadora_tiempo_dosif']['valor']) ? $parametros['dosificadora_tiempo_dosif']['valor'] : '90'; ?>"></td>
                <td colspan="4"></td>
            </tr>

            <!-- Etiquetadora -->
            <tr>
                <th colspan="2">ETIQUETADORA</th>
                <th colspan="2">TRANSPORTE</th>
                <th colspan="2">IMPRENTA</th>
            </tr>
            <tr>
                <td>Velocidad</td>
                <td><input type="text" name="etiquetadora_vel" value="<?php echo isset($parametros['etiquetadora_vel']['valor']) ? $parametros['etiquetadora_vel']['valor'] : '6800'; ?>"></td>
                <td>Velocidad de cinta transportadora</td>
                <td><input type="text" name="transporte_vel_cinta" value="<?php echo isset($parametros['transporte_vel_cinta']['valor']) ? $parametros['transporte_vel_cinta']['valor'] : '99.00'; ?>"></td>
                <td>Tiempo de impresión</td>
                <td><input type="text" name="impresion_tiempo" value="<?php echo isset($parametros['impresion_tiempo']['valor']) ? $parametros['impresion_tiempo']['valor'] : '0'; ?>"></td>
            </tr>
            <tr>
                <td>Saliente</td>
                <td><input type="text" name="etiquetadora_saliente" value="<?php echo isset($parametros['etiquetadora_saliente']['valor']) ? $parametros['etiquetadora_saliente']['valor'] : '2700'; ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Ubicación frente</td>
                <td><input type="text" name="etiquetadora_ubic_frente" value="<?php echo isset($parametros['etiquetadora_ubic_frente']['valor']) ? $parametros['etiquetadora_ubic_frente']['valor'] : '0'; ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Ubicación dorso</td>
                <td><input type="text" name="etiquetadora_ubic_dorso" value="<?php echo isset($parametros['etiquetadora_ubic_dorso']['valor']) ? $parametros['etiquetadora_ubic_dorso']['valor'] : '0'; ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Rampa</td>
                <td><input type="text" name="etiquetadora_rampa" value="<?php echo isset($parametros['etiquetadora_rampa']['valor']) ? $parametros['etiquetadora_rampa']['valor'] : '30'; ?>"></td>
                <td colspan="4"></td>
            </tr>

            <!-- Dosificador -->
            <tr>
                <th colspan="2">DOSIFICADOR</th>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Retardo cierre</td>
                <td><input type="text" name="dosificador_retardo_cierre" value="<?php echo isset($parametros['dosificador_retardo_cierre']['valor']) ? $parametros['dosificador_retardo_cierre']['valor'] : '1'; ?>"></td>
                <td colspan="4"></td>
            </tr>
            <tr>
                <td>Separación</td>
                <td><input type="text" name="dosificador_separacion" value="<?php echo isset($parametros['dosificador_separacion']['valor']) ? $parametros['dosificador_separacion']['valor'] : '9'; ?>"></td>
                <td colspan="4"></td>
            </tr>
        </table>
        <input type="hidden" name="tipo" value="aceite"/>
        <div style="text-align: center;">
            <a href="./index.php">
                <button type="button" style="background-color: #dc3545; color: white; border: none; border-radius: 5px;">Volver</button>
            </a>
        </div>
    </form>
</div>

<script>
    // Función para manejar el autoguardado
    function autoSave(inputElement) {
        const formData = new FormData();
        formData.append(inputElement.name, inputElement.value);
        formData.append('tipo', 'aceite'); // Agregar tipo siempre

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
