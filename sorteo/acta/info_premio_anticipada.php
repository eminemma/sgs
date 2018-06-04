<?php
session_start();

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");

include_once dirname(__FILE__) . '/../../db.php';
conectar_db();

//$db->debug= true;

$semana = 1;
if (isset($_REQUEST['semana']) && !empty($_REQUEST['semana'])) {
    $semana = $_REQUEST['semana'];
}
$orden = 1;
if (isset($_REQUEST['orden']) && !empty($_REQUEST['orden'])) {
    $orden = $_REQUEST['orden'];
}
try {
    $rsgordo = sql("
	SELECT
    TAG.ID_JUEGO, TAG.SORTEO, TAG.SEMANA, TAG.BILLETE, TAG.FRACCION, TAG.AGENCIA, TAG.LOCALIDAD, TAG.NOMBRE,
    TA.ID_JUEGO,TA.SORTEO,TA.SEMANA,TA.PREMIO,TA.ID_JEFE
    ,TA.ID_ESCRIBANO,TA.PRESCRIPCION,TA.PROX_SORTEO,TA.PREMIO_PROX_SORTEO
    ,TA.FECHA_SORTEO,TA.IMPORTE
    ,DECODE(TA.ID_JEFE,NULL,'SIN JEFE' ,JEFE.DESCRIPCION)        AS JEFE_SORTEO
    ,DECODE(TA.ID_ESCRIBANO,NULL,'SIN ESCRIBANO',ES.DESCRIPCION) AS ESCRIBANO,TAG.ORDEN,TAG.SUCURSAL
FROM
    SGS.T_ANTICIPADA_GANADORES TAG, T_ANTICIPADA TA, SUPERUSUARIO.USUARIOS JEFE, SGS.T_ESCRIBANO ES
WHERE
    TAG.SORTEO = TAG.SORTEO
    AND TAG.ID_JUEGO = TA.ID_JUEGO
    AND TAG.SEMANA = TA.SEMANA
    AND TAG.SORTEO=TA.SORTEO
    AND TAG.ID_JUEGO=TA.ID_JUEGO
    AND TAG.ORDEN=TA.ORDEN
    AND TA.SEMANA = ? AND TA.ORDEN = ? AND TA.ID_JUEGO = ? AND TA.SORTEO = ?
    AND TA.ID_JEFE      = JEFE.ID_USUARIO(+)
    AND TA.ID_ESCRIBANO = ES.ID_ESCRIBANO(+)

	", array($semana, $orden, $_SESSION['id_juego'], $_SESSION['sorteo']));

} catch (exception $e) {
    die($db->ErrorMsg());
}
$row = siguiente($rsgordo);

$fecha_actual = date('d/m/Y');
$sorteo       = $_SESSION['sorteo'];
$juego        = $_SESSION['juego'];
$id_juego     = $_SESSION['id_juego'];
$serie        = $_SESSION['serie'];
$presentacion = "SORTEO NRO. " . $sorteo . " DEL JUEGO DE " . $juego;
$comentario   = ' Asignación de Premio Compra Anticipada  ';
$localidad    = $row->LOCALIDAD;
$orden        = $row->ORDEN;
$descripcion  = utf8_decode($row->PREMIO);
if (is_null($localidad)) {
    $localidad = 'CORDOBA';
}

if ($row->NOMBRE == 'VENTA CONTADO CASA CENTRAL') {
    $distribuyo = 'Distribuido 9001 CORDOBA';
} else if ($row->NOMBRE == 'VENTA CONTADO') {
    if ($row->SUCURSAL == 'CASA CENTRAL') {
        $distribuyo = 'Distribuido 9001 CORDOBA de Localidad ' . $row->SUCURSAL;
    } else {
        $distribuyo = 'Distribuido 9001 ' . $row->SUCURSAL . ' de Localidad ' . $row->SUCURSAL;
    }

} else if (!is_null($row->AGENCIA)) {
    $distribuyo = 'Distribuido en  Agencia NRO. ' . str_pad($row->AGENCIA, 4, 0, STR_PAD_LEFT) . ' de ' . $row->NOMBRE . ' de Localidad ' . $localidad . ', Delegacion:' . $row->SUCURSAL;
}
$distribuyo  = utf8_decode($distribuyo);
$fraccion    = $row->FRACCION;
$billete     = $row->BILLETE;
$copia       = 'ORIGINAL';
$jefe_sorteo = utf8_decode($row->JEFE_SORTEO);

require "header_listado_b.php";
$pdf = new PDF('P', 'mm', 'A4');

//seteo como quiero que sean las lineas
$pdf->SetLineWidth(0.3);
$pdf->AliasNbPages();
//agrego pagina sino no puedo trabajar
$pdf->AddPage();
//seteo los valores y estilos de la fuente en '' podria ir B como negrita, I italica,etc
//si no seteo la fuente no me deja imprimir. tamaño en puntos
$pdf->SetFont('Arial', '', 12);
//salto de linea de 20 de alto
$pdf->Ln(20);

$pdf->SetTextColor(0, 0, 0);

//ORIGINAL
$pdf->SetFont('Arial', 'B', 12);
$pdf->SetFillColor(240, 240, 240);
$pdf->rect(10, 10, 190, 30);
$pdf->rect(65, 10, 76, 13);
$pdf->setXY(74, 11);
$pdf->Cell(68, 11, 'I    N     F     O     R     M     E ', 0, 0, 'L');

//PARA ORIGINAL -DUPLICADO
$pdf->SetFont('Arial', 'B', 8);
$pdf->rect(175, 10, 25, 8);
$pdf->setXY(180, 10);
$pdf->Cell(178, 10, 'ORIGINAL', 0, 0, 'L');
$pdf->rect(10, 40, 190, 100);

//RECTAS PARA LISTAR BILLETES EN ORIGINAL
$pdf->rect(10, 40, 18, 14);
$pdf->rect(28, 40, 147, 14);
$pdf->rect(175, 40, 25, 14);
$pdf->setXY(10, 143);
$pdf->Cell(190, 10, '- - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - - -', 0, 0, 'C');

$pdf->rect(10, 158, 190, 30);
$pdf->rect(10, 188, 190, 100);

//RECTAS PARA LISTAR BILLETES EN DUPLICADO
$pdf->rect(10, 40, 18, 14);
$pdf->rect(175, 40, 25, 14);
$pdf->rect(10, 200, 190, 0);

$pdf->Image('../../img/LOGOhorizontal.jpg', 12, 12, 25, 10);

$pdf->SetFont('Arial', '', 8);
$pdf->setXY(3, 22);
$pdf->Cell(100, 10, 'LOTERIA DE LA PROVINCIA DE CORDOBA S.E.', 0, 0, 'C');
$pdf->setXY(6, 27);
$pdf->Cell(100, 10, utf8_decode('27 de Abril 185 - Córdoba.'), 0, 0, 'C');
$pdf->setXY(6, 32);
$pdf->Cell(100, 10, utf8_decode($presentacion), 0, 0, 'C');
$pdf->setXY(81, 16);
/*$pdf->Cell(110, 10, utf8_decode('Delegación: ' . $_SESSION['sucursal']), 0, 0, 'L');*/
$pdf->setXY(107, 24);
$pdf->SetFont('Arial', 'IB', 12);
$pdf->Cell(110, 10, utf8_decode($comentario), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->setXY(138, 33);
$pdf->Cell(110, 10, utf8_decode('Fecha Impresión: ' . $fecha_actual), 0, 0, 'L');

//COPIA DE LA CABECERA PARA ABAJO

$pdf->Image('../../img/LOGOhorizontal.jpg', 12, 160, 25, 10);

$pdf->SetFont('Arial', '', 8);
$pdf->setXY(3, 170);
$pdf->Cell(100, 10, 'LOTERIA DE LA PROVINCIA DE CORDOBA S.E.', 0, 0, 'C');
$pdf->setXY(6, 175);
$pdf->Cell(100, 10, utf8_decode('27 de Abril 185 - Córdoba.'), 0, 0, 'C');
$pdf->setXY(6, 180);
$pdf->Cell(100, 10, utf8_decode($presentacion), 0, 0, 'C');
$pdf->setXY(81, 164);
/*$pdf->Cell(110, 10, utf8_decode('Delegación: ' . $_SESSION['sucursal']), 0, 0, 'L');*/
$pdf->setXY(107, 170);
$pdf->SetFont('Arial', 'IB', 12);
$pdf->Cell(110, 10, utf8_decode($comentario), 0, 0, 'L');
$pdf->SetFont('Arial', '', 8);
$pdf->setXY(138, 180);
$pdf->Cell(110, 10, utf8_decode('Fecha Impresión: ' . $fecha_actual), 0, 0, 'L');

//rectangulo para remito
$pdf->SetFont('Arial', 'B', 12);
$pdf->rect(65, 158, 76, 13);
$pdf->setXY(74, 110);
$pdf->Cell(70, 110, 'I    N     F     O     R     M     E ', 0, 0, 'L');
$pdf->setXY(123, 110);

//PARA ORIGINAL -DUPLICADO
$pdf->SetFont('Arial', 'B', 8);
$pdf->rect(175, 158, 25, 7);
$pdf->setXY(178, 109);
$pdf->Cell(178, 109, 'DUPLICADO', 0, 0, 'L');
$pdf->SetFont('Arial', 'I', 8);
$pdf->setXY(14, 40);
$pdf->Cell(10, 10, 'Semana Nro', 0, 0, 'C');
$pdf->setXY(65, 40);
$pdf->Cell(95, 10, utf8_decode('Origen'), 0, 0, 'C');
$pdf->setXY(175, 40);
$pdf->Cell(25, 10, '  ', 0, 0, 'C');
$pdf->setXY(10, 186);
$pdf->Cell(17, 10, 'Semana Nro', 0, 0, 'C');
$pdf->rect(28, 188, 0, 12);
$pdf->setXY(65, 186);
$pdf->Cell(85, 10, utf8_decode('Origen'), 0, 0, 'C');
$pdf->rect(178, 188, 0, 12);
$pdf->setXY(177, 186);
$pdf->Cell(25, 10, '  ', 0, 0, 'C');
$pdf->rect(178, 188, 0, 12);
$pdf->SetFont('Arial', 'B', 30);
$pdf->SetFillColor(180, 180, 180);

//PARA SEGUNDA HOJA
$pdf->SetFont('Arial', 'B', 14);
$pdf->SetFillColor(180, 180, 180);

//FIRMAS EN EL DUPLICADO
$pdf->SetFont('Arial', 'B', 9);

//rectangulo de autorizados
$pdf->SetFillColor(240, 240, 240);
$pdf->setXY(10, 188);

$pdf->SetFont('Arial', 'I', 8);
$pdf->setXY(06, 47);
$pdf->Cell(25, 7, $semana, 0, 0, 'C');
$pdf->setXY(06, 195);
$pdf->Cell(25, 7, $semana, 0, 0, 'C');

$pdf->SetFont('Arial', 'BI', 6);
$pdf->setXY(65, 47);
$pdf->Cell(72, 7, strtoupper($distribuyo), 0, 0, 'C');
$pdf->setXY(66, 195);
$pdf->Cell(72, 7, strtoupper($distribuyo), 0, 0, 'C');
$pdf->SetFont('Arial', 'I', 6);

$pdf->setXY(178, 47);
$pdf->Cell(18, 7, $pagaret, 0, 0, 'C');
$pdf->setXY(180, 195);
$pdf->Cell(18, 7, $pagaret, 0, 0, 'C');
$pdf->SetFont('Arial', 'BI', 10);

//CAJA PARA DATOS DE ARRIBA
$pdf->rect(25, 80, 160, 30);

//DATOS DE ARRIBA
$largo = strlen($descripcion);
$pdf->SetFillColor(150, 150, 150);
$pdf->SetFont('Arial', 'BI', 14);
$pdf->setXY(10, 73);
$pdf->Cell(70, 7, 'ORDEN: ' . $orden, 0, 0, 'C', 0);
$pdf->setXY(102 - ($largo * 2.7) / 2, 82);
$pdf->Cell($largo * 2.9, 7, $descripcion, 0, 0, 'C', 0);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('Arial', 'I', 12);
$pdf->setXY(60, 100);
$pdf->Cell(50, 7, '    Fraccion Nro. ' . str_pad($fraccion, 2, 0, STR_PAD_LEFT) . ' del Billete Nro. ' . str_pad($billete, 5, 0, STR_PAD_LEFT), 0, 0, 'L');

//CAJA PARA DATOS DE ABAJO
$pdf->rect(25, 228, 160, 30);

//DATOS DE ABAJO
$largo = strlen($descripcion);
$pdf->SetFillColor(150, 150, 150);
$pdf->SetFont('Arial', 'BI', 14);
$pdf->setXY(10, 221);
$pdf->Cell(70, 7, 'ORDEN: ' . $orden, 0, 0, 'C', 0);
$pdf->setXY(102 - ($largo * 2.7) / 2, 230);
$pdf->Cell($largo * 2.9, 7, $descripcion, 0, 0, 'C', 0);
$pdf->SetFillColor(240, 240, 240);
$pdf->SetFont('Arial', 'I', 12);
$pdf->setXY(60, 248);
$pdf->Cell(50, 7, '    Fraccion Nro. ' . str_pad($fraccion, 2, 0, STR_PAD_LEFT) . ' del Billete Nro. ' . str_pad($billete, 5, 0, STR_PAD_LEFT), 0, 0, 'L');

//FIRMA quien imprime original
$pdf->SetFont('Arial', 'I', 10);
$pdf->setXY(161, 125);
$pdf->Cell(25, 7, '................................', 0, 0, 'C');
$pdf->setXY(161, 129);
$pdf->Cell(25, 7, $jefe_sorteo, 0, 0, 'C');

//FIRMA quien imprime duplicado
$pdf->SetFont('Arial', 'I', 10);
$pdf->setXY(161, 265);
$pdf->Cell(25, 7, '................................', 0, 0, 'C');
$pdf->setXY(161, 269);
$pdf->Cell(25, 7, $jefe_sorteo, 0, 0, 'C');

$pdf->setXY(10, 135);
$pdf->setXY(10, 139);
$pdf->setXY(10, 266);

//FRACCIONES
$pdf->setXY(10, 135);
$pdf->setXY(10, 139);
$pdf->setXY(10, 266);

$pdf->Output();
