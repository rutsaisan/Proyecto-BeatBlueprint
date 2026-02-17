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
        video { border-radius: 0.75rem; background: black; width: 100%; max-height: 300px; }
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
                    <div class="glass-card p-4 flex flex-col gap-4 animate-fade-in relative group">
                        <button onclick="eliminarRecurso(<?php echo $vid['id_video']; ?>, 'video')" 
                                class="absolute top-6 right-6 w-8 h-8 rounded-full bg-red-500/20 text-red-500 opacity-0 group-hover:opacity-100 transition-opacity hover:bg-red-500 hover:text-white flex items-center justify-center z-10">
                            <i class="fas fa-trash-alt"></i>
                        </button>

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
        // Función para subir videos
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
                const data = await response.json();
                if (data.success) { location.reload(); }
                else { alert("Error: " + data.error); }
            } catch (err) { alert("Error al subir el video."); }
        });

        // Función para eliminar (Universal)
        async function eliminarRecurso(id, tipo) {
            if (!confirm(`¿Borrar este ${tipo} permanentemente?`)) return;

            try {
                const response = await fetch('php/eliminar_recurso.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&tipo=${tipo}`
                });
                const data = await response.json();
                if (data.success) { location.reload(); }
                else { alert("Error al borrar: " + data.error); }
            } catch (err) { alert("Error de conexión."); }
        }
        
    </script>
</body>
</html>