<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Menú Principal</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            height: 100vh;
        }

        .btn-container {
            margin-top: 20px;
            margin-bottom: 20px;
        }

        .btn {
            display: inline-block;
            padding: 10px 20px;
            font-size: 16px;
            font-weight: bold;
            color: white;
            background-color: #4E3B29;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            text-decoration: none;
            transition: background-color 0.3s ease;
        }

        .btn:hover {
            background-color: #0056b3;
        }

        /* Imagen de fondo con difuminado */
        .background {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: url('DALL·E 2024-10-15 23.38.47 - A production line scene showing two distinct processing lines_ one for peanut butter and another for coconut oil. On the left side, there is machinery.webp') no-repeat center center;
            background-size: cover;
            filter: blur(5px);
            z-index: -1;
        }

        .overlay {
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            justify-content: center;
            align-items: center;
            text-align: center;
        }

        h1 {
            font-size: 2.5em;
            color: white;
            margin-bottom: 40px;
        }

        .menu-container {
            display: flex;
            justify-content: center;
            gap: 30px;
        }

        .menu-option {
            width: 280px;
            background-color: #4E3B29;
            color: white;
            padding: 20px 40px;
            text-decoration: none;
            border-radius: 10px;
            font-size: 1.5em;
            transition: background-color 0.3s ease;
        }

        .menu-option:hover {
            background-color: #F29100;
        }

        /* Modal */
        .modal {
            display: none;
            position: fixed;
            z-index: 10;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            justify-content: center;
            align-items: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 20px;
            border-radius: 10px;
            width: 300px;
            text-align: center;
        }

        .modal-content input[type="password"] {
            width: 100%;
            padding: 10px;
            margin: 10px 0;
            border-radius: 5px;
            border: 1px solid #ddd;
        }

        .modal-content button {
            padding: 10px 20px;
            background-color: #4CAF50;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-content button:hover {
            background-color: #45a049;
        }

        .error-message {
            color: red;
            margin-top: 10px;
            display: none;
        }
    </style>
</head>
<body>

<!-- Imagen de fondo -->
<div class="background"></div>

<!-- Capa de superposición -->
<div class="overlay">
    <div>
        <h1>Seleccione un tipo de historial</h1>
        <div class="menu-container">
            <a href="./historial_parametros.php" class="menu-option">Cambios de Parámetros</a>
            <a href="./historial_turnos.php" class="menu-option">Cambio de Turnos</a>
        </div>
        <div class="menu-container">
            <a href="./index.php" class="menu-option" style="margin-top: 20px;">Volver</a>
        </div>
    </div>
</div>

</body>
</html>

