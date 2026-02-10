<?php
session_start();
require_once '../includes/config.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = filter_input(INPUT_POST, 'email', FILTER_SANITIZE_EMAIL);
    $contrasena = $_POST['contrasena'];

    // Consulta con los nombres exactos de la  base de datos
    $sql = "SELECT id_usuario, contrasena, nombre_completo FROM Usuarios WHERE email = ?";

    if ($stmt = $conexion->prepare($sql)) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $stmt->store_result();

        if ($stmt->num_rows == 1) {
            $stmt->bind_result($id_usuario, $pass_bd, $nombre_completo);
            $stmt->fetch();

            // --- VERIFICACIÓN DE CONTRASEÑA ---
            // 1. Verificamos si es un Hash normal (usuarios registrados web)
            if (password_verify($contrasena, $pass_bd) || ( $contrasena === $pass_bd)) {
                
                $_SESSION['loggedin'] = true;
                $_SESSION['id'] = $id_usuario;
                $_SESSION['nombre'] = $nombre_completo;

            } else {
                header("Location: ../index.php?error=1"); // Contraseña mal
                exit;
            }
        } else {
            header("Location: ../index.php?error=2"); // Usuario no existe
            exit;
        }
        $stmt->close();
    }
    $conexion->close();
}
?>