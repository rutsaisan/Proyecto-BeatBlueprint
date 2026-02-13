<?php
// 1. INICIAR SESIÓN
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 2. VERIFICAR LOGIN (Seguridad)
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

include "includes/config.php";

// 3. OBTENER ID DEL USUARIO ACTUAL
// Ya no hay redirección. Seas quien seas (ID 1, 2 o 50), verás tus libros.
if (isset($_SESSION['id'])) {
    $usuario_id = $_SESSION['id'];
} else {
    // Fallback por seguridad si la sesión 'id' no está definida
    $usuario_id = 1; 
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beat Blueprint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <style>
        /* --- ESTILOS GENERALES --- */
        :root {
            --primary-purple: #8A4FFF;
            --dark-bg: #1a1a2e;
            --card-bg: rgba(255, 255, 255, 0.08);
            --glass-border: rgba(255, 255, 255, 0.1);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 50% -20%, #4a2b69 0%, #0f0f13 60%);
            background-attachment: fixed;
            color: white;
            height: 100vh;
            overflow: hidden;
            margin: 0;
        }

        /* Utilidades */
        .glass-input {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border: 1px solid var(--glass-border);
            color: white;
            border-radius: 50px;
            padding: 12px 20px;
            width: 100%;
            outline: none;
            transition: all 0.3s ease;
        }
        .glass-card {
            background: rgba(30, 30, 40, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
        }

        /* Navegación Pantallas */
        .screen {
            display: none;
            height: 100vh;
            overflow-y: auto;
            padding: 20px;
            animation: fadeIn 0.4s ease-out;
        }
        .screen.active {
            display: flex;
            flex-direction: column;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(10px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* Canvas Stage */
        #stage-area {
            background-image: linear-gradient(#333 1px, transparent 1px), linear-gradient(90deg, #333 1px, transparent 1px);
            background-size: 40px 40px;
            background-color: #1a1a20;
            border: 2px solid #444;
            position: relative;
            overflow: hidden;
            border-radius: 10px;
            box-shadow: inset 0 0 50px rgba(0,0,0,0.8);
        }
        .dancer-token {
            width: 40px;
            height: 40px;
            background: var(--primary-purple);
            border: 2px solid white;
            border-radius: 50%;
            position: absolute;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            cursor: grab;
            box-shadow: 0 4px 10px rgba(0,0,0,0.5);
            user-select: none;
            touch-action: none;
        }
        .dancer-token:active {
            cursor: grabbing;
            transform: scale(1.1);
        }
    </style>
</head>
<body>

    <div id="screen-dashboard" class="screen active">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold italic">Beat Blueprint</h2>
            <div class="w-10 h-10 rounded-full bg-purple-600 flex items-center justify-center border-2 border-white">
                <i class="fas fa-user"></i>
            </div>
        </div>

        <div class="glass-card p-4 mb-8 flex-grow">
            <h3 class="text-gray-300 text-sm mb-3 border-b border-gray-600 pb-2">Tus últimas actividades</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-2 hover:bg-white/5 rounded-lg transition">
                    <div class="bg-blue-500/20 p-2 rounded text-blue-400"><i class="fas fa-music"></i></div>
                    <div>
                        <p class="font-semibold text-sm">Hip Hop Mix 2025</p>
                        <p class="text-xs text-gray-400">Añadido ayer</p>
                    </div>
                </div>
                <div class="flex items-center gap-3 p-2 hover:bg-white/5 rounded-lg transition">
                    <div class="bg-green-500/20 p-2 rounded text-green-400"><i class="fas fa-video"></i></div>
                    <div>
                        <p class="font-semibold text-sm">Ensayo Jueves</p>
                        <p class="text-xs text-gray-400">Video subido</p>
                    </div>
                </div>
                <div class="bg-purple-900/30 p-3 rounded-lg text-center mt-4">
                    <p class="text-sm text-purple-200">Próxima clase: Martes 18:00</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-auto mb-6">
    <a href="musica.php" class="glass-card p-6 flex flex-col items-center justify-center gap-3 active:scale-95 transition cursor-pointer hover:bg-white/10 border-t-4 border-purple-500">
        <i class="fas fa-music text-3xl text-purple-400"></i>
        <span class="font-medium text-sm">Música</span>
    </a>
    
    <a href="posiciones.php" class="glass-card p-6 flex flex-col items-center justify-center gap-3 active:scale-95 transition cursor-pointer hover:bg-white/10 border-t-4 border-blue-500">
        <i class="fas fa-map-marker-alt text-3xl text-blue-400"></i>
        <span class="font-medium text-sm">Posiciones</span>
    </a>

    <a href="vidioteca.php" class="glass-card p-6 flex flex-col items-center justify-center gap-3 active:scale-95 transition cursor-pointer hover:bg-white/10 border-t-4 border-pink-500">
        <i class="fas fa-play-circle text-3xl text-pink-400"></i>
        <span class="font-medium text-sm">Vidioteca</span>
    </a>

    <a href="wiki.php" class="glass-card p-6 flex flex-col items-center justify-center gap-3 active:scale-95 transition cursor-pointer hover:bg-white/10 border-t-4 border-yellow-500">
        <i class="fas fa-book text-3xl text-yellow-400"></i>
        <span class="font-medium text-sm">Wiki Pasos</span>
    </a>
</div>
        
        <div class="text-center pb-4">
             <a href="index.php" class="text-gray-400 text-xs hover:text-white">Cerrar Sesión</a>
        </div>
    </div>

    <div id="screen-music" class="screen">
        <button onclick="navigate('screen-dashboard')" class="mb-4 text-gray-400 hover:text-white"><i class="fas fa-arrow-left"></i> Volver</button>
        <h2 class="text-3xl font-bold mb-6">Tu Música</h2>
        <div class="glass-card p-6 mb-6 text-center">
            <div class="w-32 h-32 bg-gray-700 rounded-lg mx-auto mb-4 flex items-center justify-center shadow-lg">
                <i class="fas fa-music text-4xl text-gray-500"></i>
            </div>
            <h3 class="text-xl font-bold">Urbano Mix Vol. 1</h3>
            <p class="text-gray-400 text-sm mb-4">Importado localmente</p>
            <div class="w-full bg-gray-700 h-1 rounded-full mb-4">
                <div class="bg-purple-500 h-1 rounded-full w-1/3"></div>
            </div>
            <div class="flex justify-center gap-6 text-2xl items-center">
                <i class="fas fa-backward text-gray-400"></i>
                <div class="w-12 h-12 bg-white rounded-full flex items-center justify-center text-purple-900 cursor-pointer hover:scale-105 transition"><i class="fas fa-play pl-1"></i></div>
                <i class="fas fa-forward text-gray-400"></i>
            </div>
        </div>
        <h3 class="font-bold mb-3">Guardadas</h3>
        <div class="space-y-3">
            <div class="glass-card p-3 flex justify-between items-center">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-gray-700 rounded flex items-center justify-center text-xs">MP3</div>
                    <div>
                        <p class="font-bold text-sm">Coreografía Final</p>
                        <p class="text-xs text-gray-400">3:45</p>
                    </div>
                </div>

<?php
session_start();

// Si no existe la variable de sesión 'loggedin', redirigir al login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
?>
</body>
