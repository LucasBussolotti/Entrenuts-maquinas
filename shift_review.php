<?php
// shift_review.php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "maquinas";

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

$tipo = $_GET['tipo'] ?? null;

// Definir el turno actual basado en la hora
date_default_timezone_set('America/Argentina/Buenos_Aires');
$current_datetime = new DateTime();
$current_time = $current_datetime->format('H:i');
$current_date = $current_datetime->format('Y-m-d');

// Determinar el turno
if ($current_time >= '04:00' && $current_time < '12:59') {
    $shift = 1;
} elseif ($current_time >= '13:00' && $current_time < '22:00') {
    $shift = 2;
} else {
    $shift = 3;
}

// Verificar si ya se realizó la revisión para este turno y tipo
$sql = "SELECT * FROM shift_reviews WHERE tipo='$tipo' AND turno=$shift AND fecha_revision='$current_date'";
$result = $conn->query($sql);
$already_reviewed = ($result->num_rows > 0);

// Manejar la confirmación de revisión
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $review_confirmed = $_POST['review_confirmed'] ?? '';
    $usuario = trim($_POST['usuario'] ?? '');
    if ($review_confirmed === 'yes' && !empty($usuario)) {
        // Insertar la revisión en la base de datos
        $hora_revision = $current_datetime->format('H:i:s');
        $usuario_safe = $conn->real_escape_string($usuario);
        $sql_insert = "INSERT INTO shift_reviews (tipo, turno, fecha_revision, hora_revision, usuario) VALUES ('$tipo', $shift, '$current_date', '$hora_revision', '$usuario_safe')";
        if ($conn->query($sql_insert) === TRUE) {
            $_SESSION['shift_reviewed_'.$tipo] = true;
            // Redirigir a la página correspondiente
            header("Location: linea_$tipo.php?tipo=$tipo");
            exit();
        } else {
            $error = "Error al guardar la revisión: " . $conn->error;
        }
    } else {
        $error = "Debes confirmar la revisión y proporcionar tu nombre.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Revisión de Turno - <?php echo ucfirst($tipo); ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f0f2f5;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .modal {
            background-color: #fff;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
            text-align: center;
            width: 350px;
        }
        h2 {
            margin-bottom: 20px;
            color: #333;
        }
        p {
            margin-bottom: 30px;
            color: #555;
        }
        button {
            background-color: #4CAF50;
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 1em;
            margin: 5px;
            transition: background-color 0.3s ease;
        }
        button:hover {
            background-color: #45a049;
        }
        .error {
            color: #dc3545;
            margin-top: 15px;
        }
    </style>
</head>
<body>
<div class="modal">
    <?php if ($already_reviewed): ?>
        <h2>Revisión de Turno</h2>
        <p>Ya has revisado los parámetros para el turno <?php echo $shift; ?> hoy en la línea de <strong><?php echo ucfirst($tipo); ?></strong>.</p>
        <button onclick="window.location.href='linea_<?php echo $tipo; ?>.php?tipo=<?php echo $tipo; ?>'">Continuar</button>
    <?php else: ?>
        <h2>Revisión de Turno</h2>
        <p>Por favor, revisa los parámetros de la línea de <strong><?php echo ucfirst($tipo); ?></strong> para el turno <?php echo $shift; ?>.</p>
        <form method="POST">
            <input type="text" name="usuario" placeholder="Tu Nombre" required style="width: 100%; padding: 10px; margin-bottom: 15px; border: 1px solid #ccc; border-radius: 5px;">
            <button type="submit" name="review_confirmed" value="yes">Confirmar Revisión</button>
        </form>
        <?php if ($error): ?>
            <p class="error"><?php echo $error; ?></p>
        <?php endif; ?>
    <?php endif; ?>
</div>
</body>
</html>
