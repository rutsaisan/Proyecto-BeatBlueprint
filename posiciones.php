<?php

// 1. GESTIÓN DE SESIÓN Y SEGURIDAD
if (session_status() === PHP_SESSION_NONE) { session_start(); }

// Redirigir al login si el usuario no ha iniciado sesión
if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

// Configuración de base de datos e ID de usuario
require_once 'includes/config.php';
$usuario_id = $_SESSION['id'];

// 2. PROCESAMIENTO DE PETICIONES AJAX (ACCIONES DE BASE DE DATOS)
if (isset($_GET['action'])) {
    header('Content-Type: application/json');
    
    // --- ACCIÓN: GUARDAR O ACTUALIZAR FORMACIÓN ---
    if ($_GET['action'] === 'save') {
        $data = json_decode(file_get_contents('php://input'), true);
        $id_f = isset($data['id_formacion']) ? intval($data['id_formacion']) : null;
        $nombre = $data['esquema'];
        $tipo = $data['tipo']; // Puede venir del select o del input personalizado "Otro"
        $posiciones = $data['posiciones'];

        if ($id_f) {
            // MODO EDICIÓN: Actualizamos la cabecera
            $stmt = $conexion->prepare("UPDATE Formaciones SET nombre_posicion = ?, tipo_formacion = ? WHERE id_formacion = ? AND id_usuario = ?");
            $stmt->bind_param("ssii", $nombre, $tipo, $id_f, $usuario_id);
            $stmt->execute();
            // Limpiamos los bailarines antiguos para insertar las nuevas posiciones
            $conexion->query("DELETE FROM Posiciones_Bailarines WHERE id_formacion = $id_f");
            $id_actual = $id_f;
        } else {
            // MODO NUEVO: Insertamos nueva formación
            $stmt = $conexion->prepare("INSERT INTO Formaciones (nombre_posicion, tipo_formacion, id_usuario) VALUES (?, ?, ?)");
            $stmt->bind_param("ssi", $nombre, $tipo, $usuario_id);
            $stmt->execute();
            $id_actual = $stmt->insert_id;
        }

        // Insertamos cada bailarín con sus coordenadas X e Y
        $stmt_pos = $conexion->prepare("INSERT INTO Posiciones_Bailarines (id_formacion, nombre_bailarin, coord_x, coord_y) VALUES (?, ?, ?, ?)");
        foreach ($posiciones as $p) {
            $stmt_pos->bind_param("isss", $id_actual, $p['nombre'], $p['x'], $p['y']);
            $stmt_pos->execute();
        }
        echo json_encode(['success' => true]);
        exit;
    }

    // --- ACCIÓN: ELIMINAR FORMACIÓN ---
    if ($_GET['action'] === 'delete' && isset($_GET['id'])) {
        $id_del = intval($_GET['id']);
        // Al borrar la formación, las posiciones se borran solas por la relación ON DELETE CASCADE de SQL
        $stmt = $conexion->prepare("DELETE FROM Formaciones WHERE id_formacion = ? AND id_usuario = ?");
        $stmt->bind_param("ii", $id_del, $usuario_id);
        echo json_encode(['success' => $stmt->execute()]);
        exit;
    }

    // --- ACCIÓN: CARGAR POSICIONES DE UNA FORMACIÓN ---
    if ($_GET['action'] === 'load' && isset($_GET['id'])) {
        $id_l = intval($_GET['id']);
        $res = mysqli_query($conexion, "SELECT * FROM Posiciones_Bailarines WHERE id_formacion = $id_l");
        $pos = [];
        while($r = mysqli_fetch_assoc($res)) { $pos[] = $r; }
        echo json_encode($pos);
        exit;
    }
}

// 3. CONSULTA PARA EL LISTADO LATERAL (BIBLIOTECA)
$query_list = "SELECT f.*, (SELECT COUNT(*) FROM Posiciones_Bailarines pb WHERE pb.id_formacion = f.id_formacion) as total 
               FROM Formaciones f WHERE f.id_usuario = $usuario_id ORDER BY created_at DESC";
$res_formaciones = mysqli_query($conexion, $query_list);
?>

<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beat Blueprint - Canvas</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <style>
        /* ESTILOS DE INTERFAZ */
        body { background: #0f0f13; color: white; font-family: 'Inter', sans-serif; overflow: hidden; }
        .main-layout { display: grid; grid-template-columns: 1fr 320px; height: calc(100vh - 73px); }
        
        /* ESCENARIO */
        .stage-container { position: relative; display: flex; justify-content: center; align-items: center; background: radial-gradient(circle at center, #1a1a2e 0%, #0f0f13 100%); }
        #escenario { position: relative; width: 850px; height: 480px; background: #111; border: 2px solid rgba(138, 79, 255, 0.4); box-shadow: 0 0 40px rgba(0,0,0,0.8); }
        .grid-overlay { position: absolute; inset: 0; background-image: linear-gradient(rgba(255,255,255,0.05) 1px, transparent 1px), linear-gradient(90deg, rgba(255,255,255,0.05) 1px, transparent 1px); background-size: 50px 50px; pointer-events: none; }
        
        /* BARRA LATERAL */
        .sidebar-right { background: rgba(255,255,255,0.02); border-left: 1px solid rgba(255,255,255,0.08); backdrop-filter: blur(15px); padding: 1.5rem; overflow-y: auto; }
        .saved-position-card { background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); border-radius: 12px; padding: 12px; margin-bottom: 10px; transition: 0.3s; }
        .saved-position-card:hover { border-color: #8A4FFF; background: rgba(138, 79, 255, 0.05); }
        
        /* BAILARINES (Fichas movibles) */
        .bailarin { position: absolute; width: 60px; cursor: grab; display: flex; flex-direction: column; align-items: center; z-index: 10; }
        .avatar { width: 35px; height: 35px; background: linear-gradient(135deg, #8A4FFF, #FF4F8B); border: 2px solid white; border-radius: 50%; }
        .nombre { margin-top: 4px; background: rgba(0,0,0,0.8); font-size: 10px; padding: 2px 8px; border-radius: 4px; border: 1px solid #8A4FFF; outline: none; }
        
        /* MODAL */
        #modal-guardar { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(8px); }
    </style>
</head>
<body class="flex flex-col h-screen">

    <header class="p-4 bg-black/60 backdrop-blur-md border-b border-white/10 flex justify-between items-center z-50">
        <div class="flex items-center gap-4">
            <a href="feed.php" class="text-gray-400 hover:text-white"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-lg font-bold text-purple-400 uppercase tracking-tighter">Pizarra de Posiciones</h1>
        </div>
        <div class="flex gap-3">
            <button onclick="nuevaPosicion()" class="bg-white/5 border border-white/10 px-4 py-2 rounded-lg text-xs font-bold hover:bg-white/10 transition">
                <i class="fas fa-file mr-2"></i> Nuevo
            </button>
            <button id="btn-add" class="bg-white/5 border border-white/10 px-4 py-2 rounded-lg text-xs font-bold hover:bg-white/10 transition">
                <i class="fas fa-user-plus mr-2 text-purple-400"></i> Bailarín
            </button>
            <button onclick="abrirModal()" class="bg-purple-600 px-5 py-2 rounded-lg text-xs font-bold hover:bg-purple-500 shadow-lg shadow-purple-500/20 transition">
                <i class="fas fa-save mr-2"></i> Guardar
            </button>
        </div>
    </header>

    <div class="main-layout">
        <main class="stage-container">
            <div id="escenario">
                <div class="grid-overlay"></div>
                </div>
        </main>

        <aside class="sidebar-right">
            <h2 class="text-[10px] font-bold text-gray-500 uppercase tracking-widest mb-4">Biblioteca de Esquemas</h2>
            <div id="lista-formaciones">
                <?php while($f = mysqli_fetch_assoc($res_formaciones)): ?>
                <div class="saved-position-card group relative">
                    <div class="cursor-pointer" onclick="cargarFormacion(<?php echo $f['id_formacion']; ?>, '<?php echo addslashes($f['nombre_posicion']); ?>', '<?php echo $f['tipo_formacion']; ?>')">
                        <p class="text-sm font-bold text-white"><?php echo htmlspecialchars($f['nombre_posicion']); ?></p>
                        <p class="text-[9px] text-gray-500 uppercase"><?php echo $f['tipo_formacion']; ?> • <?php echo $f['total']; ?> Pers.</p>
                    </div>
                    <button onclick="eliminarFormacion(<?php echo $f['id_formacion']; ?>)" class="absolute top-3 right-3 text-gray-600 hover:text-red-500 transition">
                        <i class="fas fa-trash-alt text-xs"></i>
                    </button>
                </div>
                <?php endwhile; ?>
            </div>
        </aside>
    </div>

    <div id="modal-guardar">
        <div class="bg-[#1a1a2e] p-8 rounded-3xl border border-purple-500/30 w-full max-w-md shadow-2xl">
            <h2 id="modal-title" class="text-xl font-bold mb-6 text-purple-400">Guardar Posición</h2>
            <form id="form-guardar" class="space-y-4">
                <input type="hidden" id="edit-id"> <div>
                    <label class="text-[10px] text-gray-500 block mb-1">NOMBRE</label>
                    <input type="text" id="nombre-posicion" required class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-white focus:border-purple-500 outline-none transition">
                </div>

                <div>
                    <label class="text-[10px] text-gray-500 block mb-1">TIPO DE FORMACIÓN</label>
                    <select id="tipo-formacion" onchange="gestionarOtro()" class="w-full bg-black/40 border border-white/10 rounded-xl p-3 text-white outline-none cursor-pointer">
                        <option value="Formación en V">Formación en V</option>
                        <option value="Bloque Central">Bloque Central</option>
                        <option value="Línea">Línea</option>
                        <option value="Dispersos">Dispersos</option>
                        <option value="OTRO">-- Otro (Especificar) --</option>
                    </select>
                </div>

                <div id="contenedor-otro" style="display: none;">
                    <label class="text-[10px] text-purple-400 block mb-1 font-bold tracking-widest">PERSONALIZADO</label>
                    <input type="text" id="tipo-personalizado" class="w-full bg-purple-500/10 border border-purple-500/40 rounded-xl p-3 text-white outline-none" placeholder="Escribe el tipo aquí...">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="cerrarModal()" class="flex-1 p-3 rounded-xl bg-white/5 text-xs font-bold hover:bg-white/10 transition">Cancelar</button>
                    <button type="submit" class="flex-1 p-3 rounded-xl bg-purple-600 text-xs font-bold hover:bg-purple-500 transition">Confirmar</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        /**
         * LÓGICA DE CONTROL (JAVASCRIPT)
         */
        let currentId = null; // Controla si estamos editando o creando
        const escenario = document.getElementById('escenario');

        // Muestra/Oculta el campo de texto si se selecciona "OTRO"
        function gestionarOtro() {
            const select = document.getElementById('tipo-formacion');
            const divOtro = document.getElementById('contenedor-otro');
            divOtro.style.display = (select.value === 'OTRO') ? 'block' : 'none';
        }

        // Función Maestra: Crea un bailarín y le añade la lógica de "Arrastrar y Soltar"
        function crearBailarin(x, y, nombre = "Bailarín") {
            const div = document.createElement('div');
            div.className = 'bailarin';
            div.style.left = x + 'px'; div.style.top = y + 'px';
            div.innerHTML = `<div class="avatar"></div><div class="nombre" contenteditable="true" spellcheck="false">${nombre}</div>`;
            
            // Lógica de arrastre
            div.onmousedown = (e) => {
                if (e.target.classList.contains('nombre')) return; // No arrastrar si estamos editando el nombre
                let shiftX = e.clientX - div.getBoundingClientRect().left;
                let shiftY = e.clientY - div.getBoundingClientRect().top;

                document.onmousemove = (ev) => {
                    let rect = escenario.getBoundingClientRect();
                    // Cálculo de nueva posición restringida al escenario
                    let newX = ev.clientX - rect.left - shiftX;
                    let newY = ev.clientY - rect.top - shiftY;
                    div.style.left = Math.max(0, Math.min(newX, rect.width - 60)) + 'px';
                    div.style.top = Math.max(0, Math.min(newY, rect.height - 60)) + 'px';
                };
                document.onmouseup = () => document.onmousemove = null;
            };
            escenario.appendChild(div);
        }

        // Limpia el lienzo y resetea el estado para una formación nueva
        function nuevaPosicion() {
            if(confirm("¿Deseas empezar una nueva formación?")) {
                currentId = null;
                document.querySelectorAll('.bailarin').forEach(b => b.remove());
                document.getElementById('form-guardar').reset();
                document.getElementById('contenedor-otro').style.display = 'none';
                crearBailarin(400, 220, "Centro");
            }
        }

        // Carga una formación desde la base de datos al lienzo
        async function cargarFormacion(id, nombre, tipo) {
            currentId = id;
            document.getElementById('nombre-posicion').value = nombre;
            
            // Detectar si el tipo es estándar o personalizado
            const select = document.getElementById('tipo-formacion');
            const inputOtro = document.getElementById('tipo-personalizado');
            let existeEnSelect = Array.from(select.options).some(opt => opt.value === tipo);
            
            if (existeEnSelect) {
                select.value = tipo;
                document.getElementById('contenedor-otro').style.display = 'none';
            } else {
                select.value = 'OTRO';
                inputOtro.value = tipo;
                document.getElementById('contenedor-otro').style.display = 'block';
            }

            // Fetch de los bailarines específicos
            const res = await fetch(`posiciones.php?action=load&id=${id}`);
            const data = await res.json();
            document.querySelectorAll('.bailarin').forEach(b => b.remove());
            data.forEach(p => crearBailarin(p.coord_x, p.coord_y, p.nombre_bailarin));
        }

        // Eliminar registro
        async function eliminarFormacion(id) {
            if(confirm("¿Seguro que quieres eliminar esta formación permanentemente?")) {
                const res = await fetch(`posiciones.php?action=delete&id=${id}`);
                const data = await res.json();
                if(data.success) location.reload();
            }
        }

        // Modales
        function abrirModal() { 
            document.getElementById('modal-title').innerText = currentId ? "Actualizar Esquema" : "Guardar Esquema";
            document.getElementById('modal-guardar').style.display = 'flex'; 
        }
        function cerrarModal() { document.getElementById('modal-guardar').style.display = 'none'; }

        // ENVÍO DE DATOS AL SERVIDOR (GUARDAR)
        document.getElementById('form-guardar').onsubmit = async (e) => {
            e.preventDefault();
            
            const valorSelect = document.getElementById('tipo-formacion').value;
            const tipoFinal = (valorSelect === 'OTRO') ? document.getElementById('tipo-personalizado').value : valorSelect;

            const bailarines = [];
            document.querySelectorAll('.bailarin').forEach(b => {
                bailarines.push({ 
                    nombre: b.querySelector('.nombre').innerText, 
                    x: b.style.left.replace('px',''), 
                    y: b.style.top.replace('px','') 
                });
            });

            const body = { 
                id_formacion: currentId,
                esquema: document.getElementById('nombre-posicion').value,
                tipo: tipoFinal,
                posiciones: bailarines 
            };

            const res = await fetch('posiciones.php?action=save', { method: 'POST', body: JSON.stringify(body) });
            const data = await res.json();
            if(data.success) location.reload();
        };

        // Botón rápido para añadir bailarín
        document.getElementById('btn-add').onclick = () => crearBailarin(400, 220);

        // Al inicio: Ponemos un bailarín por defecto si el lienzo está vacío
        window.onload = () => { if(document.querySelectorAll('.bailarin').length === 0) crearBailarin(400, 220, "Centro"); };
    </script>
</body>
</html>