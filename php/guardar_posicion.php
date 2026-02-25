<?php
session_start();
require_once '../includes/config.php';

if (!isset($_SESSION['id'])) {
    echo json_encode(['success' => false, 'error' => 'No session']);
    exit;
}

$input = file_get_contents('php://input');
$data = json_decode($input, true);

if ($data) {
    $nombre = $data['esquema'];
    $tipo = $data['tipo'];
    $usuario_id = $_SESSION['id'];
    $posiciones = $data['posiciones'];

    // 1. Insertar la formación
    $stmt = $conexion->prepare("INSERT INTO Formaciones (nombre_posicion, tipo_formacion, id_usuario) VALUES (?, ?, ?)");
    $stmt->bind_param("ssi", $nombre, $tipo, $usuario_id);
    
    if ($stmt->execute()) {
        $id_formacion = $stmt->insert_id;
        $stmt->close();

        // 2. Insertar cada bailarín
        $stmt_pos = $conexion->prepare("INSERT INTO Posiciones_Bailarines (id_formacion, nombre_bailarin, coord_x, coord_y) VALUES (?, ?, ?, ?)");
        
        foreach ($posiciones as $p) {
            $stmt_pos->bind_param("isss", $id_formacion, $p['nombre'], $p['x'], $p['y']);
            $stmt_pos->execute();
        }
        $stmt_pos->close();

        echo json_encode(['success' => true, 'id' => $id_formacion]);
    } else {
        echo json_encode(['success' => false]);
    }
}
?>