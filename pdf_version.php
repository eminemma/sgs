<?php
//error_reporting(E_ALL);
session_start();
//var_dump($_SESSION);
include_once dirname(__FILE__) . '/db.php';

$titulo = strtoupper('Detalle de Version (SORTEOS)');

require dirname(__FILE__) . "/sorteo/acta/header_listado.php";

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

try {
    $rs = sql("	SELECT TV.VERSION, TV.DETALLE, TV.HASH, to_char(TV.FECHA_VERSION,'dd/mm/yyyy hh24:mi:ss') as FECHA_VERSION,  TV.ID, COUNT(*) AS OBJETOS
    					FROM SGS.T_VERSION TV, SGS.T_VERSION_DETALLE TD
						WHERE TV.ID = TD.ID_VERSION
						AND TV.ID = ?
						GROUP BY TV.VERSION, TV.DETALLE, TV.HASH, TV.FECHA_VERSION,  TV.ID", array($_GET['id']));
} catch (exception $e) {
    die($db->ErrorMsg());
}
$row = siguiente($rs);
$pdf->SetFont('Times', 'I', 12);
$pdf->Cell(70, 10, 'VERSION:', 0);
$pdf->Cell(110, 10, $row->VERSION, 'B', 1);
$pdf->Cell(70, 10, 'DETALLE:', 0);
$pdf->Cell(110, 10, $row->DETALLE, 'B', 1);
$pdf->Cell(70, 10, 'CANTIDAD DE OBJETOS:', 0);
$pdf->Cell(110, 10, $row->OBJETOS, 'B', 1);
$pdf->Cell(70, 10, 'HASH:', 0);
$pdf->Cell(110, 10, $row->HASH, 'B', 1);
$pdf->Cell(70, 10, 'FECHA VERSION:', 0);
$pdf->Cell(110, 10, $row->FECHA_VERSION, 'B', 1);

$pdf->SetFont('Times', 'B', 9);
$pdf->SetXY(25, 263);
$pdf->Cell(150, 5, '__________________________                                               ____________________________', 0, 1, 'J');
$pdf->SetXY(25, 268);
$pdf->Cell(150, 5, '          Responsable Sistema                                                                 Responsable Sistemas', 0, 0, 'J');
$titulo = strtoupper('Detalle de Version (SISTEMAS)');
$pdf->AddPage();
$pdf->SetFont('Times', 'I', 12);
$pdf->Cell(70, 10, 'VERSION:', 0);
$pdf->Cell(110, 10, $row->VERSION, 'B', 1);
$pdf->Cell(70, 10, 'DETALLE:', 0);
$pdf->Cell(110, 10, $row->DETALLE, 'B', 1);
$pdf->Cell(70, 10, 'CANTIDAD DE OBJETOS:', 0);
$pdf->Cell(110, 10, $row->OBJETOS, 'B', 1);
$pdf->Cell(70, 10, 'HASH:', 0);
$pdf->Cell(110, 10, $row->HASH, 'B', 1);
$pdf->Cell(70, 10, 'FECHA VERSION:', 0);
$pdf->Cell(110, 10, $row->FECHA_VERSION, 'B', 1);

$pdf->SetFont('Times', 'B', 9);
$pdf->SetXY(25, 263);
$pdf->Cell(150, 5, '__________________________                                               ____________________________', 0, 1, 'J');
$pdf->SetXY(25, 268);
$pdf->Cell(150, 5, '          Responsable Sistema                                                                 Responsable Sistemas', 0, 0, 'J');
$pdf->Output();
