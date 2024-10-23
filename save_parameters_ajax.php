<?php

// save_parameters_ajax.php
header('Content-Type: application/json');

// Configuración de la base de datos
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maquinas";

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Error de conexión a la base de datos']);
    exit();
}

// Recibir tipo
$tipo = $_POST['tipo'] ?? null;
if (!$tipo) {
    echo json_encode(['success' => false, 'message' => 'El tipo no fue proporcionado']);
    exit();
}

// Recibir y procesar todos los parámetros
$parametros = [
    'dosificadora_tiempo_columna_picos',
    'posicionador_sensor_off_activar_ordenador',
    'etiquetadora_retardo_codificador',
    'roscador_retardo_roscador',
    'dosificadora_retardo_llenado',
    'posicionador_sensor_on_contar_ordenador',
    'etiquetadora_tiempo_codificador',
    'tiempo_de_roscador',
    'dosificadora_sensor_off_activar_nivel',
    'posicionador_retardo_cepo_colocacion_tapas',
    'retardo_etiquetadora',
    'retardo_cepo_roscador',
    'dosificadora_sensor_on_cortar_nivel',
    'tiempo_cepo_colocacion_tapas',
    'etiquetadora_velocidad',
    'roscador_tiempo_cepo',
    'dosificadora_retardo_tope_entrada',
    'posicionador_retardo_colocacion_tapas',
    'etiquetadora_tiempo_rampa',
    'dosificadora_tiempo_tope',
    'posicionador_tiempo_colocacion_tapas',
    'etiquetadora_tiempo_rampa_ascendente',
    'dosificadora_retardo_tope_salida',
    'dosificadora_retardo_columna_picos',
    'dosificadora_vel_llenado_360',
    'roscador_retardo',
    'cepo_retardo',
    'dosificadora_vel_llenado_200',
    'roscador_tiempo',
    'cepo_planchado',
    'dosificadora_vel_recarga',
    'dosificadora_rechupado',
    'dosificadora_goteo',
    'dosificadora_ret_arranque',
    'dosificadora_tiempo_dosif',
    'etiquetadora_vel',
    'transporte_vel_cinta',
    'impresion_tiempo',
    'etiquetadora_saliente',
    'etiquetadora_ubic_frente',
    'etiquetadora_ubic_dorso',
    'etiquetadora_rampa',
    'dosificador_retardo_cierre',
    'dosificador_separacion'
];

date_default_timezone_set('America/Argentina/Buenos_Aires');
$timestamp = date('Y-m-d H:i:s');

// Determinar el turno según la hora actual
$hora_actual = date('H:i');
$turno = ($hora_actual >= '04:00' && $hora_actual < '13:00') ? 1 : (($hora_actual >= '13:00' && $hora_actual < '22:00') ? 2 : 3);

$values = [];

foreach ($parametros as $parametro) {
    if (isset($_POST[$parametro])) {
        $valor = $conn->real_escape_string($_POST[$parametro]);
        $values[] = "('$parametro', '$valor', $turno, '$tipo', '$timestamp')";
    }
}

if (!empty($values)) {
    $sql = "INSERT INTO parametros_maquinas (nombre_parametro, valor, turno, tipo, fecha_modificacion) VALUES " . implode(', ', $values);
    if ($conn->query($sql) === TRUE) {
    } else {
        echo json_encode(['success' => false, 'message' => 'Error al guardar los cambios: ' . $conn->error]);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'No se recibieron parámetros válidos.']);
}

$conn->close();
?>
