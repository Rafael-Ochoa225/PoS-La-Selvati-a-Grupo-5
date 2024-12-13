<?php
require "../config/Conexion.php";
// Asegúrate de que el encabezado sea JSON
header('Content-Type: application/json');

try {
    // Conexión a la base de datos
    $conexion = new mysqli(DB_HOST,DB_USERNAME,DB_PASSWORD,DB_NAME);

    // Verifica errores de conexión
    if ($conexion->connect_error) {
        throw new Exception("Error de conexión: " . $conexion->connect_error);
    }

    // Consulta SQL
    $query = "SELECT dv.iddetalle_venta, dv.idventa, dv.idarticulo, a.nombre, dv.cantidad 
              FROM detalle_venta dv 
              INNER JOIN articulo a ON dv.idarticulo = a.idarticulo 
              INNER JOIN venta v ON dv.idventa = v.idventa
              WHERE v.estado_cocina = 'pendiente'
              AND v.estado != 'Anulado'";
    $resultado = $conexion->query($query);

    // Verifica errores en la consulta
    if (!$resultado) {
        throw new Exception("Error en la consulta SQL: " . $conexion->error);
    }

    // Construye el JSON con los datos
    $pedidos = [];
    while ($fila = $resultado->fetch_assoc()) {
        $pedidos[] = $fila;
    }

    // Envía la respuesta JSON
    echo json_encode($pedidos);
} catch (Exception $e) {
    // En caso de error, devuelve un JSON con el mensaje
    echo json_encode(['error' => $e->getMessage()]);
}
?>
