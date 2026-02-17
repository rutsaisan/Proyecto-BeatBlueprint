# Proyecto-BeatBlueprint

Aquí tienes la documentación de Beat Blueprint en formato Markdown, integrando las descripciones detalladas y la estructura técnica del proyecto:

## **🚀 Beat Blueprint: The Coreographic**
Beat Blueprint es una plataforma integral diseñada para digitalizar y optimizar el flujo de trabajo de coreógrafos, directores de academias y bailarines de danza urbana. Centraliza la creación, el aprendizaje y la gestión de contenido en un solo entorno con estética cyber-modern.

## **💎 Pilares del Proyecto**
Canvas (Escenario Virtual): Sistema interactivo 2D para el diseño de formaciones en posiciones.php.

**Wiki Pasos**: Diccionario técnico de movimientos en wiki.php categorizados por nivel y estilo.

**Música**: Gestor de pistas de audio y listas de reproducción en musica.php.

**Vidioteca (Biblioteca)**: Repositorio visual optimizado para referencias y grabaciones en vidioteca.php.

 ## **🛠️Stack Tecnológico**
**Frontend**: HTML5, CSS3 (Tailwind CSS) y JavaScript para interactividad.

**Backend**: PHP para gestión de sesiones, registro y login.

**Base de Datos**: MySQL para persistencia de usuarios, canciones y pasos.

**Diseño**: Glassmorphism con paleta de morados vibrantes y modo oscuro.

 ## **📂Estructura del Repositorio**
Plaintext

└── rutsaisan-proyecto-beatblueprint/
    ├── README.md               # Documentación del proyecto
    ├── feed.php                # Dashboard principal tras el login
    ├── index.php               # Página de inicio / Login
    ├── musica.php              # Módulo de gestión de audio
    ├── posiciones.php          # Escenario virtual (Canvas)
    ├── register.html           # Formulario de registro de usuario
    ├── vidioteca.php           # Galería de vídeos y ensayos
    ├── wiki.php                # Base de datos de pasos de baile
    ├── assets/
    │   └── css/
    │       └── estilo.css      # Estilos personalizados adicionales
    ├── database/
    │   └── db.sql              # Esquema de la base de datos MySQL
    ├── includes/
    │   ├── config.php          # Conexión a la base de datos
    │   └── register.php        # Lógica de validación de registro
    └── php/
        ├── login.php           # Procesamiento de inicio de sesión
        ├── logout.php          # Cierre de sesión de usuario
        ├── register.php        # Inserción de usuarios en la DB
        ├── subir_cancion.php   # Backend para carga de archivos MP3
        └── subir_video.php     # Backend para carga de archivos de vídeo
        
## **📚¿Qué hace cada parte de Beat Blueprint?**
**Index e Inicio de Sesión**: Es la puerta de entrada que valida que solo usuarios registrados accedan a sus recursos, gestionando errores de acceso.

**Registro**: Permite nuevos ingresos validando requisitos de seguridad como la longitud de caracteres en el usuario y contraseña.

**Feed (Dashboard)**: Funciona como centro de control donde el usuario ve su actividad reciente y accede a los pilares de la app.

**Música**: Utiliza un sistema de subida asíncrono (fetch) para guardar archivos en el servidor y registrarlos en la base de datos.

**Wiki Pasos**: Herramienta educativa que centraliza nombres y descripciones de pasos para estandarizar el vocabulario técnico.

**Canvas (Posiciones)**: Proporciona un entorno visual para planificar el uso del espacio y evitar confusiones en el montaje coreográfico.

**Vidioteca**: Actúa como archivo histórico donde los vídeos se asocian al usuario para revisar progresos o referencias.

## **🎨Guía de Estilo Visual**
Para mantener la coherencia, se utilizan los siguientes estándares definidos en el código:

**Color Primario**: #8A4FFF (Morado principal).

**Fondo**: Gradiente radial desde #4a2b69 a #0f0f13.

**Componentes**: Tarjetas tipo Glass-card con backdrop-filter: blur(12px).

*Beat Blueprint — De la idea al escenario, sin perder el ritmo.*
