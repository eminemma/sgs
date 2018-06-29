<?php
/*error_reporting(E_ALL);
ini_set('display_errors', 1);*/
@session_start();

header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public");
include_once dirname(__FILE__) . '/../../db.php';

conectar_db();

// $db->debug=true;
if (isset($_POST['semana']) && $_POST['semana'] != 0) {
    $semana = $_POST['semana'];
} else if (isset($_GET['semana']) && $_GET['semana'] != 0) {
    $semana = $_GET['semana'];
} else {
    $semana = 1;
}

try {
    $rsgordo = sql("
SELECT
    TAG.ID_JUEGO, TAG.SORTEO, TAG.SEMANA, TAG.BILLETE, TAG.FRACCION, TAG.AGENCIA, TAG.LOCALIDAD, TAG.NOMBRE,
    TA.ID_JUEGO,TA.SORTEO,TA.SEMANA,TA.PREMIO,TA.ID_JEFE
    ,TA.ID_ESCRIBANO,TA.PRESCRIPCION,TA.PROX_SORTEO,TA.PREMIO_PROX_SORTEO
    ,to_char(TA.FECHA_SORTEO,'dd/mm/yyyy') as FECHA_SORTEO,TA.IMPORTE
    ,DECODE(TA.ID_JEFE,NULL,'SIN JEFE' ,JEFE.DESCRIPCION)        AS JEFE_SORTEO
    ,DECODE(TA.ID_ESCRIBANO,NULL,'SIN ESCRIBANO',ES.DESCRIPCION) AS ESCRIBANO
    ,(SELECT MAX(SEMANA) FROM SGS.T_ANTICIPADA WHERE ID_JUEGO=? AND SORTEO=?) CANTIDAD_SEMANAS
FROM
    SGS.T_ANTICIPADA_GANADORES TAG, T_ANTICIPADA TA, SUPERUSUARIO.USUARIOS JEFE, SGS.T_ESCRIBANO ES
WHERE
    TAG.SORTEO = TAG.SORTEO
    AND TAG.ID_JUEGO = TA.ID_JUEGO
    AND TAG.SEMANA = TA.SEMANA
    AND TAG.SORTEO=TA.SORTEO
    AND TAG.ID_JUEGO=TA.ID_JUEGO
    AND TA.SEMANA = ? AND TA.ID_JUEGO = ? AND TA.SORTEO = ?
    AND TA.ID_JEFE      = JEFE.ID_USUARIO(+)
    AND TA.ID_ESCRIBANO = ES.ID_ESCRIBANO(+)

", array($_SESSION['id_juego'], $_SESSION['sorteo'], $semana, $_SESSION['id_juego'], $_SESSION['sorteo']));

} catch (exception $e) {

    die($db->ErrorMsg());

}

while ($row = siguiente($rsgordo)) {

    $escribano        = utf8_decode($row->ESCRIBANO);
    $fecha            = $row->FECHA;
    $fecha_sorteo     = $row->FECHA_SORTEO;
    $usuario          = utf8_decode($row->JEFE_SORTEO);
    $fecha_proximo    = $row->PROX_SORTEO;
    $sorteo           = $row->SORTEO;
    $proximo_premio   = utf8_decode($row->PREMIO_PROX_SORTEO);
    $semana           = utf8_decode($row->SEMANA);
    $cantidad_semanas = utf8_decode($row->CANTIDAD_SEMANAS);

    $fecha_prescripcion = date('d/m/Y', strtotime(str_replace('/', '-', $row->PRESCRIPCION)));

    $cuenta = $cuenta + 1;
    if ($cuenta == 1) {
        /* ESTO SE HACE POR SI HAY MAS DE 1 PREMIO EN EL SORTEO */
        $billete1   = $row->BILLETE;
        $fraccion1  = $row->FRACCION;
        $agencia1   = $row->AGENCIA . ' ' . $row->NOMBRE;
        $localidad1 = $row->LOCALIDAD;
        $premio1    = $row->PREMIO;
    }
    if ($cuenta == 2) {
        $billete2   = $row->BILLETE;
        $fraccion2  = $row->FRACCION;
        $agencia2   = $row->NRO_AGEN . ' ' . $row->AGENCIA;
        $localidad2 = $row->LOCALIDAD;
        $premio2    = $row->PREMIO;
    }
    if ($cuenta == 3) {
        $billete3   = $row->BILLETE;
        $fraccion3  = $row->FRACCION;
        $agencia3   = $row->NRO_AGEN;
        $localidad3 = $row->LOCALIDAD;
        $premio3    = $row->PREMIO;
    }
    if ($cuenta == 4) {
        $billete4   = $row->BILLETE;
        $fraccion4  = $row->FRACCION;
        $agencia4   = $row->NRO_AGEN;
        $localidad4 = $row->LOCALIDAD;
        $premio4    = $row->PREMIO;
    }
}

/*require "header_listado.php";*/

$pdf = new FPDF('L', 'mm', 'Legal');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 1);
$pdf->SetFont('Arial', 'B', 8);
$pdf->setXY(10, 10);
/*$pdf->Image('../escribano/escribano_img/gordo_invierno_2018_semana' . $semana . '_extracto.jpg', 0, 0, 300, 210);*/
$pdf->Image('../escribano/escribano_img/gordo_invierno_2018_semana1_extracto_digital.jpg', 0, 0, 355, 215);
//----------------------- 1º Pagina ------------------------------------

$pdf->SetFont('Arial', 'B', 15);
$pdf->SetXY(213, 17.4);
$pdf->Cell(20, 0, $sorteo, 0, 'L', 1);
$pdf->SetXY(160, 17.4);
$pdf->Cell(20, 0, $fecha_sorteo, 0, 'L', 1);

if (strlen($premio1) > 40) {
    $pdf->SetFont('Arial', 'B', 14);
} else {
    $pdf->SetFont('Arial', 'B', 25);
}
$pdf->SetXY(3, 80.33);

//$pdf->Cell(160, 5, utf8_decode($premio1), 0, 0, 'C');
$res_ganador = sql("SELECT TG.ID_JUEGO,
                              TG.SORTEO,
                              TG.SEMANA,
                              TG.BILLETE,
                              TG.FRACCION,
                              NVL(TG.AGENCIA,'') as AGENCIA,
                              NVL(TG.LOCALIDAD,'CORDOBA')      AS LOCALIDAD,
                              TG.NOMBRE AS NOMBRE,
                              TA.PREMIO,TG.SUCURSAL,TA.PREMIO
                            FROM    SGS.T_ANTICIPADA_GANADORES TG,
                                    SGS.T_ANTICIPADA TA
                            WHERE TG.ID_JUEGO = ?
                            AND TG.SORTEO     = ?
                            AND TG.SEMANA     = ?
                            AND TG.SORTEO     =TA.SORTEO
                            AND TG.SEMANA     =TA.SEMANA
                            AND TG.ORDEN      =TA.ORDEN
                            ORDER BY TG.ORDEN", array($_SESSION['id_juego'], $_SESSION['sorteo'], $semana));
$y = 0;
while ($row_ganador = siguiente($res_ganador)) {
    $pdf->SetFont('Arial', 'B', 25);
    $pdf->setXY(50, 35 + $y);
    $pdf->Cell(20, 10, str_pad($row_ganador->BILLETE, 5, 0, STR_PAD_LEFT), 0, 0, 'C');
    $pdf->setXY(80, 35 + $y);
    $pdf->Cell(20, 10, str_pad($row_ganador->FRACCION, 2, 0, STR_PAD_LEFT), 0, 0, 'C');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->setXY(105, 35 + $y);
    $pdf->SetFont('Arial', 'B', 9);
    $linea_ancho = 3;
    if (strlen($row_ganador->PREMIO) <= 10) {
        $pdf->SetFont('Arial', 'B', 18);
        $linea_ancho = 10;
    }
    if (strlen($row_ganador->PREMIO) > 10 && strlen($row_ganador->PREMIO) <= 27) {
        $linea_ancho = 5;
    }
    if (strlen($row_ganador->PREMIO) > 27 && strlen($row_ganador->PREMIO) <= 37) {
        $linea_ancho = 5;
    }

    if (strlen($row_ganador->PREMIO) > 37) {
        $pdf->SetFont('Arial', 'B', 8);
    }

    $pdf->MultiCell(43, $linea_ancho, $row_ganador->PREMIO, 0, 'C');
    $pdf->setXY(150, 35.5 + $y);

    if ($row_ganador->NOMBRE == 'VENTA CONTADO CASA CENTRAL') {
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(28, 10, '9001', 0, 0, 'C');
        $pdf->setXY(180, 35.5 + $y);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(60, 5, 'CORDOBA', 0, 0, 'L');
    } else if ($row_ganador->NOMBRE == 'VENTA CONTADO') {
        if ($row_ganador->SUCURSAL == 'CASA CENTRAL') {
            $localidad = 'CORDOBA';
        } else {
            $localidad = $row_ganador->SUCURSAL;
        }
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(28, 10, '9001', 0, 0, 'C');
        $pdf->setXY(180, 35.5 + $y);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->Cell(60, 5, $localidad, 0, 0, 'L');
    } else {
        //$pdf->setXY(203, 58 + $y);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->MultiCell(28, 10, str_pad($row_ganador->AGENCIA, 4, 0, STR_PAD_LEFT), 0, 'C');
        $pdf->setXY(180, 35.5 + $y);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->MultiCell(60, 5, utf8_decode($row_ganador->LOCALIDAD), 0, 'L');

    }
    $pdf->SetFont('Arial', 'B', 25);
    $y += 12.3;
}
/*$pdf->SetFont('Arial', 'B', 28);
$pdf->setXY(5, 145);

if (strlen($agencia1) > 5) {
$pdf->SetFont('Arial', 'B', 16);
} else {
$pdf->SetFont('Arial', 'B', 20);
}

$pdf->MultiCell(160, 4, utf8_decode($agencia1), 0, 'C');*/

/*$pdf->SetFont('Arial', 'B', 25);
$pdf->SetXY(50, 158);
$pdf->Cell(0, 0, utf8_decode($localidad1), 0, 'L', 1);*/

$x = 18;
$y = 200;

$pdf->SetFont('Arial', 'B', 8);

$pdf->SetXY($x + 14, $y - 13);
$pdf->Cell(35, 10, $usuario, 0, 0, 'C');
$pdf->SetXY($x + 47, $y - 13);
$pdf->Cell(43, 10, $escribano, 0, 0, 'C');
$pdf->SetXY($x + 108, $y - 12);
$pdf->SetFont('Arial', 'B', 20);
$pdf->Cell(10, 10, $fecha_prescripcion, 0, 0, 'C');

$res_premios = sql("SELECT PREMIO,COUNT(*)AS CANTIDAD,MAX(ORDEN) ORDEN
FROM
   SGS.T_ANTICIPADA
WHERE ID_JUEGO    = ?
AND SORTEO       = ?
AND SEMANA       = ?
GROUP BY PREMIO
ORDER BY ORDEN", array($_SESSION['id_juego'], $_SESSION['sorteo'], ($semana + 1)));

if ($semana != $cantidad_semanas) {
    $pdf->SetXY($x + 160, $y - 12);
    $pdf->Cell(10, 10, $fecha_proximo, 0, 0, 'C');

    $y_premio = 0;
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetXY($x + 160, $y - 16);
    while ($row_premio = siguiente($res_premios)) {
        if ($row_premio->CANTIDAD == 1) {
            $premio = $row_premio->PREMIO;
        } else {
            $premio = str_replace('$', '', trim($row_premio->PREMIO));
            $premio = str_replace('EN EFECTIVO', '', $premio);
            $premio = str_replace('.', '', $premio);
            $premio = str_replace(' ', '', $premio);

            if (!is_numeric($premio)) {
                $premio = $row_premio->CANTIDAD . ' ' . $row_premio->PREMIO;
            } else {
                $premio = $row_premio->CANTIDAD . ' PREMIOS DE ' . $row_premio->PREMIO;
            }

        }
        $pdf->SetXY(215, $pdf->GetY() + $y_premio);
        $pdf->MultiCell(55, 4, $premio, 0, 'L');

        $y_premio = 0;
    }
}

/*
$pdf->SetXY($x + 290, $y - 32);
if ($row->SEMANA != $row->CANTIDAD_SEMANAS) {
$y_premio = 180;
if (strlen($proximo_premio) <= 10) {
$pdf->SetFont('Arial', 'B', 18);
$pdf->SetXY(306, $y_premio);
$pdf->Cell(10, 8, $proximo_premio, 0, 1, 'C');
} else {
$pdf->SetFont('Arial', 'B', 14);
$premios = str_split($proximo_premio, 25);

foreach ($premios as $premio) {
$pdf->SetXY(306, $y_premio);
$pdf->Cell(10, 8, $premio, 0, 1, 'C');
$y_premio = $pdf->GetY() + 1;

}
}

$pdf->SetFont('Arial', 'B', 19);
$pdf->SetXY($x + 210, $y - 22);

$pdf->Cell(10, 10, $fecha_proximo, 0, 0, 'C');
}*/
/*$pdf->Output();*/
$pdf->Output($_SESSION['sorteo'] . '_SEMANA_' . $semana . '.pdf', 'F');
$pdf->Output();
