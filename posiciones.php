<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beat Blueprint - Pizarra de Posiciones</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    
    <style>
        body { background-color: #0f0f13; color: white; font-family: 'Inter', sans-serif; overflow: hidden; touch-action: none; }
        
        /* Layout Principal: Escenario a la izquierda, Menú a la derecha */
        .main-layout {
            display: grid;
            grid-template-columns: 1fr 320px;
            height: calc(100vh - 73px);
        }

        .stage-container { 
            position: relative;
            display: flex; 
            justify-content: center; 
            align-items: center; 
            padding: 20px;
            background: radial-gradient(circle at center, #1a1a2e 0%, #0f0f13 100%);
        }

        #escenario {
            position: relative;
            width: 850px;
            height: 480px;
            background-image: url('escenario.png'); 
            background-size: 100% 100%;
            background-repeat: no-repeat;
            border: 2px solid rgba(138, 79, 255, 0.4);
            box-shadow: 0 0 40px rgba(0,0,0,0.9);
            overflow: hidden;
        }

        /* CUADRÍCULA TÉCNICA */
        .grid-overlay {
            position: absolute;
            inset: 0;
            background-image: 
                linear-gradient(rgba(255, 255, 255, 0.05) 1px, transparent 1px),
                linear-gradient(90deg, rgba(255, 255, 255, 0.05) 1px, transparent 1px);
            background-size: 50px 50px;
            pointer-events: none;
            z-index: 1;
        }

        /* MENU DERECHO */
        .sidebar-right {
            background: rgba(255, 255, 255, 0.02);
            border-left: 1px solid rgba(255, 255, 255, 0.08);
            backdrop-filter: blur(15px);
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
        }

        .saved-position-card {
            background: rgba(255, 255, 255, 0.03);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 12px;
            padding: 15px;
            margin-bottom: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
        }

        .saved-position-card:hover {
            background: rgba(138, 79, 255, 0.1);
            border-color: #8A4FFF;
            transform: translateX(-5px);
        }

        /* BAILARINES */
        .bailarin { position: absolute; width: 60px; cursor: grab; display: flex; flex-direction: column; align-items: center; z-index: 10; user-select: none; }
        .avatar { width: 40px; height: 40px; background: linear-gradient(135deg, #8A4FFF 0%, #FF4F8B 100%); border: 2px solid white; border-radius: 50%; box-shadow: 0 4px 10px rgba(0,0,0,0.5); }
        .nombre { margin-top: 4px; background: rgba(0, 0, 0, 0.85); color: white; font-size: 11px; padding: 2px 10px; border-radius: 4px; border: 1px solid #8A4FFF; white-space: nowrap; outline: none; }
        
        /* MODAL */
        #modal-guardar { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 100; align-items: center; justify-content: center; backdrop-filter: blur(8px); }
        
        .custom-scrollbar::-webkit-scrollbar { width: 5px; }
        .custom-scrollbar::-webkit-scrollbar-thumb { background: rgba(138, 79, 255, 0.3); border-radius: 10px; }
    </style>
</head>
<body class="flex flex-col h-screen">

    <header class="p-4 bg-black/60 backdrop-blur-md border-b border-white/10 flex justify-between items-center z-50">
        <div class="flex items-center gap-4">
            <a href="feed.php" class="text-gray-400 hover:text-white transition"><i class="fas fa-arrow-left"></i></a>
            <h1 class="text-lg font-bold tracking-tighter uppercase text-purple-400">Pizarra de Posiciones</h1>
        </div>
        
        <div class="flex gap-4">
            <button id="btn-add" class="bg-white/5 hover:bg-white/10 border border-white/10 px-4 py-2 rounded-lg text-sm font-bold transition">
                <i class="fas fa-plus text-purple-400 mr-2"></i> Añadir
            </button>
            <button id="btn-open-modal" class="bg-purple-600 hover:bg-purple-500 px-5 py-2 rounded-lg text-sm font-bold transition shadow-lg shadow-purple-500/20">
                <i class="fas fa-save mr-2"></i> Guardar Formación
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
            <h2 class="text-[10px] font-bold text-gray-500 uppercase tracking-[0.2em] mb-6 flex items-center gap-2">
                <i class="fas fa-layer-group text-purple-500"></i> Biblioteca de Posiciones
            </h2>

            <div class="overflow-y-auto pr-2 custom-scrollbar">
                <div class="saved-position-card group">
                    <div class="flex justify-between items-start mb-2">
                        <span class="text-sm font-bold text-white group-hover:text-purple-400 transition">Intro: Pirámide</span>
                        <span class="text-[9px] text-gray-500 uppercase">Hoy</span>
                    </div>
                    <div class="flex flex-wrap gap-2">
                        <span class="text-[9px] bg-purple-500/10 text-purple-400 border border-purple-500/20 px-2 py-0.5 rounded">Formación en V</span>
                        <span class="text-[9px] bg-white/5 text-gray-400 px-2 py-0.5 rounded">5 bailarines</span>
                    </div>
                </div>

                <div class="mt-10 text-center opacity-30">
                    <i class="fas fa-cloud-upload-alt text-3xl mb-3 block text-gray-600"></i>
                    <p class="text-[10px] italic leading-relaxed px-4 text-gray-400 uppercase tracking-widest">Tus formaciones aparecerán aquí tras guardarlas</p>
                </div>
            </div>
        </aside>
    </div>

    <div id="modal-guardar">
        <div class="bg-[#1a1a2e] p-8 rounded-3xl border border-purple-500/30 w-full max-w-md shadow-2xl scale-95 animate-in zoom-in duration-200">
            <h2 class="text-xl font-bold mb-1 text-purple-400">Detalles de Posición</h2>
            <p class="text-gray-400 text-[10px] mb-6 uppercase tracking-widest">Configura tu esquema antes de guardar</p>
            
            <form id="form-guardar" class="space-y-4">
                <div>
                    <label class="text-[10px] text-gray-500 ml-1 mb-1 block">NOMBRE DE LA POSICIÓN</label>
                    <input type="text" id="nombre-posicion" placeholder="Ej: Drop Estribillo" required
                        class="w-full bg-black/40 border border-white/10 rounded-xl p-3 focus:outline-none focus:border-purple-500 text-white transition">
                </div>

                <div>
                    <label class="text-[10px] text-gray-500 ml-1 mb-1 block">DISPOSICIÓN VISUAL</label>
                    <select id="tipo-formacion" class="w-full bg-black/40 border border-white/10 rounded-xl p-3 focus:outline-none focus:border-purple-500 text-white transition cursor-pointer">
                        <option value="Formación en V">Formación en V</option>
                        <option value="Bloque Central">Bloque Central</option>
                        <option value="Línea Horizontal">Línea Horizontal</option>
                        <option value="Diagonal Izquierda">Diagonal Izquierda</option>
                        <option value="Dispersos / Caos">Dispersos / Caos</option>
                    </select>
                </div>

                <div>
                    <label class="text-[10px] text-gray-500 ml-1 mb-1 block">RESUMEN DEL EQUIPO</label>
                    <input type="text" id="count-bailarines" readonly 
                        class="w-full bg-white/5 border border-white/5 rounded-xl p-3 text-purple-300 font-bold outline-none cursor-default">
                </div>

                <div class="flex gap-3 pt-4">
                    <button type="button" onclick="cerrarModal()" class="flex-1 px-4 py-3 rounded-xl bg-white/5 hover:bg-white/10 font-bold text-xs transition">Cancelar</button>
                    <button type="submit" class="flex-1 px-4 py-3 rounded-xl bg-purple-600 hover:bg-purple-500 font-bold text-xs transition">Confirmar Guardado</button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const escenario = document.getElementById('escenario');
        const modal = document.getElementById('modal-guardar');
        const form = document.getElementById('form-guardar');
        const countInput = document.getElementById('count-bailarines');

        // Función para crear un nuevo bailarín con arrastre
        function crearBailarin(x, y, nombre = "Bailarín") {
            const div = document.createElement('div');
            div.className = 'bailarin';
            div.style.left = x + 'px';
            div.style.top = y + 'px';
            div.innerHTML = `
                <div class="avatar"></div>
                <div class="nombre" contenteditable="true" spellcheck="false">${nombre}</div>
            `;

            div.addEventListener('mousedown', function(e) {
                if (e.target.classList.contains('nombre')) return;
                
                let shiftX = e.clientX - div.getBoundingClientRect().left;
                let shiftY = e.clientY - div.getBoundingClientRect().top;

                function moveAt(pageX, pageY) {
                    let rect = escenario.getBoundingClientRect();
                    let newX = pageX - rect.left - shiftX;
                    let newY = pageY - rect.top - shiftY;
                    
                    // Límites del escenario
                    div.style.left = Math.max(0, Math.min(newX, rect.width - div.offsetWidth)) + 'px';
                    div.style.top = Math.max(0, Math.min(newY, rect.height - div.offsetHeight)) + 'px';
                }

                function onMouseMove(e) { moveAt(e.clientX, e.clientY); }
                document.addEventListener('mousemove', onMouseMove);
                
                document.onmouseup = () => { 
                    document.removeEventListener('mousemove', onMouseMove); 
                    document.onmouseup = null; 
                };
            });

            div.ondragstart = () => false;
            escenario.appendChild(div);
        }

        // Manejo del Modal
        document.getElementById('btn-open-modal').onclick = () => {
            const numBailarines = document.querySelectorAll('.bailarin').length;
            countInput.value = numBailarines + (numBailarines === 1 ? " bailarín" : " bailarines");
            modal.style.display = 'flex';
        };

        function cerrarModal() { modal.style.display = 'none'; }

        // Manejo del Guardado
        form.onsubmit = function(e) {
            e.preventDefault();
            const nombrePos = document.getElementById('nombre-posicion').value;
            const tipoForm = document.getElementById('tipo-formacion').value;
            
            const datosBailarines = [];
            document.querySelectorAll('.bailarin').forEach(b => {
                datosBailarines.push({
                    nombre: b.querySelector('.nombre').innerText,
                    x: b.style.left,
                    y: b.style.top
                });
            });

            console.log("GUARDANDO:", {
                esquema: nombrePos,
                tipo: tipoForm,
                total: datosBailarines.length,
                posiciones: datosBailarines
            });

            alert(`Posición "${nombrePos}" capturada.\nTotal: ${datosBailarines.length} bailarines en ${tipoForm}.`);
            cerrarModal();
        };

        // Botones de acción
        document.getElementById('btn-add').onclick = () => crearBailarin(400, 220, "Bailarín");

        // Inicio por defecto
        window.onload = () => {
            crearBailarin(400, 220, "Centro");
        };
    </script>
</body>
</html>