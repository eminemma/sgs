<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';

require 'header_listado.php';

$serie           = $_SESSION['serie'];
$titulo          = 'Billetes Participantes del Sorteo de Enteros';
$titulo2         = 'Juego: ' . $_SESSION['juego'] . ' ' . $_SESSION['descripcion_sorteo'] . ' - Sorteo: ' . $_SESSION['sorteo'];
$tamaño_billete = 5;
$cant_fracciones = 10;

try {
    $rs_enteros = sql(" SELECT BILLETE
									FROM  SGS.T_BILLETES_PARTICIPANTES
									WHERE SORTEO   = ?
									  AND ID_JUEGO = ?
									  AND PARTICIPA_ENTERO = 'SI'
									GROUP BY BILLETE
									ORDER BY BILLETE", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {
    die($db->ErrorMsg());
}

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

$salto_pagina = 275;
//$salto_pagina=265;
$pri           = 'NO';
$subt          = 0;
$tot           = 0;
$dx            = 0;
$dy            = 0;
$divisorentero = 0;
$rs_enteros->MoveFirst();
$pdf->SetFont('Arial', '', 8);
$contador = 0;
$pdf->Ln(-2);
$pdf->SetFillColor(200, 200, 200);
$pdf->Cell(110, 8, 'CANTIDAD DE BILLETES/FRACCION IMPORTADOS: ', 1, 1, 'L', 1);
try {
    $rs_modalidad = sql("	SELECT COUNT(distinct billete) AS CANTIDAD,MODALIDAD,to_char(min(FECHA_IMPORTACION),'dd/mm/yyyy hh24:mi:ss') as FECHA_IMPORTACION
						FROM SGS.T_BILLETES_PARTICIPANTES
						WHERE SORTEO = ?
						AND PARTICIPA_ENTERO = 'SI'
						GROUP BY MODALIDAD
						ORDER BY COUNT(*) desc", array($_SESSION['sorteo']));

} catch (exception $e) {
    die($db->ErrorMsg());
}
$pdf->ln(10);
$fecha_importacion = '';
while ($row_modalidad = $rs_modalidad->FetchNextObject($toupper = true)) {
    $fecha_importacion = $row_modalidad->FECHA_IMPORTACION;
    $pdf->SetX(20);
    $pdf->Cell(55, 8, $row_modalidad->MODALIDAD, 1, 0, 'L');
    $pdf->Cell(40, 8, number_format($row_modalidad->CANTIDAD, 0, ',', '.'), 1, 1, 'L');
}
$pdf->SetX(20);
$pdf->Cell(95, 8, 'FECHA IMPORTACION: ' . $fecha_importacion, 1, 1, 'L');
$pdf->ln(10);

while ($row = $rs_enteros->FetchNextObject($toupper = true)) {
    if ($salto_pagina > 269) {
        $salto_pagina = 0;
        if ($pri == 'NO') {
            //$pdf->Ln(-2);
            $pdf->SetFont('Arial', 'B', 10);
            $pdf->Cell(195, 7, 'Billetes', 1, 1, 'C');
        } else {
            $pri = 'NO';
        }
    }
    $pdf->SetFont('Arial', '', 10);
    if ($contador == 12) {
        $pdf->Cell(15, 7, ltrim($row->BILLETE), 1, 1, 'C');
        $contador = 0;
        $cantidad++;
    } else {
        $pdf->Cell(15, 7, ltrim($row->BILLETE), 1, 0, 'C');
        $contador++;
        $cantidad++;
    }

    $y_line       = $pdf->GetY();
    $salto_pagina = number_format($y_line, 0, '.', ',');
}

$pdf->Ln(9);
$pdf->Cell(195, 8, 'Cantidad Total de Billetes: ' . $rs_enteros->RowCount(), 1, 1, 'C');
$pdf->Output();
