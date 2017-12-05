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
                                      TP.ID_PROGRAMA,tp.DESCRIPCION as PROGRAMA
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
$pdf->SetFont('Arial', 'IB', 10);
$pdf->SetXY(10, 43);
$pdf->Cell(190, 4, 'PROGRAMA DE PREMIOS', 1, 1, 'L', 1);

$pdf->SetX(10);
$pdf->Cell(50, 4, 'PREMIO', 1, 0, 'L');
$pdf->Cell(40, 4, 'TIPO PREMIO', 1, 0, 'L');
$pdf->Cell(80, 4, 'PREMIO', 1, 0, 'L');
$pdf->Cell(10, 4, 'PO', 1, 0, 'L');
$pdf->Cell(10, 4, 'S/S', 1, 1, 'L');
$pdf->SetFont('Arial', '', 7);
$id_programa = null;
$rs_programa_premios->MoveFirst();
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
$pdf->Output();
