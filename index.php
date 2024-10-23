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
            background-color: #F29100;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        .modal-content button:hover {
            background-color: #F29100;
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
        <h1>Seleccione la Línea de Producción</h1>
        <div class="menu-container">
            <a href="./linea_pasta.php?tipo=pasta" class="menu-option">Línea de Pasta</a>
            <a href="./linea_aceite.php?tipo=aceite" class="menu-option">Línea de Aceite</a>
        </div>
        <div class="menu-container" style="margin-top: 20px">
            <a href="#" class="menu-option" id="openModal">Ver Historial</a>
        </div>
    </div>
</div>

<!-- Modal para contraseña -->
<div id="passwordModal" class="modal">
    <div class="modal-content">
        <h2>Solo personal autorizado</h2>
        <input type="password" id="password" placeholder="Contraseña">
        <button id="checkPassword">Entrar</button>
        <p class="error-message" id="errorMessage">Contraseña incorrecta, inténtalo de nuevo.</p>
    </div>
</div>

<script>
    // Mostrar modal
    document.getElementById('openModal').addEventListener('click', function(event) {
        event.preventDefault();
        document.getElementById('passwordModal').style.display = 'flex';
    });

    // Verificar contraseña
    document.getElementById('checkPassword').addEventListener('click', function() {
        const password = document.getElementById('password').value;
        const correctPassword = 'adminentrenuts'; // Define aquí la contraseña correcta

        if (password === correctPassword) {
            window.location.href = 'historial_elegir.php'; // Redirige a la página de historial
        } else {
            document.getElementById('errorMessage').style.display = 'block'; // Muestra el mensaje de error
        }
    });

    // Cerrar modal si se hace clic fuera de él
    window.addEventListener('click', function(event) {
        const modal = document.getElementById('passwordModal');
        if (event.target === modal) {
            modal.style.display = 'none';
        }
    });
</script>

</body>
</html>
