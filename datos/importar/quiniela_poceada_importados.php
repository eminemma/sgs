<?php
@session_start();
//error_reporting(E_ALL);
//ini_set('display_errors', 1);
include_once dirname(__FILE__) . '/../../db.php';
require dirname(__FILE__) . '/../../sorteo/acta/header_listado.php';
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');
$sorteo       = $_SESSION['sorteo'];
$id_juego     = $_SESSION['id_juego'];
$fecha_sorteo = null;
try {
    $rs_modalidad = sql("	SELECT COUNT(*) AS CANTIDAD,MODALIDAD,to_char(min(FECHA_IMPORTACION),'dd/mm/yyyy hh24:mi:ss') as FECHA_IMPORTACION
						FROM SGS.T_BILLETES_PARTICIPANTES
						WHERE SORTEO = ?
						GROUP BY MODALIDAD
						ORDER BY COUNT(*) desc", array($sorteo));

} catch (exception $e) {
    die($db->ErrorMsg());
}
if ($row_modalidad = $rs_modalidad->FetchNextObject($toupper = true)) {
    $fecha_importacion = $row_modalidad->FECHA_IMPORTACION;
}
try {
    $rs_sorteo = sql("SELECT DISTINCT TO_CHAR(FECHA_SORTEO,'DD/MM/YYYY') AS FECHA_SORTEO,
  							(	SELECT INITCAP(US.DESCRIPCION)
								FROM SUPERUSUARIO.USUARIOS US
								WHERE US.ID_USUARIO=TS.ID_JEFE) AS JEFE,
							(	SELECT INITCAP(US1.DESCRIPCION)
								FROM SUPERUSUARIO.USUARIOS US1
								WHERE US1.ID_USUARIO=TS.ID_OPERADOR) AS OPERADOR,
							INITCAP(TE.DESCRIPCION) AS ESCRIBANO,TS.MONTO_FRACCION,
  							TO_CHAR(TS.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY') AS FECHA_CADUCIDAD,
							LPAD(TS.SORTEO,5,'0') AS NRO,QUINIELA_ASOC
					 	FROM 	SGS.T_SORTEO TS,
					   			SGS.T_ESCRIBANO TE,
								SUPERUSUARIO.USUARIOS US
						WHERE 	TS.ID_ESCRIBANO=TE.ID_ESCRIBANO
							AND SORTEO=?
							AND ID_JUEGO=?", array($sorteo, $id_juego));
} catch (exception $e) {die($db->ErrorMsg());}
if ($row_sorteo = $rs_sorteo->FetchNextObject($toupper = true)) {
    $fecha_sorteo = $row_sorteo->FECHA_SORTEO;
    $escribano    = (is_null($row_sorteo->ESCRIBANO)) ? 'Sin Escribano' : utf8_decode($row_sorteo->ESCRIBANO);
    $jefe         = (is_null($row_sorteo->JEFE)) ? 'Sin Jefe de Sorteo' : utf8_decode($row_sorteo->JEFE);
    $fecha_caduca = $row_sorteo->FECHA_CADUCIDAD;
    //$estado_sorteo=$row_sorteo->ESTADO_SORTEO;
    $monto_fraccion = $row_sorteo->MONTO_FRACCION;
    $sorteo         = $row_sorteo->NRO;
    $quiniela_asoc  = $row_sorteo->QUINIELA_ASOC;
    $operador       = (is_null($row_sorteo->OPERADOR)) ? 'Sin Operador' : $row_sorteo->OPERADOR;
}
try {
    $rs_billete = sql("SELECT COUNT(*) AS CANTIDAD
										FROM sgs.T_BILLETES_PARTICIPANTES
										WHERE SORTEO=?", array($sorteo));

    $cantidad = 0;
    if ($row_billete = $rs_billete->FetchNextObject($toupper = true)) {
        $cantidad = $row_billete->CANTIDAD;
    }
} catch (exception $e) {
    die($db->ErrorMsg());
}

try {
    $rs_programa_premios = sql("SELECT COUNT(*) AS CANTIDAD
										FROM sgs.T_BILLETES_PARTICIPANTES
										WHERE SORTEO=?", array($sorteo));

} catch (exception $e) {die($db->ErrorMsg());}

try {
    $rs_host  = sql("SELECT SYS_CONTEXT('USERENV','SERVER_HOST') as SERVER FROM DUAL");
    $row_host = $rs_host->FetchNextObject($toupper = true);

} catch (exception $e) {die($db->ErrorMsg());}
$titulo  = strtoupper('DATOS IMPORTADOS DE ' . $_SESSION['juego']);
$titulo2 = strtoupper('EQUIPO (' . $row_host->SERVER . ')');
//
$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(10, 43);
$pdf->Cell(100, 8, 'Datos Sorteo - Fecha Importacion:' . $fecha_importacion, 1, 1, 'L', 1);
$pdf->SetFont('Arial', 'B', 9);
$pdf->SetXY(20, 50);
$pdf->Cell(30, 8, 'Nro: ', 0, 0, 'L');
$pdf->SetXY(70, 50);
$pdf->Cell(30, 8, $sorteo . ' - ' . utf8_decode($_SESSION['juego_tipo']), 0, 1, 'L');
$pdf->SetXY(20, 55);
$pdf->Cell(30, 8, 'Fecha: ', 0, 0, 'L');
$pdf->SetXY(70, 55);
$pdf->Cell(30, 8, $fecha_sorteo, 0, 1, 'L');
$pdf->SetXY(20, 60);
$pdf->Cell(30, 8, 'Jefe: ', 0, 0, 'L');
$pdf->SetXY(70, 60);
$pdf->Cell(30, 8, $jefe, 0, 1, 'L');
$pdf->SetXY(20, 65);
$pdf->Cell(30, 8, 'Escribano: ', 0, 0, 'L');
$pdf->SetX(70, 65);
$pdf->Cell(30, 8, $escribano, 0, 1, 'L');
$pdf->SetXY(20, 70);
$pdf->Cell(30, 8, 'Operador: ', 0, 0, 'L');
$pdf->SetX(70, 70);
$pdf->Cell(30, 8, $operador, 0, 1, 'L');
$pdf->SetXY(20, 75);
$pdf->Cell(30, 8, 'Quiniela Asociada: ', 0, 0, 'L');
$pdf->SetX(70, 75);
$pdf->Cell(30, 8, $quiniela_asoc, 0, 1, 'L');

$pdf->SetXY(20, 80);
$pdf->Cell(30, 8, 'Fecha de Caducidad: ', 0, 0, 'L');
$pdf->SetXY(70, 80);
$pdf->Cell(30, 8, $fecha_caduca, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetXY(10, 90);
$pdf->Cell(110, 4, 'PROGRAMA DE PREMIOS', 1, 1, 'L', 1);
try {

    $rs_programa_premios = sql("	SELECT ID_DESCRIPCION                                               AS POSICION,
									  TPD.DESCRIPCION                                                   AS DESCRIPCION,
									    CASE when ID_DESCRIPCION <= 20 THEN 'TRADICIONAL'
										else 'EXTRAORDINARIO'
									END AS TIPO,
									  TPP.SALE_O_SALE,
									  DECODE(TPP.PREMIO_EFECTIVO,NULL,TDE.DESCRIPCION_ESPECIA,TPP.PREMIO_EFECTIVO) AS PREMIO,
                                      TP.ID_PROGRAMA,
                                      TPP.PORCENTAJE
								FROM SGS.T_SORTEO TS,
									  SGS.T_PROGRAMA TP,
									  SGS.T_PROGRAMA_PREMIOS TPP,
									  SGS.T_PREMIO_DESCRIPCION TPD,
									  SGS.T_DESCRIPCION_ESPECIAS  TDE
									WHERE TS.ID_PROGRAMA  		= TP.ID_PROGRAMA
									AND TPP.ID_PROGRAMA   		= TP.ID_PROGRAMA
									AND TPD.ID_PREMIO_DESC 		= TPP.ID_DESCRIPCION
									AND TPP.PREMIO_ID_ESPECIAS  = TDE.ID_DESCRIPCION_ESPECIA(+)
									AND TS.SORTEO         		= ?
									AND TS.ID_JUEGO       		= ?
									ORDER BY ID_DESCRIPCION", array($sorteo, $id_juego));

} catch (exception $e) {die($db->ErrorMsg());}
$pdf->SetX(10);
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(50, 4, 'CATEGORIA PREMIO', 1, 0, 'L', 1);
$pdf->Cell(60, 4, 'PORCENTAJE', 1, 1, 'L', 1);
$pdf->SetFont('Arial', '', 7);
$id_programa = null;
while ($row_programa_premios = $rs_programa_premios->FetchNextObject($toupper = true)) {
    $premio = is_numeric($row_programa_premios->PREMIO) ? number_format($row_programa_premios->PREMIO, 0, ',', '.') : $row_programa_premios->PREMIO;
    $pdf->SetX(10);
    $pdf->Cell(50, 4, $row_programa_premios->DESCRIPCION, 'B', 0, 'L');
    $pdf->Cell(60, 4, $row_programa_premios->PORCENTAJE . ' %', 'B', 1, 'C');
    $id_programa = $row_programa_premios->ID_PROGRAMA;
}
$pdf->ln(4);

//$db->debug=true;
try {
    $rs_extracciones = sql("   SELECT te.orden,te.posicion,te.numero,TE.SORTEO_ASOC
                FROM SGS.T_EXTRACCION te
                WHERE te.SORTEO=?
                AND te.ID_JUEGO=?
                AND te.ZONA_JUEGO=1
                AND TE.SORTEO_ASOC LIKE '%QUINIELA ASOCIADA%'
        ORDER BY te.zona_juego desc ,te.ORDEN DESC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(100, 4, 'QUINIELA ASOCIADA', 1, 1, 'L', 1);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(20, 5, 'POSICION', 1, 0, 'C', 1);
$pdf->Cell(20, 5, 'ENTERO', 1, 0, 'C', 1);
$pdf->Cell(60, 5, 'EXTRAIDO', 1, 1, 'C', 1);
$pdf->SetFont('Arial', '', 7);
while ($row_extraccion = $rs_extracciones->FetchNextObject($toupper = true)) {
    $pdf->Cell(20, 5, $row_extraccion->POSICION, 'B', 0, 'C');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 2, "0", STR_PAD_LEFT), 'B', 0, 'C');
    $pdf->Cell(60, 5, ($row_extraccion->SORTEO_ASOC), 'B', 1, 'L');
}
$pdf->ln(1);
try {
    $rs_recaudacion = sql(" SELECT
                                    RECAUDACION,
                                    TOTAL_PREMIOS_8_ACIERTOS,
                                    TOTAL_PREMIOS_7_ACIERTOS,
                                    TOTAL_PREMIOS_6_ACIERTOS
                            FROM
                                KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA
                            WHERE SORTEO = ?
                            AND ID_JUEGO = ?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}
$row_recaudacion = $rs_recaudacion->FetchNextObject($toupper = true);
$pdf->SetFont('Arial', 'B', 9);
$pdf->Cell(50, 4, 'RECAUDACION', 1, 1, 'C', 1);
$pdf->Cell(50, 5, '$ ' . number_format($row_recaudacion->RECAUDACION, 2, ',', '.'), 'B', 1, 'R');
$pdf->Cell(50, 5, '8 ACIERTOS', 1, 0, 'C', 1);
$pdf->Cell(50, 5, '7 ACIERTOS', 1, 0, 'C', 1);
$pdf->Cell(50, 5, '6 ACIERTOS', 1, 1, 'C', 1);

$pdf->Cell(50, 5, '$ ' . number_format($row_recaudacion->TOTAL_PREMIOS_8_ACIERTOS, 2, ',', '.'), 'B', 0, 'R');
$pdf->Cell(50, 5, '$ ' . number_format($row_recaudacion->TOTAL_PREMIOS_7_ACIERTOS, 2, ',', '.'), 'B', 0, 'R');
$pdf->Cell(50, 5, '$ ' . number_format($row_recaudacion->TOTAL_PREMIOS_6_ACIERTOS, 2, ',', '.'), 'B', 1, 'R');

$pdf->Output();
