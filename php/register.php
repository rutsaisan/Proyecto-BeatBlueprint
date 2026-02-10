<?php
session_start();
require_once '../includes/config.php';

// Función para mostrar alerta y volver atrás sin dejar una página en blanco
function mostrarError($mensaje) {
    echo "<script>
            alert('$mensaje');
            window.history.back();
          </script>";
    exit; // Detiene el script aquí
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitización básica
    $nombre_completo = filter_input(INPUT_POST, 'nombre_completo', FILTER_SANITIZE_SPECIAL_CHARS);
    $usuario = filter_input(INPUT_POST, 'usuario', FILTER_SANITIZE_SPECIAL_CHARS);
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena'];

    // 1. Validar campos vacíos
    if (empty($nombre_completo) || empty($usuario) || empty($email) || empty($contrasena)) {
        mostrarError("Por favor, complete todos los campos.");
    }

    // 2. VALIDACIÓN DE LONGITUD (Tus restricciones)
    
    // a. Usuario (Mínimo 5, Máximo 20)
    $len_usuario = strlen($usuario);
    if ($len_usuario < 5 || $len_usuario > 20) {
        mostrarError("Error: El usuario debe tener entre 5 y 20 caracteres.");
    }

    // b. Contraseña (Mínimo 8, Máximo 16)
    $len_pass = strlen($contrasena);
    if ($len_pass < 8 || $len_pass > 16) {
        mostrarError("Error: La contraseña debe tener entre 8 y 16 caracteres.");
    }

    // 3. Verificar duplicados en la base de datos
    $sql_check = "SELECT id_usuario FROM Usuarios WHERE usuario = ? OR email = ?";
    if ($stmt = $conexion->prepare($sql_check)) {
        $stmt->bind_param("ss", $usuario, $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows > 0) {
            $stmt->close();
            mostrarError("El usuario o correo electrónico ya está registrado.");
        }
        $stmt->close();
    }

    // 4. Hashear contraseña e Insertar
    $password_hash = password_hash($contrasena, PASSWORD_DEFAULT);

    $sql_insert = "INSERT INTO Usuarios (usuario, email, contrasena, nombre_completo) VALUES (?, ?, ?, ?)";

    if ($stmt = $conexion->prepare($sql_insert)) {
        $stmt->bind_param("ssss", $usuario, $email, $password_hash, $nombre_completo);

        if ($stmt->execute()) {
            // Éxito: Redirigimos al login con mensaje o directa
            echo "<script>
                    alert('¡Registro exitoso! Ahora puedes iniciar sesión.');
                    window.location.href = '../index.php';
                  </script>";
        } else {
            mostrarError("Error al registrar el usuario en la base de datos.");
        }
        $stmt->close();
    } else {
        mostrarError("Error en la preparación de la consulta: " . $conexion->error);
    }

    $conexion->close();
}
?>