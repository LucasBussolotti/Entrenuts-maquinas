<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Historial de Cambios</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        h1 {
            text-align: center;
            color: #333;
            margin-bottom: 20px;
        }

        .btn-container {
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #007bff;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin: 0 auto;
            background-color: #fff;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
            border-radius: 8px;
            overflow: hidden;
        }

        th, td {
            padding: 12px 15px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }

        th {
            background-color: #4E3B29;
            color: white;
            font-weight: bold;
        }

        td {
            color: #555;
        }

        tr:hover {
            background-color: #f1f1f1;
        }

        .section-title {
            font-size: 1.8em;
            color: #333;
            margin: 20px 0;
            text-align: center;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        /* Responsive Design */
        @media (max-width: 768px) {
            table, thead, tbody, th, td, tr {
                display: block;
            }

            th {
                display: none;
            }

            tr {
                margin-bottom: 10px;
                background-color: white;
                padding: 10px;
                box-shadow: 0 0 5px rgba(0, 0, 0, 0.1);
            }

            td {
                display: flex;
                justify-content: space-between;
                padding: 10px;
                border: none;
                border-bottom: 1px solid #ddd;
            }

            td:before {
                content: attr(data-label);
                font-weight: bold;
                color: #333;
            }
        }
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f9;
            margin: 0;
            padding: 20px;
        }

        .form-container {
            text-align: center;
            margin-bottom: 20px;
            background-color: #fff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        label {
            margin-right: 10px;
            font-weight: bold;
        }

        input[type="text"],
        input[type="date"],
        select {
            padding: 10px;
            margin: 10px 5px;
            border: 1px solid #ccc;
            border-radius: 5px;
            width: 200px;
            transition: border-color 0.3s;
        }

        input[type="text"]:focus,
        input[type="date"]:focus,
        select:focus {
            border-color: #007bff;
            outline: none;
        }

        .btn {
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #F29100;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #FCCB03;
        }
    </style>
</head>
<body>

<h1 class="section-title">Historial de Cambios de Parámetros</h1>

<div class="btn-container">
    <a href="./historial_elegir.php" class="btn">Volver</a>
</div>

<!-- Formulario de Filtros -->
<form method="GET" action="">
    <div style="text-align: center; margin-bottom: 20px;">
        <label for="nombre_parametro">Parámetro:</label>
        <input type="text" name="nombre_parametro" value="<?php echo isset($_GET['nombre_parametro']) ? htmlspecialchars($_GET['nombre_parametro']) : ''; ?>">

        <label for="tipo">Tipo:</label>
        <select name="tipo">
            <option value="">Todos</option>
            <option value="aceite" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'aceite') ? 'selected' : ''; ?>>Aceite</option>
            <option value="pasta" <?php echo (isset($_GET['tipo']) && $_GET['tipo'] === 'pasta') ? 'selected' : ''; ?>>Pasta</option>
            <!-- Agrega más opciones según sea necesario -->
        </select>

        <label for="fecha_modificacion">Fecha:</label>
        <input type="date" name="fecha_modificacion" value="<?php echo isset($_GET['fecha_modificacion']) ? htmlspecialchars($_GET['fecha_modificacion']) : ''; ?>">

        <button type="submit" class="btn">Filtrar</button>
        <button type="button" class="btn btn-clear" onclick="window.location.href='?'">Limpiar</button>
    </div>
</form>

<?php
// Conectar a la base de datos
$servername = "localhost";
$username = "root"; // Cambia esto si es necesario
$password = ""; // Cambia esto si es necesario
$dbname = "maquinas"; // Nombre de la base de datos

// Crear la conexión
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Inicializar la consulta
$sql = "SELECT nombre_parametro, valor, fecha_modificacion, turno, tipo FROM parametros_maquinas WHERE 1=1";

// Filtrar por nombre de parámetro
if (isset($_GET['nombre_parametro']) && $_GET['nombre_parametro'] !== '') {
    $nombre_parametro = $conn->real_escape_string($_GET['nombre_parametro']);
    $sql .= " AND nombre_parametro LIKE '%$nombre_parametro%'";
}

// Filtrar por tipo
if (isset($_GET['tipo']) && $_GET['tipo'] !== '') {
    $tipo = $conn->real_escape_string($_GET['tipo']);
    $sql .= " AND tipo = '$tipo'";
}

// Filtrar por fecha de modificación
if (isset($_GET['fecha_modificacion']) && $_GET['fecha_modificacion'] !== '') {
    $fecha_modificacion = $conn->real_escape_string($_GET['fecha_modificacion']);
    $sql .= " AND DATE(fecha_modificacion) = '$fecha_modificacion'";
}

$sql .= " ORDER BY fecha_modificacion DESC"; // Ordenar los resultados

$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>Parámetro</th><th>Valor</th><th>Fecha de Modificación</th><th>Turno</th><th>Tipo</th></tr>";
    while($row = $result->fetch_assoc()) {
        echo "<tr><td data-label='Parámetro'>" . htmlspecialchars($row["nombre_parametro"]) . "</td>";
        echo "<td data-label='Valor'>" . htmlspecialchars($row["valor"]) . "</td>";
        echo "<td data-label='Fecha de Modificación'>" . htmlspecialchars($row["fecha_modificacion"]) . "</td>";
        echo "<td data-label='Turno'>" . htmlspecialchars($row["turno"]) . "</td>";
        echo "<td data-label='Tipo'>" . htmlspecialchars($row["tipo"]) . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "<p style='text-align:center;'>No hay cambios registrados.</p>";
}

$conn->close();
?>

</body>
</html>
