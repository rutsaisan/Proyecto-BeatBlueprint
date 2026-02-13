<?php
session_start();

// 1. Seguridad: Si no hay sesión, al login
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { 
    header("Location: index.php"); 
    exit; 
}

// 2. Incluimos la configuración de la base de datos
require_once "includes/config.php";

$usuario_id = $_SESSION['id'];

// 3. OBTENER ESTILOS DE BAILE (Para el menú desplegable)
$estilos = [];
$query_estilos = "SELECT id_estilo_baile, nombre_estilo_baile FROM Estilo_baile ORDER BY nombre_estilo_baile ASC";
$res_estilos = mysqli_query($conexion, $query_estilos);

if ($res_estilos) {
    while ($fila = mysqli_fetch_assoc($res_estilos)) {
        $estilos[] = $fila;
    }
}

// 4. OBTENER CANCIONES CON JOIN (Para traer el nombre del estilo y no solo el ID)
$query_canciones = "SELECT c.*, e.nombre_estilo_baile 
                    FROM Canciones c
                    INNER JOIN Estilo_baile e ON c.id_estilo_baile = e.id_estilo_baile
                    WHERE c.id_usuario = '$usuario_id' 
                    ORDER BY c.id_cancion DESC";

$resultado_canciones = mysqli_query($conexion, $query_canciones);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beat Blueprint - Música</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <style>
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
            min-height: 100vh;
            margin: 0;
            display: flex;
            flex-direction: column;
        }

        .glass-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(12px);
            -webkit-backdrop-filter: blur(12px);
        }
        
        select option {
            background-color: #1a1a2e;
            color: white;
        }

        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: transparent; }
        ::-webkit-scrollbar-thumb {
            background: var(--glass-border);
            border-radius: 10px;
        }

        /* Reproductor nativo con estética Beat Blueprint */
        audio {
            filter: invert(100%) hue-rotate(275deg) brightness(1.5);
        }
    </style>
</head>
<body class="p-6 md:p-10 max-w-2xl mx-auto w-full">

    <header class="flex items-center gap-4 mb-8">
        <a href="feed.php" class="w-10 h-10 rounded-full glass-card flex items-center justify-center hover:bg-white/10 transition active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <h1 class="text-2xl font-bold tracking-wide">Música</h1>
    </header>

    <div class="glass-card rounded-2xl p-6 mb-8 transition hover:bg-white/5 flex flex-col gap-4">
        
        <div class="flex flex-col gap-2">
            <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold ml-1">Estilo de la canción</label>
            <select id="estilo-select" class="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-sm focus:outline-none focus:border-purple-500 transition cursor-pointer">
                <?php if (empty($estilos)): ?>
                    <option value="">No hay estilos en la base de datos</option>
                <?php else: ?>
                    <?php foreach ($estilos as $estilo): ?>
                        <option value="<?php echo $estilo['id_estilo_baile']; ?>">
                            <?php echo htmlspecialchars($estilo['nombre_estilo_baile']); ?>
                        </option>
                    <?php endforeach; ?>
                <?php endif; ?>
            </select>
        </div>

        <label for="audio-upload" class="cursor-pointer border-2 border-dashed border-white/10 rounded-xl p-8 flex flex-col items-center justify-center gap-3 hover:border-purple-500/50 hover:bg-purple-500/5 transition group">
            <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 group-hover:scale-110 transition">
                <i class="fas fa-cloud-upload-alt text-xl"></i>
            </div>
            <span class="font-medium text-sm">Sube tu archivo de audio</span>
        </label>
        <input type="file" id="audio-upload" accept="audio/*" class="hidden">
    </div>

    <div class="flex flex-col flex-grow overflow-hidden">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4 px-2">Tus Beats</h2>
        
        <div id="playlist" class="flex flex-col gap-3 overflow-y-auto pb-6 px-2">
            <?php if ($resultado_canciones && mysqli_num_rows($resultado_canciones) > 0): ?>
                <?php while ($cancion = mysqli_fetch_assoc($resultado_canciones)): ?>
                    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 animate-fade-in">
                        <div class="flex items-center gap-3 px-1">
                            <div class="w-8 h-8 rounded-full bg-white/5 flex items-center justify-center text-purple-400 flex-shrink-0">
                                <i class="fas fa-music text-xs"></i>
                            </div>
                            <div class="flex flex-col overflow-hidden w-full">
                                <span class="font-medium text-sm truncate"><?php echo htmlspecialchars($cancion['nombre_cancion']); ?></span>
                                <div class="flex gap-2">
                                    <span class="text-xs text-gray-400 truncate">Artista: <?php echo htmlspecialchars($cancion['artista']); ?></span>
                                    <span class="text-xs text-purple-400 font-bold uppercase truncate">| <?php echo htmlspecialchars($cancion['nombre_estilo_baile']); ?></span>
                                </div>
                            </div>
                        </div>
                        <audio src="<?php echo htmlspecialchars($cancion['ruta_mp3']); ?>" controls class="w-full h-10 outline-none rounded-lg"></audio>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div id="empty-state" class="text-center py-10 text-gray-500 text-sm">
                    Aún no has subido ninguna canción.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const audioUpload = document.getElementById('audio-upload');
        const playlist = document.getElementById('playlist');
        const estiloSelect = document.getElementById('estilo-select');

        audioUpload.addEventListener('change', async function(event) {
            const file = event.target.files[0];
            if (!file) return;

            const emptyState = document.getElementById('empty-state');
            if (emptyState) emptyState.style.display = 'none';

            const formData = new FormData();
            formData.append('audio', file);
            formData.append('artista', 'Desconocido'); 
            formData.append('id_estilo_baile', estiloSelect.value);     

            try {
                // Asegúrate de que este archivo existe en tu carpeta /php/
                const response = await fetch('php/subir_cancion.php', {
                    method: 'POST',
                    body: formData
                });
                
                const rawText = await response.text();
                
                try {
                    const data = JSON.parse(rawText);
                    if (data.success) {
                        location.reload(); // Recargamos para que el JOIN de PHP traiga el nombre del estilo correctamente
                    } else {
                        alert("Error: " + data.error);
                    }
                } catch (e) {
                    console.error("Respuesta no válida del servidor:", rawText);
                    alert("Error crítico del servidor. Revisa la consola.");
                }
            } catch (error) {
                console.error("Error de red:", error);
                alert("Error al conectar con el servidor.");
            }
            audioUpload.value = '';
        });
    </script>
</body>
</html>