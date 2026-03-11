<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beat Blueprint</title>
    <link href="https://fonts.googleapis.com/css2?family=Quicksand:wght@400;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/estilo.css">
    <style>
        .error-message {
            color: #ff4d4d;
            font-family: 'Quicksand', sans-serif;
            font-weight: 600;
            font-size: 0.9em;
            margin-bottom: 15px;
            text-align: center;
        }

        /* --- CSS IDIOMA --- */
        .glass-radio-group {
            --bg: rgba(255, 255, 255, 0.06);
            --text: #e5e5e5;
            display: flex;
            position: fixed; 
            top: 20px;       
            right: 20px;     
            background: var(--bg);
            border-radius: 1rem;
            backdrop-filter: blur(12px);
            box-shadow:
                inset 1px 1px 4px rgba(255, 255, 255, 0.2),
                inset -1px -1px 6px rgba(0, 0, 0, 0.3),
                0 4px 12px rgba(0, 0, 0, 0.15);
            overflow: hidden;
            width: fit-content;
            z-index: 1000;
        }

        .glass-radio-group input { display: none; }

        .glass-radio-group label {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            min-width: 90px;
            font-size: 14px;
            padding: 0.8rem 1.2rem;
            cursor: pointer;
            font-weight: 600;
            color: var(--text);
            position: relative;
            z-index: 2;
            transition: color 0.3s ease-in-out;
        }

        .glass-glider {
            position: absolute;
            top: 0;
            left: 0;
            bottom: 0;
            width: calc(100% / 3);
            border-radius: 1rem;
            z-index: 1;
            transition: transform 0.5s cubic-bezier(0.37, 1.95, 0.66, 0.56),
                        background 0.4s ease-in-out;
        }

        #glass-ES:checked ~ .glass-glider { transform: translateX(0%); background: linear-gradient(135deg, #ffd70055, #ffcc00); }
        #glass-VAL:checked ~ .glass-glider { transform: translateX(100%); background: linear-gradient(135deg, #ff4d4d55, #ff0000); }
        #glass-EN:checked ~ .glass-glider { transform: translateX(200%); background: linear-gradient(135deg, #d0e7ff55, #a0d8ff); }

        .glass-radio-group input:checked + label { color: #fff; }
    </style>
</head>

<body>
    
    <div class="glass-radio-group">
        <input type="radio" name="idioma" id="glass-ES" checked onchange="changeLanguage('es')" />
        <label for="glass-ES">Español</label>

        <input type="radio" name="idioma" id="glass-VAL" onchange="changeLanguage('val')" />
        <label for="glass-VAL">Valencià</label>

        <input type="radio" name="idioma" id="glass-EN" onchange="changeLanguage('en')" />
        <label for="glass-EN">English</label>

        <div class="glass-glider"></div>
    </div>

    <div class="main-container">
        <div class="logo-container">
            <img src="logo.png" alt="Logo" class="logo">
        </div>
        <div class="title-container">
            <h1 data-key="title">Beat Blueprint</h1>
        </div>
        
        <form action="php/login.php" method="POST" class="login-form">
            <input type="email" name="email" id="input-email" placeholder="Correo..." required>
            <input type="password" name="contrasena" id="input-pass" placeholder="Contraseña..." required>

            <?php if (isset($_GET['error'])): ?>
                <p class="error-message" id="error-text" data-error-type="<?php echo $_GET['error']; ?>">
                    <?php 
                    if($_GET['error'] == '1') echo "Contraseña incorrecta, vuelve a intentarlo";
                    else if($_GET['error'] == '2') echo "El correo no existe o faltan datos";
                    ?>
                </p>
            <?php endif; ?>

            <button type="submit" data-key="login-btn">Iniciar sesión</button>
        </form>

        <div class="footer-link">
            <a href="register.html" data-key="create-acc">Crear cuenta</a>
        </div>
    </div>

    <script>
        const translations = {
            'es': {
                'title': 'Beat Blueprint',
                'email-ph': 'Correo...',
                'pass-ph': 'Contraseña...',
                'login-btn': 'Iniciar sesión',
                'create-acc': 'Crear cuenta',
                'err-1': 'Contraseña incorrecta, vuelve a intentarlo',
                'err-2': 'El correo no existe o faltan datos'
            },
            'en': {
                'title': 'Beat Blueprint',
                'email-ph': 'Email...',
                'pass-ph': 'Password...',
                'login-btn': 'Log In',
                'create-acc': 'Create account',
                'err-1': 'Incorrect password, please try again',
                'err-2': 'Email does not exist or data missing'
            },
            'val': {
                'title': 'Beat Blueprint',
                'email-ph': 'Correu...',
                'pass-ph': 'Contrasenya...',
                'login-btn': 'Iniciar sessió',
                'create-acc': 'Crear compte',
                'err-1': 'Contrasenya incorrecta, torna a intentar-ho',
                'err-2': 'El correu no existeix o falten dades'
            }
        };

        function changeLanguage(lang) {
            // 1. Textos con data-key
            document.querySelectorAll('[data-key]').forEach(element => {
                const key = element.getAttribute('data-key');
                if (translations[lang][key]) {
                    element.textContent = translations[lang][key];
                }
            });

            // 2. Placeholders de los inputs
            const inputEmail = document.getElementById('input-email');
            const inputPass = document.getElementById('input-pass');
            if(inputEmail) inputEmail.placeholder = translations[lang]['email-ph'];
            if(inputPass) inputPass.placeholder = translations[lang]['pass-ph'];

            // 3. Traducción de errores (si existen en pantalla)
            const errorElement = document.getElementById('error-text');
            if (errorElement) {
                const type = errorElement.getAttribute('data-error-type');
                errorElement.textContent = translations[lang]['err-' + type];
            }
            
            localStorage.setItem('selectedLanguage', lang);
        }

        window.onload = () => {
            const savedLang = localStorage.getItem('selectedLanguage') || 'es';
            document.getElementById(`glass-${savedLang.toUpperCase()}`).checked = true;
            changeLanguage(savedLang);
        };
    </script>
</body>
</html>