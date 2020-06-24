<?php
include_once dirname(__FILE__) . '/../../db.php';
require dirname(__FILE__) . '/../../sorteo/acta/header_listado.php';

$id_programa = $_GET['id_programa'];
try {

    $rs_programa_premios = sql("	SELECT ID_DESCRIPCION                                               AS POSICION,
									                       TPD.DESCRIPCION                                                   AS DESCRIPCION,
									                       CASE when ID_DESCRIPCION <= 20 THEN 'TRADICIONAL'
										                     else 'EXTRAORDINARIO'
                        									END AS TIPO,
                        									  TPP.SALE_O_SALE,
                        									  DECODE(TPP.PREMIO_EFECTIVO,NULL,TDE.DESCRIPCION_ESPECIA,TPP.PREMIO_EFECTIVO) AS PREMIO,
                                                              TP.ID_PROGRAMA,tp.DESCRIPCION as PROGRAMA,
                                      TPP.PORCENTAJE
								FROM SGS.T_PROGRAMA TP,
									  SGS.T_PROGRAMA_PREMIOS TPP,
									  SGS.T_PREMIO_DESCRIPCION TPD,
									  SGS.T_DESCRIPCION_ESPECIAS  TDE
									WHERE TPP.ID_PROGRAMA   		= TP.ID_PROGRAMA
									AND TPD.ID_PREMIO_DESC 		= TPP.ID_DESCRIPCION
									AND TPP.PREMIO_ID_ESPECIAS  = TDE.ID_DESCRIPCION_ESPECIA(+)
                  					AND TP.ID_PROGRAMA = ?
									ORDER BY ID_DESCRIPCION", array($id_programa));

} catch (exception $e) {die($db->ErrorMsg());}
$row_programa_premios = $rs_programa_premios->FetchNextObject($toupper = true);
$titulo               = strtoupper('PROGRAMA DE PREMIOS "' . utf8_decode($row_programa_premios->PROGRAMA)) . '"';
$pdf                  = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFillColor(200, 200, 200);
$pdf->SetFont('Arial', 'B', 8);
$pdf->SetXY(10, 43);
$pdf->Cell(110, 4, 'PROGRAMA DE PREMIOS', 1, 1, 'L', 1);

$pdf->SetX(10);
$pdf->Cell(50, 4, 'CATEGORIA PREMIO', 1, 0, 'L', 1);
$pdf->Cell(60, 4, 'PORCENTAJE', 1, 1, 'L', 1);
$pdf->SetFont('Arial', '', 8);
$id_programa = null;
$rs_programa_premios->MoveFirst();
while ($row_programa_premios = $rs_programa_premios->FetchNextObject($toupper = true)) {
    $premio = is_numeric($row_programa_premios->PREMIO) ? number_format($row_programa_premios->PREMIO, 0, ',', '.') : $row_programa_premios->PREMIO;
    $pdf->SetX(10);
    $pdf->Cell(50, 4, $row_programa_premios->DESCRIPCION, 'B', 0, 'L');
    $pdf->Cell(60, 4, $row_programa_premios->PORCENTAJE . ' %', 'B', 1, 'C');
    $id_programa = $row_programa_premios->ID_PROGRAMA;
}
$pdf->ln(4);

$pdf->ln(4);
try {
    $rs_extracciones = sql("   SELECT te.orden,te.posicion,te.numero,TE.SORTEO_ASOC,VALIDO
                FROM SGS.T_EXTRACCION te
                WHERE te.SORTEO=?
                AND te.ID_JUEGO=?
                AND te.ZONA_JUEGO=1
                AND (TE.SORTEO_ASOC LIKE '%QUINIELA ASOCIADA%' OR TE.SORTEO_ASOC LIKE '%QUINIELA DUPLICADO%')
        ORDER BY te.zona_juego desc ,te.ORDEN DESC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(90, 4, 'QUINIELA ASOCIADA ' . $quiniela_asoc, 1, 1, 'L', 1);
$pdf->SetFont('Arial', 'B', 7);
$pdf->Cell(20, 5, 'POSICION', 1, 0, 'C', 1);
$pdf->Cell(20, 5, 'NUMERO', 1, 0, 'C', 1);
$pdf->Cell(20, 5, 'TERMINACION', 1, 0, 'C', 1);
$pdf->Cell(30, 5, 'ESTADO', 1, 1, 'C', 1);
$pdf->SetFont('Arial', '', 7);
while ($row_extraccion = $rs_extracciones->FetchNextObject($toupper = true)) {
    $pdf->Cell(20, 5, $row_extraccion->POSICION, 'B', 0, 'C');
    $pdf->Cell(20, 5, getStringBetween($row_extraccion->SORTEO_ASOC, '(', ')'), 'B', 0, 'C');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 2, "0", STR_PAD_LEFT), 'B', 0, 'C');
    $pdf->Cell(30, 5, ($row_extraccion->VALIDO == 'S' ? 'Valido' : ($row_extraccion->VALIDO == 'D' ? 'Duplicado' : '')), 'B', 1, 'C');

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
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(50, 4, 'RECAUDACION', 1, 1, 'C', 1);
$pdf->Cell(50, 5, '$ ' . number_format($row_recaudacion->RECAUDACION, 2, ',', '.'), 'B', 1, 'R');
$pdf->Cell(50, 5, '8 ACIERTOS', 1, 0, 'C', 1);
$pdf->Cell(50, 5, '7 ACIERTOS', 1, 0, 'C', 1);
$pdf->Cell(50, 5, '6 ACIERTOS', 1, 1, 'C', 1);

$pdf->Cell(50, 5, '$ ' . number_format($row_recaudacion->TOTAL_PREMIOS_8_ACIERTOS, 2, ',', '.'), 'B', 0, 'R');
$pdf->Cell(50, 5, '$ ' . number_format($row_recaudacion->TOTAL_PREMIOS_7_ACIERTOS, 2, ',', '.'), 'B', 0, 'R');
$pdf->Cell(50, 5, '$ ' . number_format($row_recaudacion->TOTAL_PREMIOS_6_ACIERTOS, 2, ',', '.'), 'B', 1, 'R');

$pdf->Output();

function getStringBetween($str, $from, $to)
{
    $sub = substr($str, strpos($str, $from) + strlen($from), strlen($str));
    return substr($sub, 0, strpos($sub, $to));
}
