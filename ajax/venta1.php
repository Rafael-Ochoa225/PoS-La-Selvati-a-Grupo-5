<?php 
if (strlen(session_id()) < 1) 
  session_start();

require_once "../modelos/Venta.php";

$venta=new Venta();

$idventa=isset($_POST["idventa"])? limpiarCadena($_POST["idventa"]):"";
$idcliente=isset($_POST["idcliente"])? limpiarCadena($_POST["idcliente"]):"";
$idusuario=$_SESSION["idusuario"];
$tipo_comprobante=isset($_POST["tipo_comprobante"])? limpiarCadena($_POST["tipo_comprobante"]):"";
$serie_comprobante=isset($_POST["serie_comprobante"])? limpiarCadena($_POST["serie_comprobante"]):"";
$num_comprobante=isset($_POST["num_comprobante"])? limpiarCadena($_POST["num_comprobante"]):"";
$fecha_hora=isset($_POST["fecha_hora"])? limpiarCadena($_POST["fecha_hora"]):"";
$impuesto=isset($_POST["impuesto"])? limpiarCadena($_POST["impuesto"]):"";
$total_venta=isset($_POST["total_venta"])? limpiarCadena($_POST["total_venta"]):"";

switch ($_GET["op"]){
	case 'guardaryeditar':
		if (empty($idventa)){
			$rspta=$venta->insertar($idcliente,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_venta,$_POST["idarticulo"],$_POST["cantidad"],$_POST["precio_venta"],$_POST["descuento"]);
			echo $rspta ? "Venta registrada" : "No se pudieron registrar todos los datos de la venta";
		}
		else {
			$rspta=$venta->editar($idventa,$idcliente,$idusuario,$tipo_comprobante,$serie_comprobante,$num_comprobante,$fecha_hora,$impuesto,$total_venta,$_POST["idarticulo"],$_POST["cantidad"],$_POST["precio_venta"],$_POST["descuento"]);
			echo $rspta ? "Venta editada" : "No se pudieron editar todos los datos de la venta";
		}
	break;

	case 'anular':
		$rspta=$venta->anular($idventa);
 		echo $rspta ? "Venta anulada" : "Venta no se puede anular";
	break;

	case 'pagar':
		$rspta=$venta->pagar($idventa);
 		echo $rspta ? "Venta pagada" : "Venta no se puede pagar";
	break;

	case 'mostrar':
		$rspta=$venta->mostrar($idventa);
 		//Codificar el resultado utilizando json
 		echo json_encode($rspta);
	break;

	case 'listarDetalle':
		//Recibimos el idingreso
		$id=$_GET['id'];

		$rspta = $venta->listarDetalle($id);
		$total=0;
		echo '<thead style="background-color:#A9D0F5">
                                    <th>Opciones</th>
                                    <th>Artículo</th>
                                    <th>Cantidad</th>
                                    <th>Precio Venta</th>
                                    <th>Descuento</th>
                                    <th>Subtotal</th>
                                </thead>';

		while ($reg = $rspta->fetch_object())
				{
					echo '<tr class="filas"><td></td><td><input type="hidden" name="idarticulo[]" value="'.$reg->idarticulo.'">'.$reg->nombre.'</td>
											<td><input type="number" name="cantidad[]" id="cantidad[]" min="'.$reg->cantidad.'" value="'.$reg->cantidad.'"></td>
											<td><input type="number" name="precio_venta[]" min="'.$reg->precio_venta.'" max="'.$reg->precio_venta.'" id="precio_venta[]" value="'.$reg->precio_venta.'"></td>
											<td><input type="number" name="descuento[]" min="0" max="0" value="'.$reg->descuento.'"></td>
											<td><span name="subtotal" id="subtotal">'.$reg->subtotal.'</span></td>
							</tr>';
					$total=$total+($reg->precio_venta*$reg->cantidad-$reg->descuento);
				}
		echo '<tfoot>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th></th>
                                    <th>TOTAL</th>
                                    <th><h4 id="total">S/.'.$total.'</h4><input type="hidden" name="total_venta" id="total_venta"></th> 
                                </tfoot>';
	break;

	case 'listar':
		$rspta=$venta->listar();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$url1='../reportes/exTicket.php?id=';
 			$url2='../reportes/exBoleta.php?id=';
 			$color="";
 			if ($reg->estado=='Aceptado') {
 				$color="green";
 			}elseif ($reg->estado=='Pagado') {
 				$color="blue";
 			}else{
 				$color="red";
 			}
 			$data[]=array(
 				"0"=>($reg->estado=='Aceptado')?'<button class="btn btn-warning" onclick="mostrar('.$reg->idventa.')"><i class="fa fa-eye"></i></button>'.
 					'<a target="_blank" href="'.$url1.$reg->idventa.'"> <button class="btn btn-info"><i class="fa fa-file"></i></button></a>'.
 					'<a target="_blank" href="'.$url2.$reg->idventa.'"> <button class="btn btn-primary"><i class="fa fa-file"></i></button></a>'.
 					' <button class="btn btn-success" onclick="pagar('.$reg->idventa.')"><i class="fa fa-check"></i></button>':
 					'',
 				"1"=>$reg->fecha,
 				"2"=>$reg->cliente,
 				"3"=>$reg->usuario,
 				"4"=>$reg->tipo_comprobante,
 				"5"=>$reg->serie_comprobante.'-'.$reg->num_comprobante,
 				"6"=>$reg->total_venta,
 				"7"=>'<span class="label bg-'.$color.'">'.$reg->estado.'</span>'
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);

	break;

	case 'selectCliente':
		require_once "../modelos/Persona.php";
		$persona = new Persona();

		$rspta = $persona->listarC();

		while ($reg = $rspta->fetch_object())
				{
				echo '<option value=' . $reg->idpersona . '>' . $reg->nombre . '</option>';
				}
	break;

	case 'listarArticulosVenta':
		require_once "../modelos/Articulo.php";
		$articulo=new Articulo();

		$rspta=$articulo->listarActivosVenta();
 		//Vamos a declarar un array
 		$data= Array();

 		while ($reg=$rspta->fetch_object()){
 			$data[]=array(
 				"0"=>'<button class="btn btn-warning" onclick="agregarDetalle('.$reg->idarticulo.',\''.$reg->nombre.'\',\''.$reg->stock.'\',\''.$reg->precio_venta.'\')"><span class="fa fa-plus"></span></button>',
 				"1"=>$reg->nombre,
 				"2"=>$reg->categoria,
 				"3"=>$reg->codigo,
 				"4"=>$reg->stock,
 				"5"=>$reg->precio_venta,
 				"6"=>"<img src='../files/articulos/".$reg->imagen."' height='100px' width='100px' >"
 				);
 		}
 		$results = array(
 			"sEcho"=>1, //Información para el datatables
 			"iTotalRecords"=>count($data), //enviamos el total registros al datatable
 			"iTotalDisplayRecords"=>count($data), //enviamos el total registros a visualizar
 			"aaData"=>$data);
 		echo json_encode($results);
	break;
}
?>