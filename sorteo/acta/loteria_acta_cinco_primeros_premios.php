<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';

require "header_listado.php";
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');

//OBTENGO DATOS DEL SORTEO
try {
    $rs_sorteo = sql("	SELECT TO_CHAR(SO.FECHA_SORTEO,'DD/MM/YYYY')       AS FECHA_SORTEO,
										jefe.descripcion                                  AS JEFE,
										operador.descripcion                              AS USUARIO,
										ESC.DESCRIPCION                                  AS ESCRIBANO,
										TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY')                      AS FECHA_CADUCIDAD
								FROM 	SGS.T_SORTEO SO,
										SGS.T_ESCRIBANO ESC,
										SUPERUSUARIO.usuarios jefe,
										SUPERUSUARIO.usuarios operador
								WHERE 	SO.ID_ESCRIBANO=ESC.ID_ESCRIBANO(+)
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

//$titulo  = strtoupper('REPORTE DE 5 PRIMEROS PREMIOS DE '.$_SESSION['juego'].' '.$_SESSION['juego_tipo']);

if ($_SESSION['sorteo'] == 4766) {
    $titulo = strtoupper('REPORTE DE 5 PRIMEROS PREMIOS DE ' . $_SESSION['juego'] . ' ORDINARIA');
} else {
    $titulo = strtoupper('REPORTE DE 5 PRIMEROS PREMIOS DE ' . $_SESSION['juego'] . ' ' . $_SESSION['juego_tipo']);
}

$titulo2 = 'EMISION ' . $_SESSION['sorteo'];
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
$pdf->SetXY(30, 80);
$pdf->SetFont('Times', 'B', 10);
try {
    $rs_fracciones = sql("SELECT tpd.descripcion,te.numero,
							DECODE(
                                    (SELECT COUNT(*) FROM sgs.t_billetes_participantes
                                    	WHERE SORTEO = te.SORTEO
                                        AND ID_JUEGO                                                    = te.ID_JUEGO
                                        AND BILLETE                                                     = te.numero
                                    ), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO
						FROM SGS.T_EXTRACCION te,sgs.t_sorteo ts,SGS.t_programa_premios tpp,SGS.t_premio_descripcion tpd
						WHERE te.posicion between 1 and 5
						and te.zona_juego=1
						and te.sorteo=ts.sorteo
						and te.id_juego=ts.id_juego
						and ts.id_programa=tpp.id_programa
						and te.posicion=tpp.id_descripcion
						and tpd.id_premio_desc=tpp.id_descripcion
						and te.sorteo=?
						and te.id_juego=?
						order by posicion", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

$pdf->SetX(24);
$pdf->Cell(80, 8, 'DETALLE DE PREMIOS', 0, 1, 'L');
$pdf->SetX(24);
$pdf->SetFont('Times', 'B', 8);
$pdf->Cell(40, 5, 'PREMIO', 1, 0, 'C');
$pdf->Cell(50, 5, 'CERTIFICADO', 1, 0, 'C');
$pdf->Cell(50, 5, 'ESTADO', 1, 1, 'C');

while ($row_fraccion = $rs_fracciones->FetchNextObject($toupper = true)) {
    $pdf->SetX(24);
    $pdf->SetFont('Times', 'B', 8);
    $pdf->Cell(40, 5, $row_fraccion->DESCRIPCION, 1, 0, 'L');
    $pdf->Cell(50, 5, 'Certificado Nro: ' . str_pad($row_fraccion->NUMERO, 5, 0, STR_PAD_LEFT), 1, 0, 'L');
    $pdf->Cell(50, 5, $row_fraccion->COMERCIALIZADO, 1, 1, 'L');
    $pdf->SetX(24);

    $distribuido = '';
    //$db->debug=true;
    try {
        $rs_dist = sql(" SELECT DESCRIPCION_AGENCIA,LOCALIDAD,ID_AGENCIA,PROVINCIA,DESCRIPCION_SUCURSAL,ID_SUCURSAL,PROVINCIA
	              FROM sgs.t_billetes_participantes
	              WHERE SORTEO = ?
	              AND ID_JUEGO = ?
	              AND BILLETE  = ?
	              group by DESCRIPCION_AGENCIA,LOCALIDAD,ID_AGENCIA,PROVINCIA,DESCRIPCION_SUCURSAL,ID_SUCURSAL,PROVINCIA
                  order by ID_SUCURSAL,ID_AGENCIA", array($_SESSION['sorteo'], $_SESSION['id_juego'], $row_fraccion->NUMERO));
    } catch (exception $e) {die($db->ErrorMsg());}

    while ($row_dist = siguiente($rs_dist)) {
        $pdf->SetX(24);

        //$distribuido = utf8_decode($row_dist->DESCRIPCION_AGENCIA);
        $distribuido = utf8_decode($row_dist->DESCRIPCION_AGENCIA);

        $distribuido = str_pad($row_dist->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . '-' . $distribuido . ' ' . str_pad($row_dist->ID_SUCURSAL, 2, "0", STR_PAD_LEFT) . '-' . $row_dist->DESCRIPCION_SUCURSAL . ' ' . $row_dist->PROVINCIA;
        if ($row_dist->DESCRIPCION_AGENCIA == 'VENTA CONTADO CASA CENTRAL') {
            $localidad   = $row_dist->PROVINCIA;
            $distribuido = '09001 - ' . $row_dist->PROVINCIA . ', ' . $localidad;
        } else if ($row_dist->DESCRIPCION_AGENCIA == 'VENTA CONTADO') {
            if ($row_dist->ID_SUCURSAL == 1) {
                $localidad = $row_dist->PROVINCIA;
            } else {
                $localidad = $row_dist->DESCRIPCION_SUCURSAL;
            }
            $distribuido = '09001 - ' . $row_dist->DESCRIPCION_SUCURSAL . ', ' . $localidad;
        }
        $pdf->MultiCell(160, 5, 'Distribuido En:' . $distribuido, 0, 1);
    }

    //$pdf->MultiCell(100,5,'Distribuido En:'.$distribuido,0,1,'C');
}
try {

    $rs_fracciones = sql("
    SELECT
        tpd.descripcion
        ,te.numero
        ,TE.FRACCION
        ,
    DECODE(
    (
    SELECT COUNT(*) FROM sgs.t_billetes_participantes
    WHERE SORTEO    = te.SORTEO
    AND ID_JUEGO    = te.ID_JUEGO
    AND BILLETE     = te.numero
    AND (FRACCION   = TE.FRACCION OR TE.POSICION=21)

    ), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO
    FROM SGS.T_EXTRACCION TE,SGS.T_SORTEO TS,SGS.T_PROGRAMA_PREMIOS TPP,SGS.T_PREMIO_DESCRIPCION TPD
    WHERE  (te.zona_juego=4 OR te.zona_juego=3)
    and te.sorteo=ts.sorteo
    and te.id_juego=ts.id_juego
    and ts.id_programa=tpp.id_programa
    AND TE.POSICION=TPP.ID_DESCRIPCION
    AND TPD.ID_PREMIO_DESC=TPP.ID_DESCRIPCION
    and te.sorteo=?
    AND TE.ID_JUEGO=?
    order by posicion", array($_SESSION['sorteo'], $_SESSION['id_juego']));
    if ($rs_fracciones->RecordCount() == 0) {
        $pdf->Output();
    }
    $titulo = strtoupper('REPORTE DE PREMIOS EXTRAORDINARIOS DE ' . $_SESSION['juego']);

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
    $pdf->SetXY(30, 80);
    $pdf->SetFont('Times', 'B', 10);

    /*
$rs_fracciones = sql("
SELECT tpd.descripcion,te.numero,
DECODE(
(SELECT COUNT(*) FROM sgs.t_billetes_participantes
WHERE SORTEO = te.SORTEO
AND ID_JUEGO                                                    = te.ID_JUEGO
AND BILLETE                                                     = te.numero
), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO
FROM SGS.T_EXTRACCION TE,SGS.T_SORTEO TS,SGS.T_PROGRAMA_PREMIOS TPP,SGS.T_PREMIO_DESCRIPCION TPD
WHERE TE.POSICION BETWEEN 21 AND 25
and (te.zona_juego=4 OR te.zona_juego=3)
and te.sorteo=ts.sorteo
and te.id_juego=ts.id_juego
and ts.id_programa=tpp.id_programa
AND TE.POSICION=TPP.ID_DESCRIPCION
AND TPD.ID_PREMIO_DESC=TPP.ID_DESCRIPCION
and te.sorteo=?
AND TE.ID_JUEGO=?
order by posicion",array($_SESSION['sorteo'], $_SESSION['id_juego']));
 */
} catch (exception $e) {
    die($db->ErrorMsg());
}

$pdf->SetX(24);
$pdf->Cell(80, 8, 'DETALLE DE PREMIOS', 0, 1, 'L');
$pdf->SetX(24);
$pdf->SetFont('Times', 'B', 8);
$pdf->Cell(45, 5, 'PREMIO', 1, 0, 'C');
$pdf->Cell(50, 5, 'BILLETE', 1, 0, 'C');
$pdf->Cell(50, 5, 'ESTADO', 1, 1, 'C');

while ($row_fraccion = $rs_fracciones->FetchNextObject($toupper = true)) {
    $pdf->SetX(24);
    $pdf->SetFont('Times', 'B', 8);
    $pdf->Cell(45, 5, $row_fraccion->DESCRIPCION, 1, 0, 'L');
    $pdf->Cell(50, 5, 'Certificado Nro: ' . str_pad($row_fraccion->NUMERO, 5, 0, STR_PAD_LEFT), 1, 0, 'L');
    $pdf->Cell(50, 5, $row_fraccion->COMERCIALIZADO, 1, 1, 'L');
    $pdf->SetX(24);

    $distribuido = '';
    //$db->debug=true;
    try {
        $rs_dist = sql(" SELECT DESCRIPCION_AGENCIA,LOCALIDAD,ID_AGENCIA,PROVINCIA,DESCRIPCION_SUCURSAL,ID_SUCURSAL,PROVINCIA
	              FROM sgs.t_billetes_participantes
	              WHERE SORTEO = ?
	              AND ID_JUEGO = ?
	              AND BILLETE  = ?
	              group by DESCRIPCION_AGENCIA,LOCALIDAD,ID_AGENCIA,PROVINCIA,DESCRIPCION_SUCURSAL,ID_SUCURSAL,PROVINCIA
                  order by ID_SUCURSAL,ID_AGENCIA", array($_SESSION['sorteo'], $_SESSION['id_juego'], $row_fraccion->NUMERO));
    } catch (exception $e) {die($db->ErrorMsg());}

    while ($row_dist = siguiente($rs_dist)) {
        $pdf->SetX(24);

        $distribuido = utf8_decode($row_dist->DESCRIPCION_AGENCIA);

        $distribuido = str_pad($row_dist->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . '-' . $distribuido . ' ' . str_pad($row_dist->ID_SUCURSAL, 2, "0", STR_PAD_LEFT) . '-' . $row_dist->DESCRIPCION_SUCURSAL . ' ' . $row_dist->PROVINCIA;

        $pdf->MultiCell(160, 5, 'Distribuido En:' . $distribuido, 0, 1);
    }

    //$pdf->MultiCell(100,5,'Distribuido En:'.$distribuido,0,1,'C');
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

$pdf->Output();
