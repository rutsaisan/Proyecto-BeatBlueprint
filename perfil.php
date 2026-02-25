<?php
/**
 * BEAT BLUEPRINT - PERFIL DE USUARIO COMPLETO
 * Incluye: Cambio de Foto, Edición de Datos y Borrado de Cuenta.
 */

if (session_status() === PHP_SESSION_NONE) { session_start(); }
require_once 'includes/config.php';

// SEGURIDAD
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php"); exit;
}

$usuario_id = $_SESSION['id'];

// --- PROCESAMIENTO DE FORMULARIOS (POST) ---
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    
    // 1. LÓGICA DE LA FOTO (SUBIDA REAL)
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === 0) {
        $directorio = "assets/uploads/perfiles/";
        if (!is_dir($directorio)) { mkdir($directorio, 0777, true); }
        
        $extension = pathinfo($_FILES['foto']['name'], PATHINFO_EXTENSION);
        $nombre_archivo = "user_" . $usuario_id . "_" . time() . "." . $extension;
        $ruta_final = $directorio . $nombre_archivo;

        if (move_uploaded_file($_FILES['foto']['tmp_name'], $ruta_final)) {
            $stmt = $conexion->prepare("UPDATE Usuarios SET foto_perfil = ? WHERE id_usuario = ?");
            $stmt->bind_param("si", $ruta_final, $usuario_id);
            $stmt->execute();
            $_SESSION['foto'] = $ruta_final; // Actualizamos sesión
            header("Location: perfil.php?status=foto_actualizada"); exit;
        }
    }

    // 2. ACTUALIZAR EMAIL Y ESTILO
    if (isset($_POST['update_profile'])) {
        $nuevo_email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
        $nuevo_estilo = !empty($_POST['estilo']) ? intval($_POST['estilo']) : null;
        
        $stmt = $conexion->prepare("UPDATE Usuarios SET email = ?, id_estilo_baile = ? WHERE id_usuario = ?");
        $stmt->bind_param("sii", $nuevo_email, $nuevo_estilo, $usuario_id);
        $stmt->execute();
        header("Location: perfil.php?status=datos_actualizados"); exit;
    }

    // 3. ELIMINAR CUENTA (BORRADO TOTAL)
    if (isset($_POST['delete_account'])) {
        $stmt = $conexion->prepare("DELETE FROM Usuarios WHERE id_usuario = ?");
        $stmt->bind_param("i", $usuario_id);
        if ($stmt->execute()) {
            session_destroy();
            header("Location: index.php?msg=cuenta_eliminada"); exit;
        }
    }
}

// OBTENER DATOS PARA MOSTRAR
$query = "SELECT u.*, e.nombre_estilo_baile FROM Usuarios u 
          LEFT JOIN Estilo_baile e ON u.id_estilo_baile = e.id_estilo_baile 
          WHERE u.id_usuario = $usuario_id";
$user = mysqli_fetch_assoc(mysqli_query($conexion, $query));
$estilos_res = mysqli_query($conexion, "SELECT * FROM Estilo_baile");
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Perfil - Beat Blueprint</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        body { 
            background: radial-gradient(circle at 50% -20%, #4a2b69 0%, #0f0f13 60%);
            color: white; min-height: 100vh; font-family: 'Inter', sans-serif;
            background-attachment: fixed;
        }
        .glass-card { 
            background: rgba(255, 255, 255, 0.05); 
            backdrop-filter: blur(15px); 
            border: 1px solid rgba(255, 255, 255, 0.1); 
        }
    </style>
</head>
<body class="p-6">

    <div class="max-w-xl mx-auto flex justify-between items-center mb-8">
        <a href="feed.php" class="text-gray-400 hover:text-white transition"><i class="fas fa-arrow-left mr-2"></i> Volver</a>
        <h1 class="text-lg font-bold uppercase tracking-widest text-purple-400">Perfil</h1>
        <a href="php/logout.php" class="text-red-400 text-xs font-bold uppercase">Cerrar Sesión</a>
    </div>

    <div class="max-w-xl mx-auto space-y-6">
        
        <div class="glass-card rounded-[2.5rem] p-10 flex flex-col items-center">
            <form action="" method="POST" enctype="multipart/form-data" id="form-foto" class="relative group cursor-pointer">
                <div class="w-36 h-36 rounded-full overflow-hidden border-4 border-purple-500/30 group-hover:border-purple-500 transition-all shadow-2xl">
                    <?php if (!empty($user['foto_perfil']) && file_exists($user['foto_perfil'])): ?>
                        <img src="<?php echo $user['foto_perfil']; ?>" class="w-full h-full object-cover">
                    <?php else: ?>
                        <div class="w-full h-full bg-gradient-to-tr from-purple-500 to-pink-500 flex items-center justify-center text-5xl">
                            <i class="fas fa-user text-white"></i>
                        </div>
                    <?php endif; ?>
                </div>
                
                <div class="absolute inset-0 flex items-center justify-center bg-black/40 rounded-full opacity-0 group-hover:opacity-100 transition">
                    <i class="fas fa-camera text-white text-2xl"></i>
                </div>
                
                <input type="file" name="foto" id="input-foto" class="hidden" onchange="document.getElementById('form-foto').submit()">
            </form>

            <h2 class="text-2xl font-bold mt-6"><?php echo htmlspecialchars($user['nombre_completo']); ?></h2>
            <p class="text-purple-400 font-medium">@<?php echo htmlspecialchars($user['usuario']); ?></p>
        </div>

        <form action="" method="POST" class="glass-card rounded-[2.5rem] p-8 space-y-5">
            <p class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-2">Ajustes de cuenta</p>
            
            <div class="space-y-4">
                <input type="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" 
                       class="w-full bg-black/20 border border-white/10 p-4 rounded-2xl outline-none focus:border-purple-500 transition" placeholder="Email">
                
                <select name="estilo" class="w-full bg-black/20 border border-white/10 p-4 rounded-2xl outline-none cursor-pointer">
                    <option value="">Sin estilo preferido</option>
                    <?php while($est = mysqli_fetch_assoc($estilos_res)): ?>
                        <option value="<?php echo $est['id_estilo_baile']; ?>" <?php echo ($user['id_estilo_baile'] == $est['id_estilo_baile']) ? 'selected' : ''; ?> class="bg-[#1a1a2e]">
                            <?php echo htmlspecialchars($est['nombre_estilo_baile']); ?>
                        </option>
                    <?php endwhile; ?>
                </select>
            </div>

            <button type="submit" name="update_profile" class="w-full bg-purple-600 hover:bg-purple-500 p-4 rounded-2xl font-bold text-xs transition shadow-lg shadow-purple-900/20">
                ACTUALIZAR DATOS
            </button>
        </form>

        <div class="glass-card rounded-[2.5rem] p-6 border border-red-500/20 bg-red-500/5 flex justify-between items-center">
            <div class="ml-2">
                <p class="text-red-500 font-bold text-sm">Zona de Peligro</p>
                <p class="text-[10px] text-gray-400">Borrar cuenta permanentemente</p>
            </div>
            <form action="" method="POST" onsubmit="return confirm('¿Estás seguro de que quieres borrar tu perfil? Esta acción es irreversible.')">
                <button type="submit" name="delete_account" class="bg-red-500/20 hover:bg-red-500 text-red-500 hover:text-white px-6 py-2 rounded-xl text-[10px] font-bold transition">
                    ELIMINAR
                </button>
            </form>
        </div>

    </div>

    <script>
        // Pequeño script para asegurar que el clic en el avatar dispare el selector de archivos
        document.querySelector('.group.cursor-pointer').addEventListener('click', function() {
            document.getElementById('input-foto').click();
        });
    </script>
</body>
</html>