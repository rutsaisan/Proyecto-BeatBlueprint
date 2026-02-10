<?php
$host = "172.24.103.203";
$user = "beatblueprint"; // Using the user found in the repo exploration
$pass = "BeatBlueprint123$"; // Using the password found in the repo exploration
$db = "beat_blueprint";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$conexion->set_charset("utf8");
?>