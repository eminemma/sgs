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

try {
    $rs_parametro = sql(" SELECT
                            ID_COMPARTIDO,
                            PARAMETRO,
                            decode(VALOR,'N','Carga un Operador','Carga dos Operadores') as VALOR
                        FROM
                            SGS.T_PARAMETRO_COMPARTIDO
                        WHERE PARAMETRO = 'CARGADOBLE'");
    if ($row_parametro = $rs_parametro->FetchNextObject($toupper = true)) {
        $valor = $row_parametro->VALOR;
    }
} catch (exception $e) {
    die($db->ErrorMsg());
}
if ($row_modalidad = $rs_modalidad->FetchNextObject($toupper = true)) {
    $fecha_importacion = $row_modalidad->FECHA_IMPORTACION;
}
try {
    $rs_sorteo = sql("SELECT distinct to_char(FECHA_SORTEO,'dd/mm/yyyy') as fecha_sorteo,
  							(	select initCap(us.descripcion)
								from superusuario.usuarios us
								where us.id_usuario=ts.id_jefe) as JEFE,
							(	select initCap(us1.descripcion)
								from superusuario.usuarios us1
								where us1.id_usuario=ts.id_operador) as OPERADOR,
							initcap(te.descripcion) as escribano,ts.monto_fraccion,
  							TO_CHAR(ts.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY') AS FECHA_CADUCIDAD,
							lpad(ts.sorteo,5,'0') as nro
					 	FROM 	sgs.T_SORTEO ts,
					   			sgs.t_escribano te,
								superusuario.usuarios us
						WHERE 	ts.id_escribano=te.id_escribano
							and sorteo=?
							and id_juego=?", array($sorteo, $id_juego));
} catch (exception $e) {die($db->ErrorMsg());}
if ($row_sorteo = $rs_sorteo->FetchNextObject($toupper = true)) {
    $fecha_sorteo = $row_sorteo->FECHA_SORTEO;
    $escribano    = (is_null($row_sorteo->ESCRIBANO)) ? 'Sin Escribano' : utf8_decode($row_sorteo->ESCRIBANO);
    $jefe         = (is_null($row_sorteo->JEFE)) ? 'Sin Jefe de Sorteo' : utf8_decode($row_sorteo->JEFE);
    $fecha_caduca = $row_sorteo->FECHA_CADUCIDAD;
    //$estado_sorteo=$row_sorteo->ESTADO_SORTEO;
    $monto_fraccion = $row_sorteo->MONTO_FRACCION;
    $sorteo         = $row_sorteo->NRO;
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
$pdf->SetFont('Arial', 'IB', 10);
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
if ($_SESSION['id_juego'] != 2) {
    $pdf->SetXY(20, 75);
    $pdf->Cell(30, 8, 'Monto de Fraccion:', 0, 0, 'L');
    $pdf->SetXY(70, 75);
    $pdf->Cell(30, 8, '$' . number_format((float) str_replace(',', '.', str_replace('.', '', $monto_fraccion)), 2, ',', '.'), 0, 1, 'L');
}
$pdf->SetXY(20, 80);
$pdf->Cell(30, 8, 'Fecha de Caducidad: ', 0, 0, 'L');
$pdf->SetXY(70, 80);
$pdf->Cell(30, 8, $fecha_caduca, 0, 1, 'L');
$pdf->SetXY(20, 85);
$pdf->Cell(30, 8, 'Tipo de Carga: ', 0, 0, 'L');
$pdf->SetXY(70, 85);
$pdf->Cell(30, 8, $valor, 0, 1, 'L');
$pdf->SetFont('Arial', 'B', 7);
$pdf->SetXY(10, 95);
$pdf->Cell(190, 4, 'PROGRAMA DE PREMIOS', 1, 1, 'L', 1);
try {

    $rs_programa_premios = sql("	SELECT ID_DESCRIPCION                                               AS POSICION,
									  TPD.DESCRIPCION                                                   AS DESCRIPCION,
									    CASE when ID_DESCRIPCION <= 20 THEN 'TRADICIONAL'
										else 'EXTRAORDINARIO'
									END AS TIPO,
									  TPP.SALE_O_SALE,
									  DECODE(TPP.PREMIO_EFECTIVO,NULL,TDE.DESCRIPCION_ESPECIA,TPP.PREMIO_EFECTIVO) AS PREMIO,
                                      TP.ID_PROGRAMA
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
$pdf->Cell(50, 4, 'PREMIO', 1, 0, 'L');
$pdf->Cell(40, 4, 'TIPO PREMIO', 1, 0, 'L');
$pdf->Cell(80, 4, 'PREMIO', 1, 0, 'L');
$pdf->Cell(10, 4, 'PO', 1, 0, 'L');
$pdf->Cell(10, 4, 'S/S', 1, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$id_programa = null;
while ($row_programa_premios = $rs_programa_premios->FetchNextObject($toupper = true)) {
    $premio = is_numeric($row_programa_premios->PREMIO) ? number_format($row_programa_premios->PREMIO, 0, ',', '.') : $row_programa_premios->PREMIO;
    $pdf->SetX(10);
    $pdf->Cell(50, 4, $row_programa_premios->DESCRIPCION, 1, 0, 'L');
    $pdf->Cell(40, 4, $row_programa_premios->TIPO, 1, 0, 'L');
    $pdf->Cell(80, 4, $premio, 1, 0, 'R');
    $pdf->Cell(10, 4, $row_programa_premios->POSICION, 1, 0, 'C');
    $pdf->Cell(10, 4, $row_programa_premios->SALE_O_SALE, 1, 1, 'L');
    $id_programa = $row_programa_premios->ID_PROGRAMA;
}
$pdf->ln(4);
try {
    $rs_pp_anticipada = sql("	SELECT SEMANA,PREMIO,ORDEN FROM SGS.T_ANTICIPADA WHERE SORTEO = ? AND ID_JUEGO = ? ORDER BY SEMANA,ORDEN", array($sorteo, $id_juego));
} catch (exception $e) {die($db->ErrorMsg());}
if ($rs_pp_anticipada->RecordCount() > 0) {
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(160, 4, 'PROGRAMA DE PREMIOS ANTICIPADOS', 1, 1, 'L', 1);
    $pdf->SetX(10);
    $pdf->Cell(20, 4, 'SEMANA', 1, 0, 'C');
    $pdf->Cell(20, 4, 'ORDEN', 1, 0, 'C');
    $pdf->Cell(120, 4, 'PREMIO', 1, 1, 'L');
    $pdf->SetFont('Arial', '', 7);

    while ($row_pp_anticipada = $rs_pp_anticipada->FetchNextObject($toupper = true)) {
        $pdf->SetX(10);
        $pdf->Cell(20, 4, $row_pp_anticipada->SEMANA, 1, 0, 'C');
        $pdf->Cell(20, 4, $row_pp_anticipada->ORDEN, 1, 0, 'C');
        $pdf->Cell(120, 4, utf8_decode($row_pp_anticipada->PREMIO), 1, 1, 'L');
    }
}
$pdf->ln(4);
try {
    $rs_conformacion = sql("  SELECT TPD.DESCRIPCION, TP.DESCRIPCION_ESPECIA,TD.IMPORTE
              FROM SGS.T_PROGRAMA_ANEXO_CABECERA TC,
                   SGS.T_PROGRAMA_ANEXO_DETALLE TD,
                   SGS.T_DESCRIPCION_ESPECIAS TP,
                   SGS.T_PREMIO_DESCRIPCION TPD
              WHERE TC.ID_ANEXO = TD.ID_ANEXO
              AND TD.ID_ESPECIE = TP.ID_DESCRIPCION_ESPECIA
              AND TD.ID_DESCRIPCION_PREMIO = TPD.ID_PREMIO_DESC
              AND TC.ID_PROGRAMA = ?
              ORDER BY TPD.ID_PREMIO_DESC ASC,TD.IMPORTE DESC", array($id_programa));
} catch (exception $e) {die($db->ErrorMsg());}
if ($rs_conformacion->RecordCount() > 0) {
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(150, 4, 'CONFORMACION DE PREMIOS', 1, 1, 'L', 1);
    $pdf->SetX(10);
    $pdf->Cell(50, 4, 'PREMIO', 1, 0, 'L');
    $pdf->Cell(80, 4, 'ESPECIE', 1, 0, 'L');
    $pdf->Cell(20, 4, 'IMPORTE', 1, 1, 'L');
    $pdf->SetFont('Arial', '', 7);

    while ($row_conformacion = $rs_conformacion->FetchNextObject($toupper = true)) {
        $pdf->SetX(10);
        $pdf->Cell(50, 4, $row_conformacion->DESCRIPCION, 1, 0, 'L');
        $pdf->Cell(80, 4, utf8_decode($row_conformacion->DESCRIPCION_ESPECIA), 1, 0, 'L');
        $pdf->Cell(20, 4, number_format($row_conformacion->IMPORTE, 0, ',', '.'), 1, 1, 'R');
    }
}

$pdf->ln(4);
$pdf->SetX(10);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(80, 4, 'CANTIDAD DE BILLETES/FRACCION IMPORTADOS: ', 1, 1, 'L', 1);
$pdf->Cell(80, 4, number_format($cantidad, 0, ',', '.'), 1, 0, 'L');
$pdf->SetFont('Arial', '', 7);
try {
    $rs_modalidad = sql("	SELECT COUNT(*) AS CANTIDAD,MODALIDAD,to_char(min(FECHA_IMPORTACION),'dd/mm/yyyy hh24:mi:ss') as FECHA_IMPORTACION
						FROM SGS.T_BILLETES_PARTICIPANTES
						WHERE SORTEO = ?
						GROUP BY MODALIDAD
						ORDER BY COUNT(*) desc", array($sorteo));

} catch (exception $e) {
    die($db->ErrorMsg());
}
$pdf->ln(6);
$fecha_importacion = '';
$p                 = 0;
$pdf->SetFont('Arial', 'B', 7);
while ($row_modalidad = $rs_modalidad->FetchNextObject($toupper = true)) {
    if ($p == 0) {
        $pdf->Cell(100, 4, 'MODALIDAD', 1, 1, 'L', 1);
        $p = 1;
    }
    $pdf->SetFont('Arial', '', 7);
    $fecha_importacion = $row_modalidad->FECHA_IMPORTACION;

    $pdf->Cell(60, 4, $row_modalidad->MODALIDAD, 1, 0, 'L');
    $pdf->Cell(40, 4, number_format($row_modalidad->CANTIDAD, 0, ',', '.'), 1, 1, 'L');
}
if ($rs_modalidad->RecordCount() > 0) {
    $pdf->Cell(100, 4, '* La venta empleado por modalidad electronica se incluye en la Venta Contado Casa Central', 0, 0, 'L');
}
$rs              = sql('SELECT FRACCIONES FROM SGS.T_SORTEO WHERE SORTEO = ? AND ID_JUEGO = ? ', array($sorteo, $id_juego));
$row_sorteo      = siguiente($rs);
$cant_fracciones = (int) $row_sorteo->FRACCIONES;

if ($_SESSION['juego_tipo'] == 'EXTRAORDINARIA' && $fecha_sorteo == date('d/m/Y')) {
    try {
        $rs_enteros = sql(" SELECT
                                COUNT(distinct billete) AS CANTIDAD
                            FROM SGS.T_BILLETES_PARTICIPANTES
                            WHERE SORTEO=?
                            AND ID_JUEGO=?
                            AND PARTICIPA_ENTERO='SI'", array($sorteo, $id_juego));
    } catch (exception $e) {
        die($db->ErrorMsg());
    }
    $row = $rs_enteros->FetchNextObject($toupper = true);
    $pdf->ln(5);
    $pdf->SetX(10);
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(100, 4, 'CANTIDAD DE BILLETES QUE PARTICIPAN EN SORTEO POR ENTERO:', 1, 1, 'L', 1);
    $pdf->SetFont('Arial', '', 7);
    $pdf->Cell(100, 4, number_format($row->CANTIDAD, 0, ',', '.'), 1, 0, 'L');
}
$pdf->Output();
