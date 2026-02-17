<?php
session_start();
require_once "../includes/config.php"; 

header('Content-Type: application/json');

if (!isset($_SESSION['id']) || !isset($_POST['id']) || !isset($_POST['tipo'])) {
    echo json_encode(['success' => false, 'error' => 'Datos incompletos']);
    exit;
}

$id = intval($_POST['id']);
$tipo = $_POST['tipo'];
$usuario_id = $_SESSION['id'];

// Definir tablas según el tipo enviado por JS
if ($tipo === 'video') {
    $tabla = "Videos";
    $col_id = "id_video";
    $col_ruta = "video";
} else {
    $tabla = "Canciones";
    $col_id = "id_cancion";
    $col_ruta = "ruta_mp3";
}

// 1. Buscar la ruta del archivo para borrarlo del disco
$stmt = $conexion->prepare("SELECT $col_ruta FROM $tabla WHERE $col_id = ? AND id_usuario = ?");
$stmt->bind_param("ii", $id, $usuario_id);
$stmt->execute();
$resultado = $stmt->get_result();

if ($fila = $resultado->fetch_assoc()) {
    $ruta_db = $fila[$col_ruta];
    // Ajuste de ruta: subir un nivel para salir de /php/ y entrar en la carpeta de archivos
    $ruta_fisica = "../" . $ruta_db; 

    if (file_exists($ruta_fisica)) {
        unlink($ruta_fisica); // Borra el archivo real
    }

    // 2. Borrar de la base de datos
    $stmt_del = $conexion->prepare("DELETE FROM $tabla WHERE $col_id = ? AND id_usuario = ?");
    $stmt_del->bind_param("ii", $id, $usuario_id);
    
    if ($stmt_del->execute()) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'error' => 'Error al borrar registro']);
    }
} else {
    echo json_encode(['success' => false, 'error' => 'No se encontró el recurso']);
}