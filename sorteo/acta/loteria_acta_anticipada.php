<?php
@session_start();
include_once dirname(__FILE__) . '/../../db.php';

$meses  = array("Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio", "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre");
$semana = (int) $_GET['semana'];

try {
    $res = sql("
SELECT TS.ID_JUEGO ,
  TS.SORTEO ,
  TS.SEMANA ,
  TS.PREMIO ,
  TS.ID_JEFE ,
  TS.ID_ESCRIBANO ,
  TS.PRESCRIPCION ,
  TS.PROX_SORTEO ,
  TS.PREMIO_PROX_SORTEO ,
  DECODE(TS.ID_JEFE,NULL,'SIN JEFE' ,JEFE.DESCRIPCION)        AS JEFE_SORTEO ,
  DECODE(TS.ID_ESCRIBANO,NULL,'SIN ESCRIBANO',ES.DESCRIPCION) AS ESCRIBANO ,
  TS.SORTEO,
  TAG.BILLETE,
  TAG.FRACCION,
  TAG.AGENCIA,
  nvl(TAG.LOCALIDAD,'CORDOBA') as LOCALIDAD ,
  TAG.NOMBRE AS NOMBRE,
  TS.FECHA_SORTEO,
  TS.IMPORTE
FROM
  SGS.T_ANTICIPADA TS,
  SUPERUSUARIO.USUARIOS JEFE,
  SGS.T_ESCRIBANO ES,
  SGS.T_ANTICIPADA_GANADORES TAG
WHERE
TS.ID_JUEGO = TAG.ID_JUEGO AND TS.SORTEO = TAG.SORTEO AND TS.SEMANA = TAG.SEMANA
AND TS.ID_JUEGO   	= ?
AND TS.SORTEO       = ?
AND TS.SEMANA       = ?
AND TS.ID_JEFE      = JEFE.ID_USUARIO(+)
AND TS.ID_ESCRIBANO = ES.ID_ESCRIBANO(+)
",
        array($_SESSION['id_juego'], $_SESSION['sorteo'], $semana));
    $row = siguiente($res);
} catch (exception $e) {
    die($db->ErrorMsg());
}

if ($semana == 1) {
    $texto_sorteo = 'PRIMER';
} else if ($semana == 2) {
    $texto_sorteo = 'SEGUNDO';
} else if ($semana == 3) {
    $texto_sorteo = 'TERCER';
} else if ($semana == 4) {
    $texto_sorteo = 'CUARTO';
} else if ($semana == 5) {
    $texto_sorteo = 'QUINTO';
} else if ($semana == 6) {
    $texto_sorteo = 'SEXTO';
} else if ($semana == 7) {
    $texto_sorteo = 'SEPTIMO';
} else if ($semana == 8) {
    $texto_sorteo = 'OCTAVO';
} else if ($semana == 9) {
    $texto_sorteo = 'NOVENO';
}
$fecha =
date('d', strtotime(str_replace('/', '-', $row->FECHA_SORTEO))) .
' de ' . $meses[date('m', strtotime(str_replace('/', '-', $row->FECHA_SORTEO))) - 1] .
' de ' .
date('Y', strtotime(str_replace('/', '-', $row->FECHA_SORTEO)));

$dia               = date('d', strtotime(str_replace('/', '-', $row->FECHA_SORTEO)));
$mes               = date('m', strtotime(str_replace('/', '-', $row->FECHA_SORTEO)));
$mes               = $meses[date('m', strtotime($row->FECHA_SORTEO)) - 1];
$jefe_sorteo       = $row->JEFE_SORTEO;
$escribano         = $row->ESCRIBANO;
$premio            = $row->PREMIO;
$billete           = $row->BILLETE;
$fraccion          = $row->FRACCION;
$numero_agencia    = $row->AGENCIA;
$nombre_agencia    = $row->NOMBRE;
$localidad_agencia = $row->LOCALIDAD;
require dirname(__FILE__) . '/../../librerias/pdf/fpdf.php';

class PDF extends FPDF
{

    public function Footer()
    {
        global $conPie;
        if ($conPie !== false) {
            $this->SetY(-15);
            $y_line = $this->GetY();
            $this->Line(10, $y_line, 200, $y_line);
            $this->SetFont('Arial', 'I', 8);
            $this->Cell(0, 7, 'Pagina: ' . $this->PageNo() . "/{nb}", 0, 0, 'R');
        }

    }
}

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 9);

$pdf->SetXY(10, 3);
$pdf->SetFont('Arial', 'B', 14);
$pdf->Cell(0, 7, utf8_decode('LOTER??A DE LA PROVINCIA DE C??RDOBA S.E.'), 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 7, 'ACTA DE SORTEO POR COMPRA ANTICIPADA', 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->Cell(0, 7, utf8_decode('EMISION N?? ') . $_SESSION['sorteo'], 0, 1, 'C');
$pdf->SetFont('Arial', 'B', 10);
$pdf->Cell(0, 7, $texto_sorteo . ' SORTEO (' . $fecha . ')', 0, 1, 'C');

$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 7, utf8_decode('En la Ciudad de C??rdoba, Capital de la Provincia del mismo nombre, Rep??blica Argentina, a los ' . $dia . ' d??as del mes de ' . $mes . ' del a??o ' . date('Y', strtotime(str_replace('/', '-', $row->FECHA_SORTEO))) . ', se re??nen en Sal??n de Sorteos de Loter??a de la Provincia de C??rdoba, sita en calle 27 de Abril 185 de ??sta Ciudad, el Sr/a. Jefe de Sorteo ' . $jefe_sorteo . ' y el Escribano/a ' . $escribano . ', a efectos de la realizaci??n del ' . $texto_sorteo . ' SORTEO por Compra Anticipada de Billetes de Loter??a correspondientes a la Emisi??n N?? ' . $_SESSION['sorteo'] . ' "Gordo de Invierno 2022", el cual se efectuar?? a trav??s de Sistema Inform??tico con el total de fracciones vendidas y cuyos datos (n??mero de billete y fracci??n) han sido ingresados al sistema correspondiente a los fines de la realizaci??n de dicho sorteo, cuyo premio consiste en:'));
$sql = "SELECT PREMIO,COUNT(*)AS CANTIDAD,MAX(ORDEN) ORDEN
FROM
   SGS.T_ANTICIPADA
WHERE ID_JUEGO    = ?
AND SORTEO       = ?
AND SEMANA       = ?
GROUP BY PREMIO
ORDER BY ORDEN";
$res = sql($sql, array($_SESSION['id_juego'], $_SESSION['sorteo'], $semana));
while ($row = siguiente($res)) {
    if ($row->CANTIDAD == 1) {
        $premio = str_replace('$', '', trim($row->PREMIO));
        $premio = str_replace('EN EFECTIVO', '', $premio);
        $premio = str_replace('.', '', $premio);
        $premio = str_replace(' ', '', $premio);

        if (!is_numeric($premio)) {
            $premio = $row->CANTIDAD . ' ' . $row->PREMIO;
        } else {
            $premio = $row->CANTIDAD . ' PREMIO DE ' . $row->PREMIO;
        }
    } else {
        $premio      = '' . $row->CANTIDAD . ' PREMIOS DE ' . $row->PREMIO;
        $premio_real = str_replace('$', '', trim($row->PREMIO));
        $premio_real = str_replace('EN EFECTIVO', '', $premio_real);
        $premio_real = str_replace('.', '', $premio_real);
        $premio_real = str_replace(' ', '', $premio_real);

        if (!is_numeric($premio_real)) {
            $premio = '(' . $row->CANTIDAD . ')   ' . $row->PREMIO;
        } else {
            $premio = $row->CANTIDAD . ' PREMIOS DE ' . $row->PREMIO;
        }

    }
    $pdf->SetFont('Arial', 'B', 10);
    $pdf->Cell(0, 7, utf8_decode($premio), 0, 1, 'C');
}

$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 7, utf8_decode('Recibiendo por parte de Departamento Sistemas el soporte inform??tico que contiene los datos ingresados,               siendo las ............. horas del d??a de la fecha se procede a realizar el sorteo resultando favorecido:'));
$sql = "SELECT TAG.BILLETE,TAG.FRACCION,TAG.AGENCIA,TAG.NOMBRE,TAG.LOCALIDAD,TAG.ORDEN,TAG.SUCURSAL,TA.PREMIO,count(*) as cantidad
        FROM
          SGS.T_ANTICIPADA_GANADORES TAG,SGS.T_ANTICIPADA TA
        WHERE TAG.ID_JUEGO    = ?
        AND TAG.SORTEO       = ?
        AND TAG.SEMANA       = ?
        AND TAG.SEMANA = TA.SEMANA
        AND TAG.ORDEN = TA.ORDEN
        AND TAG.SORTEO = TA.SORTEO
        AND TAG.ID_JUEGO = TA.ID_JUEGO
        group by TAG.BILLETE,TAG.FRACCION,TAG.AGENCIA,TAG.NOMBRE,TAG.LOCALIDAD,TAG.ORDEN,TAG.SUCURSAL,TA.PREMIO
        ORDER BY TAG.ORDEN";
$res = sql($sql, array($_SESSION['id_juego'], $_SESSION['sorteo'], $semana));
$i   = 1;
while ($row = siguiente($res)) {
    if ($i == 8) {
        $pdf->AddPage();
    }

    $billete           = $row->BILLETE;
    $fraccion          = $row->FRACCION;
    $numero_agencia    = $row->AGENCIA;
    $nombre_agencia    = $row->NOMBRE;
    $localidad_agencia = $row->LOCALIDAD;
    $sucursal          = $row->SUCURSAL;
    $orden             = $row->ORDEN;
    $premio            = $row->PREMIO;

    $pdf->Ln(1);
    $pdf->SetFont('Arial', 'B', 12);
    $pdf->SetX(30);
    $billete  = str_pad($billete, 5, 0, STR_PAD_LEFT);
    $fraccion = str_pad($fraccion, 2, "0", STR_PAD_LEFT);
    $pdf->Cell(100, 5, utf8_decode('ORDEN:' . $orden . '      N?? BILLETE: ' . $billete . '                                       FRACCI??N: ' . $fraccion), 0, 1, 'L');
    $pdf->SetX(30);
    $pdf->SetX(30);
    if ($nombre_agencia == 'VENTA CONTADO') {
        if ($sucursal == 'CASA CENTRAL') {
            $sucursal = 'CORDOBA';
        }

        $pdf->Cell(100, 5, utf8_decode('Comercializado por 9001 ' . $sucursal), 0, 1, 'L');
        $pdf->SetX(30);
        $pdf->Cell(100, 5, utf8_decode('de la Localidad de : ' . $sucursal . ', Delegaci??n:' . $sucursal), 0, 1, 'L');
        $pdf->SetX(30);
        $pdf->Cell(100, 5, utf8_decode('Premio : ' . $premio), 0, 1, 'L');
        $pdf->SetX(10);
    } else if ($nombre_agencia == 'VENTA CONTADO CASA CENTRAL') {
        $pdf->Cell(100, 5, utf8_decode('Comercializado por 9001 CORDOBA'), 0, 1, 'L');
        $pdf->SetX(30);
        $pdf->Cell(100, 5, utf8_decode('de la Localidad de : Cordoba, Delegaci??n: Casa Central'), 0, 1, 'L');
        $pdf->SetX(30);
        $pdf->Cell(100, 5, utf8_decode('Premio : ' . $premio), 0, 1, 'L');
        $pdf->SetX(10);
    } else if (!is_null($numero_agencia)) {
        $pdf->Cell(100, 5, utf8_decode('Comercializado por la Agencia N??: ' . str_pad($numero_agencia, 4, 0, STR_PAD_LEFT) . ' ' . $nombre_agencia), 0, 1, 'L');
        $pdf->SetX(30);
        $pdf->Cell(100, 5, utf8_decode('de la Localidad de : ' . $localidad_agencia . ', Delegaci??n:' . $sucursal), 0, 1, 'L');
        $pdf->SetX(30);
        $premio_real = str_replace('$', '', trim($row->PREMIO));
        $premio_real = str_replace('EN EFECTIVO', '', $premio_real);
        $premio_real = str_replace('.', '', $premio_real);
        $premio_real = str_replace(' ', '', $premio_real);

        if (!is_numeric($premio_real)) {
            $premio = '(' . $row->CANTIDAD . ')   ' . $row->PREMIO;
        } else {
            $premio = $row->PREMIO;
        }
        $pdf->Cell(100, 5, utf8_decode('Premio : ' . $premio), 0, 1, 'L');
        $pdf->SetX(10);
    }

    $pdf->Line(30, $pdf->GetY(), 200, $pdf->GetY());
    $i += 1;
}
$pdf->SetFont('Arial', '', 10);
$pdf->MultiCell(0, 7, utf8_decode('Siendo las ............... horas, se da por finalizado el Acto, previa lectura y ratificaci??n de los actuantes, firman la presente ante m?? Escribano/a ' . $escribano . ', de todo lo que certifico, doy fe.-'));
$pdf->SetFont('Times', 'B', 11);
$pdf->SetXY(25, 250);
$pdf->Cell(20, 0, utf8_decode('Consta en escritura N??_________Secci??n_________ - Doy fe'), 0, 0, 1);

$pdf->SetFont('Times', 'B', 9);
$pdf->SetXY(25, 263);
$pdf->Cell(150, 5, '___________________                                      _________________________', 0, 1, 'C');
$pdf->SetXY(25, 268);
$pdf->Cell(150, 5, '      Jefe de Sorteos                                               Firma Escribano Actuante', 0, 0, 'C');
$pdf->SetFont('Arial', '', 10);
$pdf->Output();
