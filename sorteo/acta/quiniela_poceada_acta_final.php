<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';

require "header_listado.php";
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');

//OBTENGO DATOS DEL SORTEO
try {
    $rs_sorteo = sql("  SELECT TO_CHAR(SO.FECHA_SORTEO,'DD/MM/YYYY')       AS FECHA_SORTEO,
                    jefe.descripcion                                  AS JEFE,
                    operador.descripcion                              AS USUARIO,
                    ESC.DESCRIPCION                                  AS ESCRIBANO,
                    TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY')                      AS FECHA_CADUCIDAD
                FROM  SGS.T_SORTEO SO,
                    SGS.T_ESCRIBANO ESC,
                    SUPERUSUARIO.usuarios jefe,
                    SUPERUSUARIO.usuarios operador
                WHERE   SO.ID_ESCRIBANO=ESC.ID_ESCRIBANO(+)
                  AND jefe.ID_USUARIO(+)=so.id_jefe
                  AND operador.ID_USUARIO(+)=SO.id_operador
                  AND SORTEO           = ?
                  AND ID_JUEGO         = ?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {
    die($db->ErrorMsg());
}

$row_sor     = $rs_sorteo->FetchNextObject($toupper = true);
$fechasorteo = $row_sor->FECHA_SORTEO;
$fechacaduca = $row_sor->FECHA_CADUCIDAD;
$jefe        = $row_sor->JEFE;
$operador    = utf8_decode($row_sor->USUARIO);
$escribano   = utf8_decode($row_sor->ESCRIBANO);
$jefesorteo  = utf8_decode($row_sor->JEFE);

//$db->debug=true;
try {
    $rs_extracciones = sql("   SELECT te.orden,te.posicion,te.numero,TE.SORTEO_ASOC
                FROM SGS.T_EXTRACCION te
                WHERE te.SORTEO=?
                AND te.ID_JUEGO=?
                AND te.ZONA_JUEGO=1
        ORDER BY te.zona_juego desc ,te.ORDEN DESC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}



$titulo = strtoupper($_SESSION['juego']);

$titulo2 = strtoupper('EMISION ' . $_SESSION['sorteo'] . ' ' . $desc);

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

$x_suc           = 0;
$x_fp_nombre     = '';
$corte           = 0;
$total           = 0;
$estado_anterior = 0;
$jj              = 0;
$zy              = 123;
$zy1             = 123;
$x               = 42;
$xx              = 100;

$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 48);
$pdf->Cell(30, 8, 'Fecha:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 48);
$pdf->Cell(30, 8, $fechasorteo, 1, 0, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 56);
$pdf->Cell(30, 8, 'Hora:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 56);
$pdf->Cell(30, 8, '..............', 1, 0, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 64);
$pdf->Cell(30, 8, 'Caducidad:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 64);
$pdf->Cell(30, 8, $fechacaduca, 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetXY(60, 100);

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(10, 5, '#OR', 1, 0, 'C');
$pdf->Cell(20, 5, 'POSICION', 1, 0, 'C');
$pdf->Cell(20, 5, 'ENTERO', 1, 0, 'C');
$pdf->Cell(60, 5, 'EXTRAIDO', 1, 1, 'C');
while ($row_extraccion = $rs_extracciones->FetchNextObject($toupper = true)) {
  $pdf->SetX(60);
    $pdf->Cell(10, 5, $row_extraccion->ORDEN, 'B', 0, 'C');
    $pdf->Cell(20, 5, $row_extraccion->POSICION, 'B', 0, 'C');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 2, "0", STR_PAD_LEFT), 'B', 0, 'C');
    $pdf->Cell(60, 5, ($row_extraccion->SORTEO_ASOC), 'B', 1, 'L');
}

//hora
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(87, 250);
$pdf->Cell(20, 5, 'Hora de Finalizacion:............', 0, 0, 'L');

$pdf->SetFont('Times', 'B', 9);
$pdf->SetXY(25, 264);
$pdf->Cell(150, 5, '___________________                                               ___________________                                      _________________________', 0, 1, 'J');
$pdf->SetXY(25, 268);
$pdf->Cell(150, 5, '          Operador                                                                  Jefe de Sorteos                                               Firma Escribano Actuante', 0, 0, 'J');

$pdf->SetXY(28, 271);
$pdf->Cell(25, 5, $operador, 0, 0, 'C');

$pdf->SetXY(96, 271);
$pdf->Cell(25, 5, $jefesorteo, 0, 0, 'C');

$pdf->SetXY(162, 271);
$pdf->Cell(25, 5, $escribano, 0, 0, 'C');
$pdf->Output();
