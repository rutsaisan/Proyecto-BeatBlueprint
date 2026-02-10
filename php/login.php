<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Limpiamos el email para evitar caracteres extraños
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena'];

    // 1. Buscamos el usuario en la base de datos
    $sql = "SELECT id_usuario, contrasena, nombre_completo FROM Usuarios WHERE email = ?";

    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        // 2. Si encontramos 1 usuario con ese email...
        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id_usuario, $pass_bd, $nombre_completo);
            $stmt->fetch();

            // 3. Verificamos la contraseña (Hash o Texto plano)
            if (password_verify($contrasena, $pass_bd) || ($contrasena === $pass_bd)) {
                
                // --- INICIO DE SESIÓN EXITOSO ---
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id_usuario;
                $_SESSION['nombre'] = $nombre_completo;

                // AQUÍ ESTÁ EL CAMBIO: Redirige a feed.php (que está en la carpeta anterior)
                header("Location: ../feed.php");
                exit; // Importante para detener el script aquí

            } else {
                // Contraseña incorrecta -> Volver al index con error 1
                header("Location: ../index.php?error=1");
                exit;
            }
        } else {
            // Usuario no encontrado -> Volver al index con error 2
            header("Location: ../index.php?error=2");
            exit;
        }
        $stmt->close();
    }
    $conexion->close();
}
?>