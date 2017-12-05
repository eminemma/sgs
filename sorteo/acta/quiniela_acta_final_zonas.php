<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';
require "header_listado.php";
try {
    $rs_sorteo = sql("  SELECT  TO_CHAR(SO.FECHA_SORTEO,'DD/MM/YYYY') AS FECHA_SORTEO,
                                JEFE.DESCRIPCION                                  AS JEFE,
                                OPERADOR.DESCRIPCION                              AS USUARIO,
                                ESC.DESCRIPCION                                  AS ESCRIBANO,
                                TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY')                      AS FECHA_CADUCIDAD
                        FROM  SGS.T_SORTEO SO,
                              SGS.T_ESCRIBANO ESC,
                              SUPERUSUARIO.USUARIOS JEFE,
                              SUPERUSUARIO.USUARIOS OPERADOR
                        WHERE   SO.ID_ESCRIBANO       = ESC.ID_ESCRIBANO(+)
                          AND JEFE.ID_USUARIO(+)      = SO.ID_JEFE
                          AND OPERADOR.ID_USUARIO(+)  = SO.ID_OPERADOR
                          AND SORTEO                  = ?
                          AND ID_JUEGO                = ?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
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
    $rs_extracciones = sql("    SELECT TE.ORDEN,TPP.PREMIO_EFECTIVO AS PREMIO,
                                      TPD.ID_PREMIO_DESC ||' '|| TPD.DESCRIPCION AS PREMIO,TE.POSICION,TE.NUMERO,TE.FRACCION,DECODE(
                                      (
                                        SELECT COUNT(*) FROM SGS.T_BILLETES_PARTICIPANTES
                                        WHERE SORTEO = TE.SORTEO
                                          AND ID_JUEGO                                                    = TE.ID_JUEGO
                                          AND BILLETE                                                     = TE.NUMERO
                                          AND FRACCION                                                    =TE.FRACCION
                                      ), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO,
                                      (SELECT COUNT(*) FROM SGS.T_BILLETES_PARTICIPANTES WHERE BILLETE=TE.NUMERO AND SORTEO=TE.SORTEO)
                                      AS GANADORES,TO_CHAR(FECHA_EXTRACCION,'DD/MM/YY HH:MI:SS') AS FECHA_EXTRACCION
                                FROM SGS.T_EXTRACCION TE,SGS.T_PROGRAMA_PREMIOS TPP,SGS.T_PREMIO_DESCRIPCION TPD,SGS.T_SORTEO S
                                WHERE TE.SORTEO           = ?
                                  AND TE.ID_JUEGO         = ?
                                  AND TE.ZONA_JUEGO       = 1
                                  AND TPP.ID_DESCRIPCION  = TPD.ID_PREMIO_DESC
                                  AND TE.POSICION         = TPP.ID_DESCRIPCION
                                  AND S.ID_JUEGO          = TE.ID_JUEGO
                                  AND S.SORTEO            = TE.SORTEO
                                  AND S.ID_PROGRAMA       = TPP.ID_PROGRAMA
                                ORDER BY TE.ZONA_JUEGO DESC ,TE.ORDEN DESC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

$titulo  = strtoupper('ACTA SORTEO DE ' . $_SESSION['juego'] . ' ' . utf8_decode($_SESSION['juego_tipo']));
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
$pdf->SetXY(10, 100);

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(40, 5, '', 0, 0, 'C');
$pdf->Cell(20, 5, '#ORDEN', 1, 0, 'C');
$pdf->Cell(70, 5, 'PREMIO', 1, 0, 'C');
$pdf->Cell(20, 5, 'NUMERO', 1, 1, 'C'); /*
$pdf->Cell(30, 5, 'FECHA', 1, 1, 'C');*/
while ($row_extraccion = $rs_extracciones->FetchNextObject($toupper = true)) {
    $pdf->Cell(40, 5, '', 0, 0, 'C');
    $pdf->Cell(20, 5, $row_extraccion->ORDEN, 'B', 0, 'C');
    $pdf->Cell(70, 5, $row_extraccion->PREMIO, 'B', 0, 'L');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 4, "0", STR_PAD_LEFT), 'B', 1, 'C'); /*
$pdf->Cell(30, 5, $row_extraccion->FECHA_EXTRACCION, 'B', 1, 'C');*/
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

try {
    $rs_extracciones = sql("   SELECT TE.ORDEN,
                                      DECODE(TPP.TIPO_PREMIO,'ESPECIE',TDE.DESCRIPCION_ESPECIA,'EFECTIVO','$'||TPP.PREMIO_EFECTIVO) AS PREMIO,
                                      TPD.DESCRIPCION,TE.POSICION,TE.NUMERO,TE.FRACCION,
                                      DECODE(
                                              (
                                                SELECT COUNT(*) FROM SGS.T_BILLETES_PARTICIPANTES WHERE SORTEO = TE.SORTEO
                                                AND ID_JUEGO                                                    = TE.ID_JUEGO
                                                AND BILLETE                                                     = TE.NUMERO
                                                AND FRACCION                                                    =TE.FRACCION
                                              ), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO,
                                      (SELECT COUNT(*) FROM SGS.T_BILLETES_PARTICIPANTES WHERE BILLETE=TE.NUMERO AND FRACCION=TE.FRACCION AND SORTEO=TE.SORTEO)
                                      AS GANADORES,TO_CHAR(FECHA_EXTRACCION,'DD/MM/YY HH:MI:SS') AS FECHA_EXTRACCION
                                FROM SGS.T_EXTRACCION TE,SGS.T_PROGRAMA_PREMIOS TPP,SGS.T_PREMIO_DESCRIPCION TPD,SGS.T_DESCRIPCION_ESPECIAS TDE,SGS.T_SORTEO S
                                WHERE TE.SORTEO=?
                                    AND TE.ID_JUEGO=?
                                    AND TE.ZONA_JUEGO=3
                                    AND TPP.ID_DESCRIPCION=TPD.ID_PREMIO_DESC
                                    AND TE.POSICION=TPP.ID_DESCRIPCION
                                    AND TPP.ID_DESCRIPCION=TPD.ID_PREMIO_DESC
                                    AND TDE.ID_DESCRIPCION_ESPECIA=TPP.PREMIO_ID_ESPECIAS
                                    AND TE.ID_JUEGO=S.ID_JUEGO
                                    AND TE.SORTEO=S.SORTEO
                                    AND TPP.ID_PROGRAMA=S.ID_PROGRAMA
                                ORDER BY TE.ZONA_JUEGO DESC ,TE.ORDEN DESC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

$titulo2 = strtoupper('PREMIOS EXTRAORDINARIOS, EMISION ' . $_SESSION['sorteo'] . ' ' . $desc);
if ($rs_extracciones->RecordCount() == 0) {
    $pdf->Output();
}

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
$pdf->SetXY(10, 100);

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(10, 5, '#ORDEN', 1, 0, 'C');
$pdf->Cell(20, 5, 'PREMIO', 1, 0, 'C');
$pdf->Cell(20, 5, 'ENTERO', 1, 0, 'C');
$pdf->Cell(10, 5, 'FRA.', 1, 0, 'C');
$pdf->Cell(60, 5, 'PREMIO', 1, 0, 'C');
$pdf->Cell(10, 5, 'GAN.', 1, 0, 'C');
$pdf->Cell(65, 5, 'IMPO./ESPECIAS', 1, 1, 'C');
while ($row_extraccion = $rs_extracciones->FetchNextObject($toupper = true)) {
    $pdf->Cell(10, 5, $row_extraccion->ORDEN, 'B', 0, 'C');
    $pdf->Cell(20, 5, $row_extraccion->POSICION, 'B', 0, 'C');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 5, "0", STR_PAD_LEFT), 'B', 0, 'C');
    $pdf->Cell(10, 5, str_pad($row_extraccion->FRACCION, 2, "0", STR_PAD_LEFT), 'B', 0, 'C');
    $pdf->Cell(60, 5, $row_extraccion->DESCRIPCION, 'B', 0, 'L');
    $pdf->Cell(10, 5, $row_extraccion->GANADORES, 'B', 0, 'C');
    $pdf->Cell(65, 5, $row_extraccion->PREMIO, 'B', 1, 'R');
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
