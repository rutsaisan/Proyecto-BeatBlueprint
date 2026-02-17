<?php
session_start();
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) { 
    header("Location: index.php"); 
    exit; 
}
require_once "includes/config.php";

$usuario_id = $_SESSION['id'];

// Obtener estilos
$query_estilos = "SELECT id_estilo_baile, nombre_estilo_baile FROM Estilo_baile ORDER BY nombre_estilo_baile ASC";
$res_estilos = mysqli_query($conexion, $query_estilos);
$estilos = mysqli_fetch_all($res_estilos, MYSQLI_ASSOC);

// Obtener canciones con JOIN filtradas por usuario
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
    <title>Beat Blueprint - Música</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: radial-gradient(circle at 50% -20%, #4a2b69 0%, #0f0f13 60%); 
            color: white; min-height: 100vh; display: flex; flex-direction: column;
        }
        .glass-card { background: rgba(255, 255, 255, 0.08); backdrop-filter: blur(12px); border: 1px solid rgba(255, 255, 255, 0.1); border-radius: 1rem; }
        audio { filter: invert(100%) hue-rotate(275deg) brightness(1.5); }
    </style>
</head>
<body class="p-6 md:p-10 max-w-2xl mx-auto w-full">

    <header class="flex items-center gap-4 mb-8">
        <a href="feed.php" class="w-10 h-10 rounded-full glass-card flex items-center justify-center hover:bg-white/10 transition active:scale-95">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
        <h1 class="text-2xl font-bold tracking-wide tracking-tighter uppercase italic text-purple-400">Música</h1>
    </header>

    <div class="glass-card p-6 mb-8 flex flex-col gap-4">
        <select id="estilo-select" class="w-full bg-white/5 border border-white/10 rounded-lg p-3 text-sm text-white focus:outline-none">
            <?php foreach ($estilos as $estilo): ?>
                <option value="<?php echo $estilo['id_estilo_baile']; ?>" class="bg-[#1a1a2e]">
                    <?php echo htmlspecialchars($estilo['nombre_estilo_baile']); ?>
                </option>
            <?php endforeach; ?>
        </select>

        <label for="audio-upload" class="cursor-pointer border-2 border-dashed border-white/10 rounded-xl p-8 flex flex-col items-center justify-center gap-3 hover:border-purple-500/50 transition group">
            <div class="w-12 h-12 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400 group-hover:scale-110 transition">
                <i class="fas fa-cloud-upload-alt text-xl"></i>
            </div>
            <span class="font-medium text-sm">Sube tu archivo de audio</span>
            <input type="file" id="audio-upload" accept="audio/*" class="hidden">
        </label>
    </div>

    <div class="flex flex-col flex-grow overflow-hidden">
        <h2 class="text-sm font-semibold text-gray-400 uppercase tracking-wider mb-4 px-2">Tus Beats</h2>
        <div id="playlist" class="flex flex-col gap-3 overflow-y-auto pb-6 px-2">
            <?php if (mysqli_num_rows($resultado_canciones) > 0): ?>
                <?php while ($cancion = mysqli_fetch_assoc($resultado_canciones)): ?>
                    <div class="glass-card p-4 rounded-xl flex flex-col gap-3 relative group">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-purple-500/10 flex items-center justify-center text-purple-400">
                                    <i class="fas fa-music text-xs"></i>
                                </div>
                                <div class="flex flex-col">
                                    <span class="font-medium text-sm"><?php echo htmlspecialchars($cancion['nombre_cancion']); ?></span>
                                    <span class="text-[10px] text-purple-400 font-bold uppercase tracking-widest"><?php echo htmlspecialchars($cancion['nombre_estilo_baile']); ?></span>
                                </div>
                            </div>
                            <button onclick="eliminarRecurso(<?php echo $cancion['id_cancion']; ?>, 'musica')" 
                                    class="text-gray-500 hover:text-red-500 transition-colors">
                                <i class="fas fa-trash-alt text-xs"></i>
                            </button>
                        </div>
                        <audio src="<?php echo htmlspecialchars($cancion['ruta_mp3']); ?>" controls class="w-full h-10"></audio>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="text-center py-10 text-gray-500 text-sm italic">Aún no has subido ninguna canción.</div>
            <?php endif; ?>
        </div>
    </div>

    <script>
        // Función subir audio
        const audioUpload = document.getElementById('audio-upload');
        audioUpload.addEventListener('change', async function() {
            const file = this.files[0];
            if (!file) return;

            const formData = new FormData();
            formData.append('audio', file);
            formData.append('id_estilo_baile', document.getElementById('estilo-select').value);

            try {
                const response = await fetch('php/subir_cancion.php', { method: 'POST', body: formData });
                const data = await response.json();
                if (data.success) { location.reload(); }
                else { alert("Error: " + data.error); }
            } catch (err) { alert("Error al subir archivo."); }
        });

        // Función eliminar (Igual que en videoteca)
        async function eliminarRecurso(id, tipo) {
            if (!confirm(`¿Borrar permanentemente?`)) return;
            try {
                const response = await fetch('php/eliminar_recurso.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `id=${id}&tipo=${tipo}`
                });
                const data = await response.json();
                if (data.success) { location.reload(); }
                else { alert(data.error); }
            } catch (err) { alert("Error de red."); }
        }
    </script>
</body>
</html>