<?php
//Activamos el almacenamiento en el buffer
ob_start();
session_start();

if (!isset($_SESSION["nombre"])) 
{
  header("Location: login.html");
}
else
{
require 'header.php';

if ($_SESSION['ventas']==1)
{
?>
<!--Contenido-->
      <!-- Content Wrapper. Contains page content -->
      <div class="content-wrapper">        
        <!-- Main content -->
        <section class="content">
            <div class="row">
              <div class="col-md-12">
                  <div class="box">
                    <div class="box-header with-border">
                          <h1 class="box-title">Sistema de Pedidos <button class="btn btn-info" id="btnagregar" onclick="location.reload();"><i class="fa fa-refresh"></i> Actualizar</button></h1>
                        <div class="box-tools pull-right">
                        </div>
                    </div>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        .pedido {
            border: 1px solid #ccc;
            padding: 20px;
            margin: 10px 0;
            background-color: #f9f9f9;
        }
        .pedido h3 {
            margin: 0;
        }
        .pedido li {
            font-size: 17px;
        }
        .pedido button {
            margin-top: 10px;
            font-size: 20px;
        }
    </style>
</head>
<body>

    <div id="contenedor-pedidos"></div>

    <script>
        // Función para cargar los pedidos desde la base de datos
        async function cargarPedidos() {
            try {
                const response = await fetch('../modelos/obtener_pedidos2.php');
                const pedidos = await response.json();

                // Agrupar pedidos por idventa
                const pedidosAgrupados = pedidos.reduce((acc, detalle) => {
                    if (!acc[detalle.idventa]) {
                        acc[detalle.idventa] = { idventa: detalle.idventa, usuario: detalle.usuario_nombre, detalles: [] };
                    }
                    acc[detalle.idventa].detalles.push({
                        articulo: detalle.nombre,
                        cantidad: detalle.cantidad
                    });
                    return acc;
                }, {});

                const pedidosArray = Object.values(pedidosAgrupados);
                const contenedor = document.getElementById('contenedor-pedidos');
                contenedor.innerHTML = ''; // Limpiar el contenedor antes de agregar nuevos pedidos

                pedidosArray.forEach(pedido => {
                    const pedidoHTML = document.createElement('div');
                    pedidoHTML.classList.add('pedido');
                    pedidoHTML.innerHTML = `
                        <h3>Pedido #${pedido.idventa}</h3>
                        <h3>Hecho por: <strong>${pedido.usuario}</strong></h3>
                        <h4>Detalles:</h4>
                        <ul>
                            ${pedido.detalles.map(detalle => 
                                `<li><strong>Artículo:</strong> ${detalle.articulo} - <strong>Cantidad:</strong> ${detalle.cantidad}</li>`
                            ).join('')}
                        </ul>
                        <button class="btn btn-primary" onclick="actualizarEstado(${pedido.idventa}, 'Entregado')">Entregado</button>
                    `;
                    contenedor.appendChild(pedidoHTML);
                });
            } catch (error) {
                console.error('Error al cargar los pedidos:', error);
            }
        }

        // Función para actualizar el estado de cocina de un pedido
        async function actualizarEstado(idventa, estado) {
            try {
                const response = await fetch('../modelos/actualizar_estado.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                    body: `idventa=${idventa}&estado=${estado}`
                });

                const data = await response.json();
                if (data.success) {
                    console.log(`Pedido #${idventa} actualizado a "${estado}"`);
                    cargarPedidos(); // Recargar la lista de pedidos
                } else {
                    alert(data.message);
                }
            } catch (error) {
                console.error('Error al actualizar el estado de cocina:', error);
            }
        }

        // Cargar los pedidos al cargar la página
        document.addEventListener('DOMContentLoaded', cargarPedidos);
        // Cargar pedidos cada 5 segundos
        setInterval(cargarPedidos, 3000);
        cargarPedidos();
    </script>

</body>
</html>

                    <!--Fin centro -->
                  </div><!-- /.box -->
              </div><!-- /.col -->
          </div><!-- /.row -->
      </section><!-- /.content -->

    </div><!-- /.content-wrapper -->
  <!--Fin-Contenido-->
<?php
}
else
{
  require 'noacceso.php';
}

require 'footer.php';
?>
<script type="text/javascript" src="scripts/categoria.js"></script>
<?php 
}
ob_end_flush();
?>

