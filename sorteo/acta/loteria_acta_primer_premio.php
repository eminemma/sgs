<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';

//$db->debug=true;
//OBTENGO DATOS DEL SORTEO

try {
    $rs_sorteo = sql("SELECT TO_CHAR(SO.FECHA_SORTEO, 'DD/MM/YYYY') AS FECHA_SORTEO,
              TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO, 'DD/MM/YYYY') AS FECHA_CADUCIDAD,
              U.DESCRIPCION JEFE_SORTEO,
              SO.DESCRIPCION AS USUARIO,
              US.DESCRIPCION OPERADOR,
              ESC.DESCRIPCION     AS ESCRIBANO
              from sgs.T_SORTEO       SO,
              sgs.T_ESCRIBANO    ESC,
              SUPERUSUARIO.USUARIOS U,
              SUPERUSUARIO.USUARIOS US
              where SO.ID_ESCRIBANO = ESC.ID_ESCRIBANO (+)
              AND U.ID_USUARIO(+) = SO.ID_JEFE
              AND US.ID_USUARIO(+) = SO.ID_OPERADOR
              AND SO.SORTEO = ?
              AND SO.ID_JUEGO = ?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}
$row_sor     = $rs_sorteo->FetchNextObject($toupper = true);
$fechasorteo = $row_sor->FECHA_SORTEO;
$fechacaduca = $row_sor->FECHA_CADUCIDAD;
$jefesorteo  = utf8_decode($row_sor->JEFE_SORTEO);
$operador    = utf8_decode($row_sor->OPERADOR);
$escribano   = utf8_decode($row_sor->ESCRIBANO);

try {
    $rs = sql("   SELECT DISTINCT LPAD(numero, '5', '0') as EXTRACCION,
                                            posicion as ID_DESCRIPCION,
                                            DECODE(
                                            (SELECT COUNT(*) FROM sgs.t_billetes_participantes WHERE SORTEO = te.SORTEO
                                            AND ID_JUEGO                                                    = te.ID_JUEGO
                                            AND BILLETE                                                     = te.numero
                                            ), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO,tPP.PREMIO_EFECTIVO,
                                           (SELECT DISTINCT DESCRIPCION_AGENCIA FROM sgs.t_billetes_participantes
                                              WHERE SORTEO = te.SORTEO
                                              AND ID_JUEGO                                                    = te.ID_JUEGO
                                              AND BILLETE                                                     = te.numero
                                              AND ROWNUM=1)
                                             AS DISTRIBUIDOEN
                              FROM sgs.T_EXTRACCION te,SGS.t_programa_premios tpp,SGS.T_SORTEO S
                              WHERE zona_juego=1
                                AND posicion    =1
                                AND te.posicion=tpp.id_descripcion
                                AND S.ID_JUEGO=TE.ID_JUEGO
                                AND S.SORTEO=TE.SORTEO
                                AND S.ID_PROGRAMA=TPP.ID_PROGRAMA
                                AND TE.sorteo      =?
                                AND TE.id_juego    =?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

$row = $rs->FetchNextObject($toupper = true);

//$titulo=strtoupper('ACTA SORTEO DE '.$_SESSION['juego'].' '.$_SESSION['juego_tipo']);

if ($_SESSION['sorteo'] == 4766) {
    $titulo = strtoupper('ACTA SORTEO DE ' . $_SESSION['juego'] . ' ORDINARIA');
} else {
    $titulo = strtoupper('LOTERIA ' . $_SESSION['juego_tipo']);
}

if ($_SESSION['codigo_tipo'] == 2) {
    $desc = $_SESSION['descripcion_sorteo'];
} else {
    $desc = "";
}

$titulo2 = strtoupper(utf8_decode('EMISIÓN ' . $_SESSION['sorteo'] . ' PRIMER PREMIO'));

require "header_listado.php";
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times', 'B', 12);
$pdf->SetXY(10, 50);
$pdf->Cell(40, 5, utf8_decode('Posición'), 'B', 0, 'C');
$pdf->Cell(50, 5, 'Fecha ', 'B', 0, 'C');
$pdf->Cell(40, 5, utf8_decode('Número'), 'B', 0, 'C');
$pdf->Cell(40, 5, '', 'B', 1, 'L');

$y_line = $pdf->GetY();
$pdf->SetX(10);
$pdf->SetFont('Times', '', 18);

$pdf->Cell(40, 8, $row->ID_DESCRIPCION, 'B', 0, 'C');
$pdf->SetFont('Times', '', 14);
$pdf->Cell(50, 8, $fechasorteo, 'B', 0, 'C');
$pdf->SetFont('Times', '', 24);
$pdf->Cell(40, 8, $row->EXTRACCION, 'B', 0, 'C');
$pdf->SetFont('Times', '', 10);
$pdf->Cell(40, 8, $row->COMERCIALIZADO, 'B', 1, 'L');
$pdf->Ln(20);
$pdf->SetX(20);
if ($row->COMERCIALIZADO == 'VENDIDO') {
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(30, 10, ' Monto del Premio $ ' . number_format($row->PREMIO_EFECTIVO, 0, ',', '.'), '0', 0, 'L');
}
$pdf->SetXY(20, 100);

$pdf->SetFont('Times', 'B', 10);
if (!is_null($row->DISTRIBUIDOEN)) {
    try {
        $rs_fracciones = sql(" SELECT LPAD(FRACCION,2,0) AS FRACCION,DESCRIPCION_AGENCIA,LOCALIDAD,ID_AGENCIA,PROVINCIA,ID_SUCURSAL
              FROM sgs.t_billetes_participantes
              WHERE SORTEO = ?
              AND ID_JUEGO = ?
              AND BILLETE  = ?", array($_SESSION['sorteo'], $_SESSION['id_juego'], $row->EXTRACCION));
    } catch (exception $e) {die($db->ErrorMsg());}
    $distribuido = '';
    $i           = 0;
    $agencia     = '';
    while ($row_fraccion = $rs_fracciones->FetchNextObject($toupper = true)) {
        if ($i == 0) {
            $agencia = $row_fraccion->DESCRIPCION_AGENCIA;
            //$distribuido.=$row_fraccion->DESCRIPCION_AGENCIA.',';
            $distribuido = $row_fraccion->DESCRIPCION_AGENCIA;

            $distribuido = str_pad($row_fraccion->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . '-' . $distribuido . ' ' . str_pad($row_fraccion->ID_SUCURSAL, 2, "0", STR_PAD_LEFT) . '-' . $row_fraccion->DESCRIPCION_SUCURSAL . ' ' . $row_fraccion->PROVINCIA;

            $i = 1;
        }
        if ($row_fraccion->DESCRIPCION_AGENCIA != $agencia) {
            $distribuido = $row_fraccion->DESCRIPCION_AGENCIA;

            $distribuido = str_pad($row_fraccion->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . '-' . $distribuido . ' ' . str_pad($row_fraccion->ID_SUCURSAL, 2, "0", STR_PAD_LEFT) . '-' . $row_fraccion->DESCRIPCION_SUCURSAL . ' ' . $row_fraccion->PROVINCIA;

            $agencia = $row_fraccion->DESCRIPCION_AGENCIA;
        }

    }

    /*if($distribuido=='VENTA CONTADO,'){
    $distribuido='VENTA MOSTRADOR';
    }*/

    // $pdf->MultiCell(180,5,'DISTRIBUIDO A: '.$distribuido,0,'L');
}

if (!is_null($row->DISTRIBUIDOEN)) {
    $pdf->SetFont('Times', 'B', 10);
    $pdf->SetX(24);
    $pdf->Cell(80, 8, 'DETALLE DE FRACCIONES CON PREMIO', 0, 1, 'L');
    $pdf->SetX(24);
    $pdf->SetFont('Times', 'B', 8);
    $pdf->Cell(18, 5, 'FRACCION', 1, 0, 'R');
    $pdf->Cell(100, 5, 'AGENCIA', 1, 0, 'C');
    $pdf->Cell(60, 5, 'LOCALIDAD', 1, 1, 'C');
    try {
        $rs_fracciones = sql(" SELECT LPAD(FRACCION,2,0) AS FRACCION,DESCRIPCION_AGENCIA,LOCALIDAD,ID_AGENCIA,ID_SUCURSAL,DESCRIPCION_SUCURSAL
              FROM sgs.t_billetes_participantes
              WHERE SORTEO = ?
              AND ID_JUEGO = ?
              AND BILLETE  = ?", array($_SESSION['sorteo'], $_SESSION['id_juego'], $row->EXTRACCION));
    } catch (exception $e) {die($db->ErrorMsg());}
    while ($row_fraccion = $rs_fracciones->FetchNextObject($toupper = true)) {
        $distribuido = '';
        $pdf->SetX(24);
        $pdf->Cell(18, 5, $row_fraccion->FRACCION, 1, 0, 'C');
        $distribuido = $row_fraccion->DESCRIPCION_AGENCIA;

        $distribuido = str_pad($row_fraccion->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . '- ' . $distribuido . ' ' . str_pad($row_fraccion->ID_SUCURSAL, 2, "0", STR_PAD_LEFT) . '-' . $row_fraccion->DESCRIPCION_SUCURSAL . ' ' . $row_fraccion->PROVINCIA;
        $pdf->Cell(100, 5, $distribuido, 1, 0, 'L');
        $pdf->Cell(60, 5, $row_fraccion->LOCALIDAD, 1, 1, 'L');
    }
}

$pdf->SetFont('Times', 'B', 9);
$pdf->SetXY(25, 264);
$pdf->Cell(150, 5, '___________________                                               ___________________                                      _________________________', 0, 1, 'J');
$pdf->SetXY(25, 268);
$pdf->Cell(150, 5, '          Operador                                                                  Jefe de Sorteos                                               Firma Escribano Actuante', 0, 0, 'J');
//$pdf->Cell(150,5,'  Firma Responsable                                                     Firma Responsable                                          Firma Escribano Actuante',0,0,'J');
$pdf->SetXY(28, 271);
$pdf->Cell(25, 5, $operador, 0, 0, 'C');

$pdf->SetXY(96, 271);
$pdf->Cell(25, 5, $jefesorteo, 0, 0, 'C');

$pdf->SetXY(162, 271);
$pdf->Cell(25, 5, $escribano, 0, 0, 'C');
$conPie = false;
$pdf->Output();
