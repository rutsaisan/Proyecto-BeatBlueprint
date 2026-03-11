<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['loggedin']) || $_SESSION['loggedin'] !== true) {
    header("Location: index.php");
    exit;
}

include "includes/config.php";
$usuario_id = $_SESSION['id'] ?? 1;

// --- CONSULTA PARA LA ÚLTIMA CANCIÓN ---
$sql_cancion = "SELECT nombre_cancion, created_at FROM Canciones WHERE id_usuario = ? ORDER BY id_cancion DESC LIMIT 1";
$stmt_cancion = $conexion->prepare($sql_cancion);
$stmt_cancion->bind_param("i", $usuario_id);
$stmt_cancion->execute();
$res_cancion = $stmt_cancion->get_result();
$ultima_cancion = $res_cancion->fetch_assoc();

// --- CONSULTA PARA EL ÚLTIMO VIDEO ---
$sql_video = "SELECT descripcion, created_at FROM Videos WHERE id_usuario = ? ORDER BY id_video DESC LIMIT 1";
$stmt_video = $conexion->prepare($sql_video);
$stmt_video->bind_param("i", $usuario_id);
$stmt_video->execute();
$res_video = $stmt_video->get_result();
$ultimo_video = $res_video->fetch_assoc();

// --- CONSULTA: DATOS DEL USUARIO ---
$sql_user = "SELECT foto_perfil FROM Usuarios WHERE id_usuario = ?";
$stmt_user = $conexion->prepare($sql_user);
$stmt_user->bind_param("i", $usuario_id);
$stmt_user->execute();
$res_user = $stmt_user->get_result();
$user_data = $res_user->fetch_assoc();

function haceCuanto($fecha) {
    if(!$fecha) return "No hay actividad";
    $timestamp = strtotime($fecha);
    $diferencia = time() - $timestamp;
    if ($diferencia < 3600) return "Hace poco";
    if ($diferencia < 86400) return "Hoy";
    return date("d/m", $timestamp);
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

        .glass-card {
            background: rgba(30, 30, 40, 0.6);
            backdrop-filter: blur(12px);
            border: 1px solid rgba(255, 255, 255, 0.05);
            border-radius: 20px;
        }

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

        .language-pill {
            background: rgba(255, 255, 255, 0.05);
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 4px 12px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        #language-selector {
            background: transparent;
            color: white;
            border: none;
            font-size: 0.75rem;
            font-weight: 600;
            cursor: pointer;
            outline: none;
        }

        #language-selector option { background: #1a1a2e; }
    </style>
</head>
<body>
    <div class="fixed top-6 right-20 z-50">
        <div class="language-pill">
            <i class="fas fa-globe-americas text-[10px] text-purple-400"></i>
            <select id="language-selector">
                <option value="es">ESP</option>
                <option value="en">ENG</option>
                <option value="val">VAL</option>
            </select>
        </div>
    </div>

    <div id="screen-dashboard" class="screen active">
        <div class="flex justify-between items-center mb-6">
            <h2 class="text-2xl font-bold italic">Beat Blueprint</h2>
            <a href="perfil.php" class="w-10 h-10 rounded-full overflow-hidden border-2 border-white flex items-center justify-center bg-purple-600">
                <?php if (!empty($user_data['foto_perfil']) && file_exists($user_data['foto_perfil'])): ?>
                    <img src="<?php echo $user_data['foto_perfil']; ?>" class="w-full h-full object-cover">
                <?php else: ?>
                    <i class="fas fa-user text-white"></i>
                <?php endif; ?>
            </a>
        </div>

        <div class="glass-card p-4 mb-8 flex-grow">
            <h3 data-i18n="activity_title" class="text-gray-300 text-sm mb-3 border-b border-gray-600 pb-2">Tus últimas actividades</h3>
            <div class="space-y-3">
                <div class="flex items-center gap-3 p-2 rounded-lg">
                    <div class="bg-blue-500/20 p-2 rounded text-blue-400"><i class="fas fa-music"></i></div>
                    <div>
                        <p class="font-semibold text-sm"><?php echo $ultima_cancion ? $ultima_cancion['nombre_cancion'] : "Sin canciones"; ?></p>
                        <p class="text-xs text-gray-400"><?php echo $ultima_cancion ? "Añadida: " . haceCuanto($ultima_cancion['created_at']) : "Sube tu primer track"; ?></p>
                    </div>
                </div>

                <div class="flex items-center gap-3 p-2 rounded-lg">
                    <div class="bg-green-500/20 p-2 rounded text-green-400"><i class="fas fa-video"></i></div>
                    <div>
                        <p class="font-semibold text-sm"><?php echo $ultimo_video ? $ultimo_video['descripcion'] : "Sin videos nuevos"; ?></p>
                        <p class="text-xs text-gray-400"><?php echo $ultimo_video ? "Subido: " . haceCuanto($ultimo_video['created_at']) : "Registra tu ensayo"; ?></p>
                    </div>
                </div>

                <div class="bg-purple-900/30 p-3 rounded-lg text-center mt-4">
                    <p id="frase-display" class="text-sm text-purple-200 italic">Cargando inspiración...</p>
                </div>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4 mt-auto mb-6">
            <a href="musica.php" class="glass-card p-6 flex flex-col items-center gap-3 border-t-4 border-purple-500">
                <i class="fas fa-music text-3xl text-purple-400"></i>
                <span data-i18n="menu_music" class="font-medium text-sm">Música</span>
            </a>
            <a href="posiciones.php" class="glass-card p-6 flex flex-col items-center gap-3 border-t-4 border-blue-500">
                <i class="fas fa-map-marker-alt text-3xl text-blue-400"></i>
                <span data-i18n="menu_pos" class="font-medium text-sm">Posiciones</span>
            </a>
            <a href="vidioteca.php" class="glass-card p-6 flex flex-col items-center gap-3 border-t-4 border-pink-500">
                <i class="fas fa-play-circle text-3xl text-pink-400"></i>
                <span data-i18n="menu_vid" class="font-medium text-sm">Vidioteca</span>
            </a>
            <a href="wiki.html" class="glass-card p-6 flex flex-col items-center gap-3 border-t-4 border-yellow-500">
                <i class="fas fa-book text-3xl text-yellow-400"></i>
                <span data-i18n="menu_wiki" class="font-medium text-sm">Wiki Pasos</span>
            </a>
        </div>
        
        <div class="text-center pb-4">
             <a data-i18n="logout" href="index.php" class="text-gray-400 text-xs">Cerrar Sesión</a>
        </div>
    </div>

<script>
    const translations = {
        es: {
            activity_title: "Tus últimas actividades",
            menu_music: "Música",
            menu_pos: "Posiciones",
            menu_vid: "Vidioteca",
            menu_wiki: "Wiki Pasos",
            logout: "Cerrar Sesión"
        },
        en: {
            activity_title: "Your latest activities",
            menu_music: "Music",
            menu_pos: "Formations",
            menu_vid: "Video Library",
            menu_wiki: "Step Wiki",
            logout: "Logout"
        },
        val: {
            activity_title: "Les teues últimes activitats",
            menu_music: "Musica",
            menu_pos: "Formacions",
            menu_vid: "Videoteca",
            menu_wiki: "Wiki de passos",
            logout: "Tancar Sessió"
        }
    };

    const frasesDanza = {
        es: [
                "La danza es el lenguaje oculto del alma.",
                "No intentes bailar mejor que nadie. Intenta bailar mejor que tú mismo.",
                "Si puedes hablar, puedes cantar. Si puedes caminar, puedes bailar.",
                "La técnica es solo la base; la pasión es lo que te hace volar.",
                "El baile es una forma de llegar a la libertad.",
                "Tu cuerpo es tu instrumento, mantenlo afinado.",
                "En el escenario no hay errores, solo nuevas oportunidades de improvisar.",
                "La vida no consiste en esperar a que pase la tormenta, sino en aprender a bailar bajo la lluvia.",
                "Baila como si nadie te estuviera mirando.",
                "La danza es el reflejo de lo que el cuerpo convierte en arte.",
                "Hay atajos para la felicidad, y el baile es uno de ellos.",
                "Bailar es alcanzar una palabra que no existe. Cantar una canción de mil generaciones. Sentir el significado de un momento.",
                "Un bailarín baila porque su sangre baila en sus venas.",
                "Bailar es la única actividad donde 'perder el paso' es un deporte de riesgo, pero 'perder la cabeza' es lo esperado.",
                "Un gran bailarín no se define por sus piruetas, sino por cuántas veces se levanta después de una caída.",
                "La técnica es el mapa, pero la pasión es el destino.",
                "No bailes para impresionar; baila para expresar lo que las palabras no alcanzan a decir.",
                "El único mal baile es aquel que no se hizo por miedo a hacer el ridículo.",
                "Tus pies son las herramientas, pero el ritmo lo dicta tu corazón.",
                "Si te equivocas en el paso, sonríe y sigue. En el baile, como en la vida, el espectáculo debe continuar.",
                "Hay momentos que no se pueden explicar, solo se pueden bailar.",
                "La danza es el único lenguaje universal que no necesita traducción, solo sentimiento.",
                "No dejes que el miedo a no ser 'perfecto' te robe el placer de moverte.",
                "Baila por ti, para ti y a pesar de todo.",
                "La vida es una pista de baile: unos miran, otros critican, pero solo los que se atreven a salir a bailar son los que realmente viven."
            ],
        en: [
                "Dance is the hidden language of the soul.",
                "Don't try to dance better than anyone else. Try to dance better than yourself.",
                "If you can talk, you can sing. If you can walk, you can dance.",
                "Technique is only the foundation; passion is what makes you fly.",
                "Dance is a way to reach freedom.",
                "Your body is your instrument, keep it tuned.",
                "On stage there are no mistakes, only new opportunities to improvise.",
                "Life isn't about waiting for the storm to pass, it's about learning to dance in the rain.",
                "Dance like nobody is watching.",
                "Dance is the reflection of what the body turns into art.",
                "There are shortcuts to happiness, and dancing is one of them.",
                "To dance is to reach a word that doesn't exist. To sing a song of a thousand generations. To feel the meaning of a moment.",
                "A dancer dances because their blood dances in their veins.",
                "Dancing is the only activity where 'losing your step' is a risky sport, but 'losing your mind' is expected.",
                "A great dancer is not defined by their pirouettes, but by how many times they get up after a fall.",
                "Technique is the map, but passion is the destination.",
                "Don't dance to impress; dance to express what words cannot say.",
                "The only bad dance is the one that wasn't done for fear of looking ridiculous.",
                "Your feet are the tools, but the rhythm is dictated by your heart.",
                "If you miss a step, smile and keep going. In dance, as in life, the show must go on.",
                "There are moments that cannot be explained, only danced.",
                "Dance is the only universal language that doesn't need translation, only feeling.",
                "Don't let the fear of not being 'perfect' steal the pleasure of moving.",
                "Dance for you, for yourself, and in spite of everything.",
                "Life is a dance floor: some watch, others criticize, but only those who dare to go out and dance are the ones who truly live."
        ],
        val: [
                "La dansa és el llenguatge ocult de l'ànima.",
                "No intentes ballar millor que ningú. Intenta ballar millor que tu mateix.",
                "Si pots parlar, pots cantar. Si pots caminar, pots ballar.",
                "La tècnica és només la base; la passió és el que et fa volar.",
                "El ball és una manera d'arribar a la llibertat.",
                "El teu cos és el teu instrument, *mantenlo afinat.",
                "En l'escenari no hi ha errors, només noves oportunitats d'improvisar.",
                "La vida no consistix a esperar que passe la tempesta, sinó a aprendre a ballar sota la pluja.",
                "Balla com si ningú t'estiguera mirant.",
                "La dansa és el reflex del que el cos convertix en art.",
                "Hi ha dreceres per a la felicitat, i el ball és un d'ells.",
                "Ballar és aconseguir una paraula que no existix. Cantar una cançó de mil generacions. Sentir el significat d'un moment.",
                "Un ballarí balla perquè la seua sang balla en les seues venes.",
                "Ballar és l'única activitat on 'perdre el pas' és un esport de risc, però 'perdre el cap' és l'esperat.",
                "Un gran ballarí no es definix per les seues piruetes, sinó per quantes vegades s'alça després d'una caiguda.",
                "La tècnica és el mapa, però la passió és el destí.",
                "No balles per a impressionar; balla per a expressar el que les paraules no arriben a dir.",
                "L'únic mal ball és aquell que no es va fer per por de fer el ridícul.",
                "Els teus peus són les ferramentes, però el ritme el dicta el teu cor.",
                "Si t'equivoques en el pas, somriu i seguix. En el ball, com en la vida, l'espectacle ha de continuar.",
                "Hi ha moments que no es poden explicar, només es poden ballar.",
                "La dansa és l'únic llenguatge universal que no necessita traducció, sol sentiment.",
                "No deixes que la por a no ser 'perfecte' et robe el plaer de moure't.",
                "Balla per tu, per a tu i malgrat tot.",
                "La vida és una pista de ball: uns miren, uns altres critiquen, però només els que s'atrevixen a eixir a ballar són els que realment viuen."
        ]
    };

    let indiceFraseActual = 0;

    function generarFrase() {
        indiceFraseActual = Math.floor(Math.random() * frasesDanza.es.length);
        actualizarTextoFrase();
    }

    function actualizarTextoFrase() {
        const display = document.getElementById('frase-display');
        const lang = document.getElementById('language-selector').value || 'es';
        display.innerText = `"${frasesDanza[lang][indiceFraseActual]}"`;
    }

    function setLanguage(lang) {
        document.querySelectorAll('[data-i18n]').forEach(el => {
            const key = el.getAttribute('data-i18n');
            if (translations[lang][key]) {
                el.textContent = translations[lang][key];
            }
        });
        localStorage.setItem('preferredLang', lang);
    }

    const langSelector = document.getElementById('language-selector');
    
    langSelector.addEventListener('change', (e) => {
        const lang = e.target.value;
        setLanguage(lang);
        actualizarTextoFrase();
    });

    window.onload = function() {
        generarFrase();
        const savedLang = localStorage.getItem('preferredLang') || 'es';
        langSelector.value = savedLang;
        setLanguage(savedLang);
        actualizarTextoFrase();
    };
</script>
</body>
</html>