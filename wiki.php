<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beat Blueprint - Wiki de Pasos</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/js/all.min.js"></script>
    
    <style>
        :root {
            --primary-purple: #8A4FFF;
            --dark-bg: #1a1a2e;
            --card-bg: rgba(255, 255, 255, 0.05);
            --glass-border: rgba(255, 255, 255, 0.1);
            --dark-card: rgba(15, 15, 19, 0.4);
        }

        body {
            font-family: 'Inter', sans-serif;
            background: radial-gradient(circle at 50% -20%, #4a2b69 0%, #0f0f13 60%);
            background-attachment: fixed;
            color: white;
            margin: 0;
            padding-bottom: 50px;
        }

        .glass-card {
            background: var(--card-bg);
            border: 1px solid var(--glass-border);
            backdrop-filter: blur(10px);
            transition: transform 0.3s ease, border-color 0.3s ease;
        }

        /* Contenedor tipo Ventana de Sección */
        .style-section {
            background: var(--dark-card);
            border: 1px solid var(--glass-border);
            border-radius: 3rem;
            padding: 4rem 2rem;
            backdrop-filter: blur(12px);
            box-shadow: 0 20px 50px rgba(0,0,0,0.3);
            margin-bottom: 5rem;
        }

        .section-badge {
            background: linear-gradient(90deg, var(--primary-purple), #FF4F8B);
            padding: 6px 24px;
            border-radius: 99px;
            font-size: 0.8rem;
            font-weight: 800;
            letter-spacing: 0.1em;
            text-transform: uppercase;
        }

        .glass-card:hover {
            border-color: var(--primary-purple);
            transform: translateY(-5px);
        }

        .text-gradient {
            background: linear-gradient(to right, #fff, var(--primary-purple));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        img {
            border-radius: 40px;
        }
    </style>
</head>
<body>

    <div class="p-6">
        <a href="feed.php" class="w-10 h-10 rounded-full glass-card flex items-center justify-center hover:bg-white/10 transition">
            <i class="fas fa-chevron-left text-sm"></i>
        </a>
    </div>

    <header class="text-center mb-20 px-4">
        <h1 class="text-5xl md:text-7xl font-extrabold tracking-tighter uppercase text-gradient italic">
            Wiki de Pasos
        </h1>
        <p class="text-gray-400 mt-4 tracking-widest uppercase text-xs">Domina la técnica de cada paso</p>
    </header>

    <main class="max-w-6xl mx-auto px-6">

        <div class="style-section">
            <div class="flex items-center gap-4 mb-16">
                <span class="section-badge">Hip Hop</span>
                <div class="h-[1px] flex-grow bg-white/10"></div>
            </div>

            <section class="flex flex-col md:flex-row items-center gap-12 group">
                <div class="w-full md:w-1/2 aspect-video rounded-3xl overflow-hidden glass-card">
                    <img src="img/pasos/Bounce.png" alt="Bounce" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    
                    <h2 class="text-4xl font-bold mt-2 mb-4">Bounce</h2>
                    <span class="text-purple-500 font-bold text-sm tracking-widest uppercase">Básico</span>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        Es el movimiento de muelleo (arriba y abajo) flexionando las rodillas. Es la base para mantener el tiempo.
                        Imagina que tienes muelles en las rodillas. Con los pies un poco más abiertos que el ancho de tus hombros, deja caer el peso relajando las rodillas rítmicamente. El cuerpo debe ir hacia abajo (down-bounce). No es un salto, es un peso muerto que rebota.
                    </p><br>
                    <a class="text-xs text-purple-400 font-bold uppercase tracking-wider hover:text-white transition" href="https://youtube.com/shorts/ZgYi0VKDtCM?si=yD5CdUA6_2TACu-R" target="_blank">
                        <i class="fas fa-play-circle mr-1"></i> Ver Tutorial
                    </a>
                </div>
            </section>
            <section class="flex flex-col md:flex-row items-center gap-12 group">
                <div class="w-full md:w-1/2 aspect-video rounded-3xl overflow-hidden glass-card">
                    <img src="img/pasos/Rocking.png" alt="Rocking" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    
                    <h2 class="text-4xl font-bold mt-2 mb-4">Rocking</h2>
                    <span class="text-purple-500 font-bold text-sm tracking-widest uppercase">Básico</span>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        Un balanceo del torso de adelante hacia atrás, similar al movimiento que se hace de forma natural al escuchar música.Imagina que alguien te empuja suavemente el pecho hacia atrás y luego tú recuperas la posición. El movimiento nace del torso y la pelvis, creando una onda constante que te ayuda a fluir entre pasos.
                    </p><br>
                    <a class="text-xs text-purple-400 font-bold uppercase tracking-wider hover:text-white transition" href="https://youtu.be/ee_haw0JyO4?si=bFRocsi-pH0k-fL1">
                        <i class="fas fa-play-circle mr-1"></i> Ver Tutorial
                    </a>
                </div>
            </section>
            <section class="flex flex-col md:flex-row items-center gap-12 group">
                <div class="w-full md:w-1/2 aspect-video rounded-3xl overflow-hidden glass-card">
                    <img src="img/pasos/The Mouse.png" alt="The Mouse" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    
                    <h2 class="text-4xl font-bold mt-2 mb-4">The Mouse</h2>
                    <span class="text-purple-500 font-bold text-sm tracking-widest uppercase">Básico</span>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        Junta un poco los pies. El movimiento consiste en mover los talones hacia afuera y luego las puntas hacia afuera de forma muy rápida y pequeña, como si tus pies "corrieran" por el suelo de lado a lado sin despegarse apenas de él. Se ve muy nervioso y ágil.
                    </p><br>
                    <a class="text-xs text-purple-400 font-bold uppercase tracking-wider hover:text-white transition" href="https://youtu.be/m-qE7irNMFY">
                        <i class="fas fa-play-circle mr-1"></i> Ver Tutorial
                    </a>
                </div>
            </section>
            <section class="flex flex-col md:flex-row items-center gap-12 group">
                <div class="w-full md:w-1/2 aspect-video rounded-3xl overflow-hidden glass-card">
                    <img src="img/pasos/Running Man.png" alt="Running Man" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    
                    <h2 class="text-4xl font-bold mt-2 mb-4">Running Man</h2>
                    <span class="text-purple-500 font-bold text-sm tracking-widest uppercase">Básico</span>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        La clave es el deslizamiento. Mientras una rodilla sube, el pie que está en el suelo se desliza hacia atrás. Al bajar el pie que estaba arriba, el otro vuelve al centro.<br>
                        1.- Sube una rodilla a la altura de la cadera.<br>

                        2.- Mientras bajas ese pie al suelo, el otro pie (el que estaba apoyado) debe deslizarse hacia atrás.<br>

                        3.- Ahora sube la otra rodilla y repite. Parece que corres, pero te quedas en el sitio.<br>
                    </p><br>
                    <a class="text-xs text-purple-400 font-bold uppercase tracking-wider hover:text-white transition" href="https://youtu.be/Qq5snba5Ji4?si=XB_hStWtgkbuEQV1">
                        <i class="fas fa-play-circle mr-1"></i> Ver Tutorial
                    </a>
                </div>
            </section>
            <section class="flex flex-col md:flex-row items-center gap-12 group">
                <div class="w-full md:w-1/2 aspect-video rounded-3xl overflow-hidden glass-card">
                    <img src="img/pasos/Criss Cross.png" alt="Criss Cross" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    
                    <h2 class="text-4xl font-bold mt-2 mb-4">Criss Cross</h2>
                    <span class="text-purple-500 font-bold text-sm tracking-widest uppercase">Básico</span>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        es un salto rítmico que consiste en abrir y cerrar las piernas alternativamente: en el primer pulso saltas abriendo los pies hacia los lados y, en el segundo, saltas de nuevo cruzando un pie por delante del otro. Es fundamental mantener el peso en las puntas de los pies y coordinar el cruce (derecha delante, abrir, izquierda delante) para crear un efecto visual de tijera constante.
                    </p><br>
                    <a class="text-xs text-purple-400 font-bold uppercase tracking-wider hover:text-white transition" href="https://youtube.com/shorts/qR_itaRdwMc?si=EZSK49656_5IGdZn">
                        <i class="fas fa-play-circle mr-1"></i> Ver Tutorial
                    </a>
                </div>
            </section>
        </div>

        <div class="style-section">
            <div class="flex items-center gap-4 mb-16">
                <span class="section-badge" style="background: linear-gradient(90deg, #4f8aff, #8A4FFF);">Popping</span>
                <div class="h-[1px] flex-grow bg-white/10"></div>
            </div>

            <section class="flex flex-col md:flex-row-reverse items-center gap-12 group">
                <div class="w-full md:w-1/2 aspect-video rounded-3xl overflow-hidden glass-card">
                    <img src="img/pasos/moonwalk.jpg" alt="Moonwalk" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    <span class="text-purple-500 font-bold text-sm tracking-widest uppercase">Intermedio</span>
                    <h2 class="text-4xl font-bold mt-2 mb-4">Moonwalk</h2>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        Técnica de deslizamiento que crea la ilusión óptica de que el bailarín se mueve hacia adelante mientras camina hacia atrás. Popularizado por Michael Jackson, requiere un control preciso del peso en los metatarsos.
                    </p>
                </div>
            </section>
        </div>

        <div class="style-section">
            <div class="flex items-center gap-4 mb-16">
                <span class="section-badge" style="background: linear-gradient(90deg, #F97316, #E11D48);">Breaking</span>
                <div class="h-[1px] flex-grow bg-white/10"></div>
            </div>

            <section class="flex flex-col md:flex-row items-center gap-12 group">
                <div class="w-full md:w-1/2 aspect-video rounded-3xl overflow-hidden glass-card">
                    <img src="img/pasos/sixstep.jpg" alt="Six Step" class="w-full h-full object-cover opacity-80 group-hover:opacity-100 transition duration-500">
                </div>
                <div class="w-full md:w-1/2">
                    <span class="text-purple-500 font-bold text-sm tracking-widest uppercase">Footwork</span>
                    <h2 class="text-4xl font-bold mt-2 mb-4">Six Step</h2>
                    <p class="text-gray-400 leading-relaxed text-lg">
                        El movimiento fundamental del footwork en el Breaking. Consiste en una secuencia de seis pasos circulares en el suelo, utilizando las manos como apoyo y las piernas para crear una rotación fluida alrededor del eje central.
                    </p>
                </div>
            </section>
        </div>

    </main>

</body>
</html>