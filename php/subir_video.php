<?php
// Limpiar cualquier salida previa que pueda ensuciar el JSON
ob_start();
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');
session_start();

require_once '../includes/config.php';

$response = ["success" => false, "error" => "Error desconocido"];

try {
    if (!isset($_SESSION['id'])) {
        throw new Exception("Sesión expirada. Por favor, inicia sesión de nuevo.");
    }

    // Comprobar si hay errores en la subida de archivos de PHP
    if ($_FILES['video']['error'] !== UPLOAD_ERR_OK) {
        $php_errors = [
            1 => "El archivo excede el límite 'upload_max_filesize' en php.ini",
            2 => "El archivo excede el límite definido en el formulario HTML",
            3 => "El archivo se subió parcialmente",
            4 => "No se subió ningún archivo",
            6 => "Falta la carpeta temporal",
            7 => "Error al escribir en el disco",
            8 => "Una extensión de PHP detuvo la subida"
        ];
        $errorCode = $_FILES['video']['error'];
        throw new Exception($php_errors[$errorCode] ?? "Error de subida código: $errorCode");
    }

    $usuario_id = $_SESSION['id'];
    $id_estilo = $_POST['id_estilo_baile'] ?? 1;
    $nombre_video = $_FILES['video']['name'];

    $target_dir = "../uploads/videos/";
    if (!file_exists($target_dir)) {
        mkdir($target_dir, 0777, true);
    }

    $extension = pathinfo($nombre_video, PATHINFO_EXTENSION);
    $nuevo_nombre = time() . "_" . uniqid() . "." . $extension;
    $ruta_final = $target_dir . $nuevo_nombre;
    $ruta_db = "uploads/videos/" . $nuevo_nombre;

    if (move_uploaded_file($_FILES['video']['tmp_name'], $ruta_final)) {
        // Usar los nombres de tabla y columnas de tu db.sql
        $sql = "INSERT INTO Videos (video, descripcion, id_estilo_baile, id_usuario) VALUES (?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        $desc = "Grabación: " . $nombre_video;
        $stmt->bind_param("ssii", $ruta_db, $desc, $id_estilo, $usuario_id);

        if ($stmt->execute()) {
            $response = ["success" => true, "nombre" => $nombre_video, "ruta" => $ruta_db];
        } else {
            throw new Exception("Error en Base de Datos: " . $conexion->error);
        }
    } else {
        throw new Exception("No se pudo mover el archivo. Revisa los permisos de la carpeta 'uploads/videos/'");
    }

} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

// Borrar cualquier buffer de salida (warnings de PHP) y enviar solo el JSON
ob_end_clean();
echo json_encode($response);