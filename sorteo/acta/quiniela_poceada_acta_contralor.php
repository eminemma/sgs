<?php
session_start();
//var_dump($_SESSION);
include_once dirname(__FILE__) . '/../../db.php';

$titulo = strtoupper('Extracto  ' . $_SESSION['juego']);

$titulo2 = strtoupper('SORTEO ' . $_SESSION['sorteo']);

require "header_listado.php";
//require dirname(__FILE__).'/../../librerias/pdf/fpdf.php';
$conPie = false;
$pdf    = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();
try {
    $rs_sorteo = sql("  SELECT  TE.DESCRIPCION AS ESCRIBANO,
                                TOP.DESCRIPCION     AS OPERADOR,
                                TJ.DESCRIPCION      AS JEFE,
                                TJT.DESCRIPCION AS TIPO_JUEGO,
                                TO_CHAR(TS.FECHA_SORTEO,'dd/mm/YYYY') as FECHA_SORTEO,
                                TO_CHAR(TS.FECHA_SORTEO,'HH24:MI:SS') AS HORA_SORTEO,
                                TO_CHAR(TS.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY') AS FECHA_CADUCIDAD,
                                TS.SORTEO
                        FROM    SGS.T_SORTEO TS,
                                SGS.T_ESCRIBANO TE,
                                SUPERUSUARIO.USUARIOS TOP,
                                SUPERUSUARIO.USUARIOS TJ,
                                SGS.T_JUEGO_TIPO TJT
                        WHERE TS.ID_ESCRIBANO     = TE.ID_ESCRIBANO
                            AND TS.ID_OPERADOR    = TOP.ID_USUARIO
                            AND TS.ID_JEFE        = TJ.ID_USUARIO
                            AND TS.ID_TIPO_JUEGO  = TJT.ID_JUEGO_TIPO
                            AND TS.SORTEO         = ?
                            AND TS.ID_JUEGO       = ?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {
    die($db->ErrorMsg());
}

try {
    $rs_extracciones = sql("    SELECT TD.DESCRIPCION,LPAD(TE.NUMERO,2,0) AS NUMERO,TE.POSICION
                        FROM    SGS.T_EXTRACCION TE,
                                SGS.T_PREMIO_DESCRIPCION TD
                        WHERE TE.POSICION =TD.ID_PREMIO_DESC
                            AND TE.SORTEO = ?
                            AND TE.ID_JUEGO = ?
                            AND (SORTEO_ASOC LIKE ('%QUINIELA ASOCIADA%') OR SORTEO_ASOC LIKE ('%VALIDA%'))
                        ORDER BY TE.POSICION", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {
    die($db->ErrorMsg());
}

$zy         = 110;
$zy1        = 110;
$x          = 25;
$xx         = 110;
$row_sorteo = $rs_sorteo->FetchNextObject($toupper = true);

$pdf->SetFont('Times', '', 11);
$pdf->SetXY(120, 40);
$pdf->Cell(30, 5, 'Sorteo Nro:', 0, 0, 'R');

$pdf->SetXY(150, 40);
$pdf->Cell(30, 5, $_SESSION['sorteo'], 1, 0, 'C');

$pdf->SetXY(120, 45);
$pdf->Cell(30, 5, 'Fecha:', 0, 0, 'R');

$pdf->SetXY(150, 45);
$pdf->Cell(30, 5, $row_sorteo->FECHA_SORTEO, 1, 0, 'C');

$pdf->SetXY(120, 50);
$pdf->Cell(30, 5, 'Hora:', 0, 0, 'R');

$pdf->SetXY(150, 50);
$pdf->Cell(30, 5, '    :     ', 1, 0, 'C');

$pdf->SetXY(120, 55);
$pdf->Cell(30, 5, 'Caducidad:', 0, 0, 'R');

$pdf->SetXY(150, 55);
$pdf->Cell(30, 5, $row_sorteo->FECHA_CADUCIDAD, 1, 0, 'C');

$fechasorteo = $row_sorteo->FECHA_SORTEO;
$escribano   = $row_sorteo->ESCRIBANO;

$pdf->SetFont('Times', '', 11);
$pdf->SetXY(25, 62);
$pdf->MultiCell(160, 5, utf8_decode($texto1), 0, 'J', 0, 0);

$pdf->SetXY(25, 127);
try {
    $rs_extracciones_comp = sql("SELECT * FROM(
                                        SELECT
                                            *
                                        FROM
                                            SGS.T_EXTRACCION TE
                                        WHERE
                                            TE.SORTEO = ?
                                            AND TE.ID_JUEGO = ?
                                            and valido ='D'
                                            AND TE.SORTEO_ASOC  LIKE '%QUINIELA DUPLICADO%'
                                        UNION ALL
                                        SELECT
                                            *
                                        FROM
                                            SGS.T_EXTRACCION TE
                                        WHERE
                                            TE.SORTEO = ?
                                            AND TE.ID_JUEGO = ?
                                            AND TE.ZONA_JUEGO = 1
                                            AND ( TE.SORTEO_ASOC NOT LIKE '%QUINIELA ASOCIADA%'
                                                  AND TE.SORTEO_ASOC NOT LIKE '%QUINIELA DUPLICADO%' )
                                )
                                ORDER BY POSICION,fecha_extraccion ASC", array($_SESSION['sorteo'], $_SESSION['id_juego'], $_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {
    die($db->ErrorMsg());
}

$pdf->SetXY(45, 70);

$y_inicio = 70;
while ($row = $rs_extracciones->FetchNextObject($toupper = true)) {

    if ($jj == 5) {
        $x += 40;
        $jj = 0;
        $pdf->SetY(70);
        $pdf->SetX($x);
    }
    $pdf->SetX($x);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(20, 5, ucwords(strtolower('POSICION ' . $row->POSICION)), 1, 0, 'C');
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(20, 5, $row->NUMERO, 1, 1, 'C');
    $jj += 1;
}

$res_rec = sql(
    "SELECT
				    TOTAL_PREMIOS_8_ACIERTOS,
				    TOTAL_PREMIOS_7_ACIERTOS,
				    TOTAL_PREMIOS_6_ACIERTOS,
				    (SELECT COUNT(*) FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA WHERE SORTEO=REC.SORTEO AND ID_JUEGO=REC.ID_JUEGO AND ID_DESCRIPCION = 82) AS CANTIDAD_GANADORES_8,
				    (SELECT COUNT(*) FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA WHERE SORTEO=REC.SORTEO AND ID_JUEGO=REC.ID_JUEGO AND ID_DESCRIPCION = 83) AS CANTIDAD_GANADORES_7,
				    (SELECT COUNT(*) FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA WHERE SORTEO=REC.SORTEO AND ID_JUEGO=REC.ID_JUEGO AND ID_DESCRIPCION = 84) AS CANTIDAD_GANADORES_6
				FROM
				    KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA REC
				WHERE
				    SORTEO       = ?
				    AND ID_JUEGO = ?",
    array($_SESSION['sorteo'], $_SESSION['id_juego'])
);

$row_rec = siguiente($res_rec);

$pozo_8_aciertos = (($row_rec->CANTIDAD_GANADORES_8 == 0) ? 'Pozo Vacante con 8 Aciertos' : ($row_rec->CANTIDAD_GANADORES_8 > 1 ? $row_rec->CANTIDAD_GANADORES_8 . ' Ganadores con $' . number_format(($row_rec->TOTAL_PREMIOS_8_ACIERTOS / $row_rec->CANTIDAD_GANADORES_8), 2, ',', '.') . ' c/u, 8 Aciertos  ' : $row_rec->CANTIDAD_GANADORES_8 . ' Ganador con $' . number_format(($row_rec->TOTAL_PREMIOS_8_ACIERTOS / $row_rec->CANTIDAD_GANADORES_8), 2, ',', '.') . ', 8 Aciertos  '));
$pozo_7_aciertos = (($row_rec->CANTIDAD_GANADORES_7 == 0) ? 'Pozo Vacante con 7 Aciertos' : ($row_rec->CANTIDAD_GANADORES_7 > 1 ? $row_rec->CANTIDAD_GANADORES_7 . ' Ganadores con $' . number_format(($row_rec->TOTAL_PREMIOS_7_ACIERTOS / $row_rec->CANTIDAD_GANADORES_7), 2, ',', '.') . ' c/u, 7 Aciertos' : $row_rec->CANTIDAD_GANADORES_7 . ' Ganador con $' . number_format(($row_rec->TOTAL_PREMIOS_7_ACIERTOS / $row_rec->CANTIDAD_GANADORES_7), 2, ',', '.') . ', 7 Aciertos  '));
$pozo_6_aciertos = (($row_rec->CANTIDAD_GANADORES_6 == 0) ? 'Pozo Vacante con 6 Aciertos' : ($row_rec->CANTIDAD_GANADORES_6 > 1 ? $row_rec->CANTIDAD_GANADORES_6 . ' Ganadores con $' . number_format(($row_rec->TOTAL_PREMIOS_6_ACIERTOS / $row_rec->CANTIDAD_GANADORES_6), 2, ',', '.') . ' c/u, 6 Aciertos ' : $row_rec->CANTIDAD_GANADORES_6 . ' Ganador con $' . number_format(($row_rec->TOTAL_PREMIOS_6_ACIERTOS / $row_rec->CANTIDAD_GANADORES_6), 2, ',', '.') . ', 6 Aciertos  '));
$pdf->ln(10);
$pdf->SetX(25);
$pdf->Cell(160, 5, 'Ganadores', 1, 1, 'C');
$pdf->SetX(25);
$pdf->Cell(160, 5, $pozo_8_aciertos, 1, 1, 'C');
$pdf->SetX(25);
$pdf->Cell(160, 5, $pozo_7_aciertos, 1, 1, 'C');
$pdf->SetX(25);
$pdf->Cell(160, 5, $pozo_6_aciertos, 1, 1, 'C');

$pdf->Output(dirname(__FILE__) . '/reporte_contralor_' . $_SESSION['sorteo'] . '_' . date('dmY') . '.pdf', 'F');
