<?php
session_start();

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

require "header_listado.php";

$pdf = new FPDF('L', 'mm', 'A4');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetAutoPageBreak(true, 1);
$pdf->SetFont('Arial', 'B', 8);
$pdf->setXY(10, 10);

/*$pdf->Image('../escribano/escribano_img/gordo_navidad_2019_semana1_extracto_digital.jpg', 0, 0, 297, 215);*/
//----------------------- 1º Pagina ------------------------------------

$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(270, 32);
$pdf->Cell(20, 0, $sorteo, 0, 'L', 1);
$pdf->SetXY(260, 17);
$pdf->Cell(20, 0, $fecha_sorteo, 0, 'L', 1);

if (strlen($premio1) > 40) {
    $pdf->SetFont('Arial', 'B', 14);
} else {
    $pdf->SetFont('Arial', 'B', 25);
}
$pdf->SetXY(3, 20);

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
$y        = 45;
$cantiGan = 0;
if ($semana == $cantidad_semanas) {
    $y = 20;
}
while ($row_ganador = siguiente($res_ganador)) {
    $pdf->SetFont('Arial', 'B', 20);
    $pdf->setXY(17, 8 + $y);
    $pdf->Cell(32, 16, str_pad($row_ganador->BILLETE, 5, 0, STR_PAD_LEFT), 0, 0, 'C');
    $pdf->setXY(45, 8 + $y);
    $pdf->Cell(32, 17, str_pad($row_ganador->FRACCION, 2, 0, STR_PAD_LEFT), 0, 0, 'C');
    $pdf->SetFont('Arial', 'B', 11);
    $pdf->setXY(75, 8 + $y);
    $pdf->SetFont('Arial', 'B', 15);
    $linea_ancho = 17;
    if (strlen($row_ganador->PREMIO) <= 10) {
        $pdf->SetFont('Arial', 'B', 20);
        $linea_ancho = 17;
    }
    if (strlen($row_ganador->PREMIO) > 10 && strlen($row_ganador->PREMIO) <= 29) {
        $pdf->SetFont('Arial', 'B', 13);
        $linea_ancho = 8;
    }
    if (strlen($row_ganador->PREMIO) > 29 && strlen($row_ganador->PREMIO) <= 37) {
        $pdf->SetFont('Arial', 'B', 13);
        $linea_ancho = 4.5;
    }

    if (strlen($row_ganador->PREMIO) > 37) {
        $pdf->SetFont('Arial', 'B', 8);
    }

    $pdf->MultiCell(40, $linea_ancho, $row_ganador->PREMIO, 0, 'C');
    $pdf->setXY(112, 9 + $y);

    if ($row_ganador->NOMBRE == 'VENTA CONTADO CASA CENTRAL') {
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(35, 17, '9001', 0, 0, 'C');
        $pdf->setXY(145, 9 + $y);
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(50, 17, 'CORDOBA', 0, 0, 'L');
    } else if ($row_ganador->NOMBRE == 'VENTA CONTADO') {
        if ($row_ganador->SUCURSAL == 'CASA CENTRAL') {
            $localidad = 'CORDOBA';
        } else {
            $localidad = $row_ganador->SUCURSAL;
        }
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(35, 17, '9001', 0, 0, 'C');
        $pdf->setXY(145, 9 + $y);
        $pdf->SetFont('Arial', 'B', 13);
        $pdf->Cell(50, 17, $localidad, 0, 0, 'L');

    } else {
        //$pdf->setXY(203, 58 + $y);
        $pdf->SetFont('Arial', 'B', 20);
        $pdf->Cell(35, 17, str_pad($row_ganador->AGENCIA, 4, 0, STR_PAD_LEFT), 0, 0, 'C');
        $pdf->setXY(145, 9 + $y);
        $pdf->SetFont('Arial', 'B', 11);
        $pdf->MultiCell(50, 5, utf8_decode($row_ganador->LOCALIDAD), 0, 'L');

    }
    $pdf->SetFont('Arial', 'B', 25);

    if ($semana == $cantidad_semanas) {
        $y += 17.1;
    } else {
        $y += 17.1;
    }
    /*
CORTE PARA A4 DISEÑO

$cantiGan += 1;
if ($cantiGan == 7) {
$pdf->AddPage();
//$pdf->Image('../escribano/escribano_img/gordo_invierno_2018_semana1_extracto_2.jpg', 0, 0, 430, 0);
$pdf->SetAutoPageBreak(true, 1);
$pdf->SetFont('Arial', 'B', 8);
$pdf->setXY(10, 10);
$y = -50;
}
 */
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

$x = 5;
$y = 192;

$pdf->SetFont('Arial', 'B', 10);
if ($semana == $cantidad_semanas) {
    $pdf->SetXY($x + 20, $y - 11);
} else {
    $pdf->SetXY($x + 2, $y - 10);
}
$pdf->Cell(35, 10, $usuario, 0, 0, 'C');

if ($semana == $cantidad_semanas) {
    $pdf->SetXY($x + 61, $y - 11);
} else {
    $pdf->SetXY($x + 25, $y - 10);
}
$pdf->Cell(49, 10, $escribano, 0, 0, 'C');
if ($semana == $cantidad_semanas) {
    $pdf->SetXY($x + 130, $y - 10);
} else {
    $pdf->SetXY($x + 82, $y - 10);
}
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
    $pdf->SetXY($x + 125, $y - 10);
    $pdf->Cell(10, 10, $fecha_proximo, 0, 0, 'C');

    $y_premio = -5;
    $pdf->SetFont('Arial', 'B', 9);
    $pdf->SetXY($x + 140, $y - 5);
    while ($row_premio = siguiente($res_premios)) {
        if ($row_premio->CANTIDAD == 1) {
            $premio = $row_premio->CANTIDAD . ' PREMIO DE ' . $row_premio->PREMIO;
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
        $pdf->SetXY(160, $pdf->GetY() + $y_premio);
        $pdf->MultiCell(60, 4, $premio, 0, 'L');

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

$pdf->Output();
