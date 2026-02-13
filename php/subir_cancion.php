<?php
// Desactivar cualquier salida de texto accidental (espacios, warnings) para no romper el JSON
ob_start(); 
error_reporting(E_ALL);
ini_set('display_errors', 0);
header('Content-Type: application/json');

session_start();
require_once '../includes/config.php'; 

$response = ["success" => false, "error" => "Error desconocido"];

try {
    // 1. Verificar sesión
    if (!isset($_SESSION['id'])) {
        throw new Exception("Sesión no iniciada. Por favor, vuelve a loguearte.");
    }

    // 2. Verificar si PHP detectó errores en la subida (como el tamaño)
    if ($_FILES['audio']['error'] !== UPLOAD_ERR_OK) {
        $errores_php = [
            1 => "El archivo excede el límite de PHP (upload_max_filesize).",
            2 => "El archivo excede el límite del formulario.",
            3 => "Subida parcial.",
            4 => "No se subió ningún archivo.",
            6 => "Falta carpeta temporal.",
            7 => "Error al escribir en disco.",
        ];
        throw new Exception($errores_php[$_FILES['audio']['error']] ?? "Error de subida código: " . $_FILES['audio']['error']);
    }

    $usuario_id = $_SESSION['id'];
    $nombre_original = $_FILES['audio']['name'];
    $artista = $_POST['artista'] ?? 'Desconocido';
    $id_estilo = isset($_POST['id_estilo_baile']) ? intval($_POST['id_estilo_baile']) : 1;

    // 3. Definir ruta (Saliendo de /php/ para entrar en /uploads/)
    $target_dir = "../uploads/";
    
    // Intentar crear la carpeta si no existe
    if (!file_exists($target_dir)) {
        if (!mkdir($target_dir, 0777, true)) {
            throw new Exception("El servidor no tiene permisos para crear la carpeta 'uploads'. Créala manualmente.");
        }
    }

    // Generar nombre único para evitar que canciones con el mismo nombre se sobreescriban
    $file_extension = pathinfo($nombre_original, PATHINFO_EXTENSION);
    $new_file_name = time() . "_" . uniqid() . "." . $file_extension;
    $target_file = $target_dir . $new_file_name;
    $ruta_db = "uploads/" . $new_file_name; 

    // 4. Mover el archivo
    if (move_uploaded_file($_FILES['audio']['tmp_name'], $target_file)) {
        $sql = "INSERT INTO Canciones (ruta_mp3, nombre_cancion, artista, id_estilo_baile, id_usuario) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conexion->prepare($sql);
        if (!$stmt) {
            throw new Exception("Error preparando la BD: " . $conexion->error);
        }
        
        $stmt->bind_param("sssii", $ruta_db, $nombre_original, $artista, $id_estilo, $usuario_id);

        if ($stmt->execute()) {
            $response = [
                "success" => true,
                "nombre" => $nombre_original,
                "ruta" => $ruta_db
            ];
        } else {
            throw new Exception("Error al guardar en la base de datos.");
        }
    } else {
        throw new Exception("No se pudo mover el archivo. Verifica que la carpeta 'uploads' tenga permisos de escritura (777).");
    }

} catch (Exception $e) {
    $response["error"] = $e->getMessage();
}

// Limpiar buffer y enviar JSON
ob_end_clean();
echo json_encode($response);