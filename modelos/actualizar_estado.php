<?php
require "../config/Conexion.php";
// Asegúrate de que el encabezado sea JSON
header('Content-Type: application/json');

// Incluir el archivo de conexión a la base de datos
$conexion = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);

if ($conexion->connect_error) {
    die("Error de conexión: " . $conexion->connect_error);
}

$id_venta = $_POST['idventa'];
$estado_cocina = $_POST['estado'];

$query = "
    UPDATE venta 
    SET estado_cocina = '$estado_cocina' 
    WHERE idventa = $id_venta
";

if ($conexion->query($query) === TRUE) {
    echo json_encode(['success' => true]);
} else {
    echo json_encode(['success' => false, 'error' => $conexion->error]);
}

$conexion->close();
?>
