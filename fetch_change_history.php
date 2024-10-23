<?php

$servername = "localhost";
$username = "root"; // Este es el usuario por defecto para localhost en MySQL
$password = ""; // En la mayoría de las configuraciones locales, la contraseña está vacía
$dbname = "maquinas"; // El nombre de la base de datos que creaste

// Crear conexión a la base de datos
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexión
if ($conn->connect_error) {
    die("Conexión fallida: " . $conn->connect_error);
}

// Consultar historial de cambios
$sql = "SELECT nombre_parametro, valor, fecha_modificacion FROM parametros_maquinas ORDER BY fecha_modificacion DESC";
$result = $conn->query($sql);

if ($result->num_rows > 0) {
    echo "<table><tr><th>Parámetro</th><th>Valor</th><th>Fecha de Modificación</th></tr>";
    while ($row = $result->fetch_assoc()) {
        echo "<tr><td>" . $row["nombre_parametro"] . "</td><td>" . $row["valor"] . "</td><td>" . $row["fecha_modificacion"] . "</td></tr>";
    }
    echo "</table>";
} else {
    echo "No hay cambios registrados.";
}

$conn->close();

