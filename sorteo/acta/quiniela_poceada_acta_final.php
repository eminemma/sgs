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
                    TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY')                      AS FECHA_CADUCIDAD,QUINIELA_ASOC
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

$row_sor       = $rs_sorteo->FetchNextObject($toupper = true);
$fechasorteo   = $row_sor->FECHA_SORTEO;
$fechacaduca   = $row_sor->FECHA_CADUCIDAD;
$jefe          = $row_sor->JEFE;
$operador      = utf8_decode($row_sor->USUARIO);
$escribano     = utf8_decode($row_sor->ESCRIBANO);
$jefesorteo    = utf8_decode($row_sor->JEFE);
$quiniela_asoc = $row_sor->QUINIELA_ASOC;

//$db->debug=true;
try {
    $rs_extracciones = sql("   SELECT TE.ORDEN,TE.POSICION,TE.NUMERO,TE.SORTEO_ASOC,VALIDO,POSICION_DUPLICADO
                FROM SGS.T_EXTRACCION TE
                WHERE TE.SORTEO=?
                AND TE.ID_JUEGO=?
                AND TE.ZONA_JUEGO=1
        ORDER BY TE.ZONA_JUEGO DESC ,TE.ORDEN DESC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

$titulo = strtoupper($_SESSION['juego']);

$titulo2 = strtoupper('SORTEO ' . $_SESSION['sorteo'] . ' ' . $desc);
$conPie  = false;
$pdf     = new PDF('P');
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

$pdf->SetFont('Times', '', 9);
$pdf->SetXY(120, 50);
$pdf->Cell(30, 5, 'Sorteo Nro:', 0, 0, 'R');

$pdf->SetXY(150, 50);
$pdf->Cell(30, 5, $_SESSION['sorteo'], 1, 0, 'C');

$pdf->SetXY(120, 55);
$pdf->Cell(30, 5, 'Fecha:', 0, 0, 'R');

$pdf->SetXY(150, 55);
$pdf->Cell(30, 5, $row_sor->FECHA_SORTEO, 1, 0, 'C');

$pdf->SetXY(120, 60);
$pdf->Cell(30, 5, 'Hora:', 0, 0, 'R');

$pdf->SetXY(150, 60);
$pdf->Cell(30, 5, '    :     ', 1, 0, 'C');

$pdf->SetXY(120, 65);
$pdf->Cell(30, 5, 'Caducidad:', 0, 0, 'R');

$pdf->SetXY(150, 65);
$pdf->Cell(30, 5, $row_sor->FECHA_CADUCIDAD, 1, 1, 'C');

$pdf->SetXY(40, 80);

$unaPagina = true;
cabecera();
while ($row_extraccion = $rs_extracciones->FetchNextObject($toupper = true)) {
    $pdf->SetX(40);
    $pdf->Cell(10, 5, $row_extraccion->ORDEN, 'B', 0, 'C');
    $pdf->Cell(20, 5, $row_extraccion->POSICION, 'B', 0, 'C');
    $pdf->Cell(60, 5, getStringBetween($row_extraccion->SORTEO_ASOC, '(', ')'), 'B', 0, 'C');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 2, "0", STR_PAD_LEFT), 'B', 0, 'C');
    $pdf->Cell(40, 5, ($row_extraccion->VALIDO == 'S' ? 'Valido' : ($row_extraccion->VALIDO == 'D' ? 'Duplicado Con Posicion ' . $row_extraccion->POSICION_DUPLICADO : '')), 'B', 1, 'C');

    if ($pdf->GetY() >= 240) {
        $unaPagina = false;
        firma();
        $pdf->AddPage();
        $pdf->SetX(40);
        cabecera();
    }
}
if ($unaPagina) {
    firma();
}

$pdf->Output();
function cabecera()
{
    global $pdf, $row_sor;
    $pdf->SetFont('Times', 'B', 9);
    $pdf->Cell(10, 5, '#OR', 1, 0, 'C');
    $pdf->Cell(20, 5, 'POSICION', 1, 0, 'C');
    $pdf->Cell(60, 5, 'QUINIELA ASOCIADA N' . $row_sor->QUINIELA_ASOC, 1, 0, 'C');
    $pdf->Cell(20, 5, 'NUMERO', 1, 0, 'C');
    $pdf->Cell(40, 5, 'ESTADO', 1, 1, 'C');
}
function firma()
{
    global $pdf, $operador, $jefesorteo, $escribano;
    //hora
    //
    $pdf->SetFont('Times', 'B', 9);
    $pdf->SetXY(25, 250);
    $pdf->Cell(20, 0, utf8_decode('Consta en escritura Nº_________Sección_________ - Doy fe'), 0, 0, 1);

    $pdf->SetFont('Times', 'B', 9);
    $pdf->SetXY(25, 260);
    $pdf->Cell(150, 5, '___________________                                               ___________________                                      _________________________', 0, 1, 'J');
    $pdf->SetXY(25, 265);
    $pdf->Cell(150, 5, '          Operador                                                                 Jefe de Sorteos                                               Firma Escribano Actuante', 0, 0, 'J');
    $pdf->SetXY(28, 270);
    $pdf->Cell(25, 5, $operador, 0, 0, 'C');

    $pdf->SetXY(96, 270);
    $pdf->Cell(25, 5, $jefesorteo, 0, 0, 'C');

    $pdf->SetXY(162, 270);
    $pdf->Cell(25, 5, $escribano, 0, 0, 'C');

}

function getStringBetween($str, $from, $to)
{
    $sub = substr($str, strpos($str, $from) + strlen($from), strlen($str));
    return substr($sub, 0, strpos($sub, $to));
}
