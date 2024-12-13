<?php
//Activamos el almacenamiento en el buffer
ob_start();
if (strlen(session_id()) < 1) 
  session_start();

if (!isset($_SESSION["nombre"]))
{
  echo 'Debe ingresar al sistema correctamente para visualizar el reporte';
}
else
{
if ($_SESSION['ventas']==1)
{
?>
<?php

	# Incluyendo librerias necesarias #
    require "./code128.php";

    //Incluímos la clase Venta
    require_once "../modelos/Venta.php";
    //Instanaciamos a la clase con el objeto venta
    $venta = new Venta();
    //En el objeto $rspta Obtenemos los valores devueltos del método ventacabecera del modelo
    $rspta = $venta->ventacabecera($_GET["id"]);
    //Recorremos todos los valores obtenidos
    $reg = $rspta->fetch_object();

    $pdf = new PDF_Code128('P','mm',array(80,258));
    $pdf->SetMargins(4,10,4);
    $pdf->AddPage();
    
    # Encabezado y datos de la empresa #
    $pdf->SetFont('Arial','B',10);
    $pdf->SetTextColor(0,0,0);
    $pdf->MultiCell(0,5,utf8_decode(strtoupper("La Selvatina")),0,'C',false);
    $pdf->SetFont('Arial','',9);
    $pdf->MultiCell(0,5,utf8_decode("RUC: 10750160838"),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Direccion: Calle Teniente Cesar López 116"),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Teléfono: "),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Email: selvatina19@gmail.com"),0,'C',false);

    $pdf->Ln(1);
    $pdf->Cell(0,5,utf8_decode("------------------------------------------------------"),0,0,'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0,5,utf8_decode("Fecha: ".$reg->fecha),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Trabajador: ".$reg->usuario),0,'C',false);
    $pdf->SetFont('Arial','B',10);
    $pdf->MultiCell(0,5,utf8_decode(strtoupper($reg->tipo_comprobante." Electronica: ".$reg->serie_comprobante." - ".$reg->num_comprobante)),0,'C',false);
    $pdf->SetFont('Arial','',9);

    $pdf->Ln(1);
    $pdf->Cell(0,5,utf8_decode("------------------------------------------------------"),0,0,'C');
    $pdf->Ln(5);

    $pdf->MultiCell(0,5,utf8_decode("Cliente: ".$reg->cliente),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Documento: ".$reg->tipo_documento.": ".$reg->num_documento),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Teléfono: ".$reg->telefono),0,'C',false);
    $pdf->MultiCell(0,5,utf8_decode("Dirección: ".$reg->direccion),0,'C',false);

    $pdf->Ln(1);
    $pdf->Cell(0,5,utf8_decode("-------------------------------------------------------------------"),0,0,'C');
    $pdf->Ln(3);

    # Tabla de productos #
    $pdf->Cell(10,5,utf8_decode("Cant."),0,0,'C');
    $pdf->Cell(19,5,utf8_decode("Precio"),0,0,'C');
    $pdf->Cell(28,5,utf8_decode("Producto"),0,0,'C');

    $pdf->Ln(3);
    $pdf->Cell(72,5,utf8_decode("-------------------------------------------------------------------"),0,0,'C');
    $pdf->Ln(3);



    /*----------  Detalles de la tabla  ----------*/
    $rsptad = $venta->ventadetalle($_GET["id"]);
    $cantidad=0;
    while ($regd = $rsptad->fetch_object()) {
        //$pdf->MultiCell(0,4,utf8_decode($regd->articulo),0,'C',false);
        $pdf->Cell(10,4,utf8_decode($regd->cantidad),0,0,'C');
        $pdf->Cell(19,4,utf8_decode($regd->subtotal),0,0,'C');
        $pdf->Cell(28,4,utf8_decode($regd->articulo),0,0,'C');
        $pdf->MultiCell(0,4,utf8_decode(""),0,'C',false);
        $cantidad+=$regd->cantidad;
    }
    $pdf->Ln(4);
    $pdf->Ln(7);
    /*----------  Fin Detalles de la tabla  ----------*/



    $pdf->Cell(72,5,utf8_decode("-------------------------------------------------------------------"),0,0,'C');

        $pdf->Ln(5);

    # Impuestos & totales #
    $pdf->Cell(18,5,utf8_decode(""),0,0,'C');
    $pdf->Cell(22,5,utf8_decode("TOTAL"),0,0,'C');
    $pdf->Cell(32,5,utf8_decode($reg->total_venta),0,0,'C');

    $pdf->Ln(5);

    $pdf->Cell(72,5,utf8_decode("-------------------------------------------------------------------"),0,0,'C');

    $pdf->Ln(10);

    $pdf->MultiCell(0,5,utf8_decode("*** Para poder realizar un reclamo o devolución debe de presentar este ticket ***"),0,'C',false);

    $pdf->SetFont('Arial','B',9);
    $pdf->Cell(0,7,utf8_decode("Gracias por su compra"),'',0,'C');

    $pdf->Ln(9);

    # Codigo de barras #
    //$pdf->Code128(5,$pdf->GetY(),"COD000001V0001",70,20);
    //$pdf->SetXY(0,$pdf->GetY()+21);
    //$pdf->SetFont('Arial','',14);
    //$pdf->MultiCell(0,5,utf8_decode("COD000001V0001"),0,'C',false);
    
    # Nombre del archivo PDF #
    $pdf->Output("I","BoletaElectronica.pdf",true);

?>
<?php 
}
else
{
  echo 'No tiene permiso para visualizar el reporte';
}

}
ob_end_flush();
?>