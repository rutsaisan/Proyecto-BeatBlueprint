<?php
session_start();
// 1. Verificar seguridad
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}
require_once "includes/config.php";

$usuario_id = $_SESSION['id'];

// 2. Obtener estilos para el selector
$res_estilos = mysqli_query($conexion, "SELECT * FROM Estilo_baile");
$estilos = mysqli_fetch_all($res_estilos, MYSQLI_ASSOC);

// 3. Obtener videos guardados por el usuario
$query_videos = "SELECT * FROM Videos WHERE id_usuario = '$usuario_id' ORDER BY id_video DESC";
$resultado_videos = mysqli_query($conexion, $query_videos);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <title>Beat Blueprint - Vidioteca</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: radial-gradient(circle at 50% -20%, #4a2b69 0%, #0f0f13 60%); 
            color: white; min-height: 100vh; font-family: 'Inter', sans-serif; 
        }
        .glass-card { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(12px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
            border-radius: 1rem; 
        }
        video { 
            border-radius: 0.75rem; 
            background: black; 
            width: 100%; 
            max-height: 300px;
        }
        /* Estilo para el contenedor del select */
        select {
            appearance: none; /* Quita el diseño por defecto del navegador */
            -webkit-appearance: none;
            -moz-appearance: none;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 24 24' stroke='white'%3E%3Cpath stroke-linecap='round' stroke-linejoin='round' stroke-width='2' d='M19 9l-7 7-7-7'%3E%3C/path%3E%3C/svg%3E");
            background-repeat: no-repeat;
            background-position: right 1rem center;
            background-size: 1rem;
            padding-right: 2.5rem !important; /* Espacio para la flecha personalizada */
        }

        /* Estilo para las opciones (el desplegable abierto) */
        select option {
            background-color: #1a1a2e; /* Color sólido para que sea legible */
            color: white;
            padding: 10px;
        }

        /* Evitar el color azul feo al seleccionar en algunos navegadores */
        select:focus {
            outline: none;
            border-color: var(--primary-purple);
            box-shadow: 0 0 0 2px rgba(138, 79, 255, 0.2);
        }
    </style>
</head>
<body class="p-6 md:p-10">

    <div class="max-w-5xl mx-auto">
        <header class="flex justify-between items-center mb-10">
            <h1 class="text-3xl font-bold text-purple-400 italic tracking-tighter uppercase">Vidioteca</h1>
            <a href="feed.php" class="text-gray-400 hover:text-white transition"><i class="fas fa-arrow-left"></i> Volver</a>
        </header>

        <div class="glass-card p-6 mb-10 grid md:grid-cols-2 gap-6 items-center">
            <div>
                <label class="text-xs text-gray-400 uppercase tracking-wider font-semibold ml-1">Estilo del video</label>
                <select id="estilo-select" class="w-full bg-white/10 border border-white/20 rounded-lg p-3 text-sm text-white focus:outline-none transition cursor-pointer glass-card">
                    <?php foreach ($estilos as $estilo): ?>
                        <option value="<?php echo $estilo['id_estilo_baile']; ?>" class="bg-[#1a1a2e]">
                            <?php echo htmlspecialchars($estilo['nombre_estilo_baile']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            
            <label for="video-upload" class="cursor-pointer border-2 border-dashed border-purple-500/30 rounded-xl p-6 flex flex-col items-center justify-center gap-2 hover:bg-purple-500/5 transition group">
                <i class="fas fa-video text-2xl text-purple-400 group-hover:scale-110 transition"></i>
                <span class="font-medium text-sm">Añadir grabación</span>
                <input type="file" id="video-upload" accept="video/*" class="hidden">
            </label>
        </div>

        <div id="video-grid" class="grid grid-cols-1 md:grid-cols-2 gap-8">
            <?php if (mysqli_num_rows($resultado_videos) > 0): ?>
                <?php while ($vid = mysqli_fetch_assoc($resultado_videos)): ?>
                    <div class="glass-card p-4 flex flex-col gap-4 animate-fade-in">
                        <video src="<?php echo htmlspecialchars($vid['video']); ?>" controls></video>
                        <div class="px-1">
                            <p class="font-semibold text-sm truncate"><?php echo htmlspecialchars($vid['descripcion']); ?></p>
                            <span class="text-xs text-purple-400 font-bold uppercase tracking-widest">Ensayo Guardado</span>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-span-full text-center py-20 text-gray-500">
                    <i class="fas fa-film text-4xl mb-4 block opacity-20"></i>
                    Aún no hay videos en tu vidioteca.
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        const videoUpload = document.getElementById('video-upload');
        const estiloSelect = document.getElementById('estilo-select');

        videoUpload.addEventListener('change', async function() {
    const file = this.files[0];
    if (!file) return;

    const formData = new FormData();
    formData.append('video', file);
    formData.append('id_estilo_baile', estiloSelect.value);

    try {
        const response = await fetch('php/subir_video.php', {
            method: 'POST',
            body: formData
        });

        const rawText = await response.text(); // Capturamos la respuesta como texto primero
        console.log("Respuesta del servidor:", rawText);

        const data = JSON.parse(rawText);
        if (data.success) {
            location.reload(); 
        } else {
            alert("Error: " + data.error);
        }
    } catch (err) {
        console.error("Fallo al procesar JSON:", err);
        alert("El video es demasiado pesado o el servidor dio error. Revisa la consola.");
    }
});
    </script>
</body>
</html>