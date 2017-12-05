<?php
//error_reporting(E_ALL);
session_start();
//var_dump($_SESSION);
include_once dirname(__FILE__) . '/db.php';

$titulo = strtoupper('Detalle de Version ');

require dirname(__FILE__) . "/sorteo/acta/header_listado.php";

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

try {
    $rs = sql("	SELECT TV.VERSION,
						TV.DETALLE,
						TD.HASH,
						TO_CHAR(TV.FECHA_VERSION,'dd/mm/yyyy hh24:mi:ss') AS FECHA_VERSION,
						TV.ID,
						TD.OBJETO,
						TD.ARCHIVO
				FROM 	SGS.T_VERSION TV,
						SGS.T_VERSION_DETALLE TD
				WHERE TV.ID = TD.ID_VERSION
					AND TV.ID   =?
					ORDER BY TD.OBJETO DESC,TD.ARCHIVO", array($_GET['id']));
} catch (exception $e) {
    die($db->ErrorMsg());
}
$pdf->SetFont('Arial', 'B', 10);
if ($rs->RecordCount() > 0) {
    $pdf->Cell(25, 4, 'OBJETO', 1, 0, 'L');
    $pdf->Cell(120, 4, 'ARCHIVO', 1, 0, 'L');
    $pdf->Cell(50, 4, 'HASH', 1, 1, 'L');
    $pdf->SetFont('Arial', '', 7);
    while ($row = $rs->FetchNextObject($toupper = true)) {
        $pdf->SetX(10);
        $pdf->Cell(25, 4, $row->OBJETO, 1, 0, 'L');
        $pdf->Cell(120, 4, $row->ARCHIVO, 1, 0, 'L');
        $pdf->Cell(50, 4, $row->HASH, 1, 1, 'L');
    }
}
$pdf->MultiCell(190, 4, 'REFERENCIA');
$pdf->MultiCell(190, 4, 'BASE DE DATOS: SE OBTIENE UN UNICO MD5 LOS SIGUIENTES OBJETOS: TABLAS, PROCEDIMIENTOS, FUNCIONES, SECUENCIAS, KEYS.  ');
$pdf->MultiCell(190, 4, utf8_decode('CODIGO FUENTE: ES UN HASH UNICO OBTENIDO DE CONCATENAR TODOS LOS HASH DE LOS ARCHIVOS DE LA APLICACIÓN MAS HASH DE BASE DE DATOS, ESTE ES EL QUE FINALMENTE RECIBE EL USUARIO  '));
$pdf->Output();
