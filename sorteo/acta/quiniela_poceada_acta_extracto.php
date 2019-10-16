<?php
session_start();
//var_dump($_SESSION);
include_once dirname(__FILE__) . '/../../db.php';

$titulo = strtoupper('ACTA ' . $_SESSION['juego']);

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
$texto1      = "En la Ciudad de Córdoba, República Argentina, a los " . substr($fechasorteo, 0, 2) . " días del Mes de " . nombre_meses(substr($fechasorteo, 3, 2)) . " del año " . substr($fechasorteo, 6, 4) . " presentes en el Salón de Sorteos de la 'LOTERIA DE LA PROVINCIA DE CORDOBA S.E.', sito en calle 27 de Abril 185, de esta Ciudad, los agentes de la Institución: el Sr. " . $row_sorteo->JEFE . " en su carácter de Jefe de Sorteos en representación de la Subgerencia Departamental de Operaciones y el Sr. " . $row_sorteo->OPERADOR . " en su calidad de operador, siendo las     :     horas, con el objeto de realizar el Sorteo '" . $_SESSION['juego'] . "' programado. Iniciado el sorteo, se verifica en forma alternativa y conforme a la Reglamentación vigente, los veinte premios por extracción, lo que como resultado se consignan a continuación:";
$texto2      = "Con lo que se da por terminado el acto, previa lectura y ratificación de los actuantes, firman la presente por ante mí " . $escribano . " doy fe Escribano Autorizante, de todo lo que certifico; siendo las ............... hs., se da por finalizado el Sorteo.";

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
$pdf->ln(1);
$pdf->SetX(25);
$pdf->SetFont('Times', '', 11);
$y            = $pdf->GetY();
$ln           = 5;
$primer_corte = true;
$pdf->SetX(27);
$pdf->Cell(6, 5, 'PD', 0, 0, 'C');
$pdf->Cell(6, 5, 'ND', 0, 0, 'C');
$pdf->Cell(6, 5, 'PV', 0, 0, 'C');
$pdf->Cell(6, 5, '', 0, 0, 'C');
$pdf->Cell(130, 5, utf8_decode('Extracciones complementarias hasta la extracción valida'), 0, 1, 'L');
$pdf->SetX(27);
while ($row_extracciones_comp = $rs_extracciones_comp->FetchNextObject($toupper = true)) {
    $salto_linea = 0;
    $x_actual    = $pdf->GetX();
    if ($x_actual >= 180) {
        $pdf->ln($ln);
        $pdf->setX(27);
    }
    $strikeout_x       = $pdf->getX() + 1;
    $strikeout_y_start = $pdf->GetY() + 0.3;

    if ($row_extracciones_comp->VALIDO == 'S') {
        $pdf->SetTextColor(255, 255, 255);
        $pdf->SetFillColor(0, 0, 0);
    } else {
        $pdf->SetTextColor(0, 0, 0);
        $pdf->SetFont('Times', '', 9);
        $pdf->SetFillColor(230, 230, 230);
    }
    if (strpos($row_extracciones_comp->SORTEO_ASOC, 'QUINIELA DUPLICADO') !== false && $primer_corte == false) {
        $pdf->ln(5);
        $pdf->SetX(27);
        $pdf->Cell(6, 5, $row_extracciones_comp->POSICION, 1, 0, 'C');
        $pdf->Cell(6, 5, str_pad($row_extracciones_comp->NUMERO, 2, "0", STR_PAD_LEFT), 1, 0, 'C', 1);
        $pdf->Cell(6, 5, $row_extracciones_comp->POSICION_DUPLICADO, 1, 0, 'C', 1);
        $pdf->Cell(6, 5, ':', 0, 0, 'C');
    } else {
        if ($primer_corte == true) {
            $pdf->Cell(6, 5, $row_extracciones_comp->POSICION, 1, 0, 'C');
        }

        $pdf->Cell(6, 5, str_pad($row_extracciones_comp->NUMERO, 2, "0", STR_PAD_LEFT), 1, 0, 'C', 1);
        if ($primer_corte == true) {
            $pdf->Cell(6, 5, $row_extracciones_comp->POSICION_DUPLICADO, 1, 0, 'C', 1);
        }
        if ($primer_corte == true) {
            $pdf->Cell(6, 5, ':', 0, 0, 'C');
        }

    }

    $primer_corte = false;

    $strikeout_y = $strikeout_y_start + 2;
    /*if ($row_extracciones_comp->VALIDO == 'D') {

$pdf->Line($strikeout_x, $strikeout_y, $strikeout_x + 3, $strikeout_y);
}*/

}
$pdf->ln(7);
$pdf->SetX(25);
$pdf->SetTextColor(0, 0, 0);
$pdf->SetFont('Times', 'B', 11);
$pdf->Cell(100, 5, utf8_decode('Referencia PD: Posición Duplicada, ND: Número Duplicado, PV: Posición Validada'), 0, 1, 'L');

$pdf->SetXY(25, 230);
$pdf->SetFont('Times', '', 11);
$pdf->MultiCell(155, 5, utf8_decode('Hora de finalización del sorteo: ............, labrándose la presente, previa lectura y ratificación de los actuantes, firman la presente por ante mí ' . utf8_decode($row_sorteo->ESCRIBANO) . ' Escribano/a autorizante doy fe de todo lo que certifico.'));
/*$pdf->SetFont('Times', '', 11);
$pdf->SetXY(25, 230);
$pdf->MultiCell(160, 5, utf8_decode($texto2), 0, 'J', 0, 0);*/

//registro
$pdf->SetFont('Times', 'B', 11);
$pdf->SetXY(25, 250);
$pdf->Cell(20, 0, utf8_decode('Consta en escritura Nº_________Sección_________ - Doy fe'), 0, 0, 1);

$pdf->SetFont('Times', 'B', 9);
$pdf->SetXY(25, 260);
$pdf->Cell(150, 5, '___________________                                               ___________________                                      _________________________', 0, 1, 'J');
$pdf->SetXY(25, 265);
$pdf->Cell(150, 5, '          Operador                                                                 Jefe de Sorteos                                               Firma Escribano Actuante', 0, 0, 'J');
$pdf->SetXY(28, 270);
$pdf->Cell(25, 5, utf8_decode($row_sorteo->OPERADOR), 0, 0, 'C');

$pdf->SetXY(96, 270);
$pdf->Cell(25, 5, utf8_decode($row_sorteo->JEFE), 0, 0, 'C');

$pdf->SetXY(162, 270);
$pdf->Cell(25, 5, utf8_decode($row_sorteo->ESCRIBANO), 0, 0, 'C');

$pdf->SetXY(45, 103);
$jj = 0;

$y_inicio = $pdf->GetY();
while ($row = $rs_extracciones->FetchNextObject($toupper = true)) {

    if ($jj == 5) {
        $x += 40;
        $jj = 0;
        $pdf->SetY($y_inicio);
        $pdf->SetX($x);

    }
    $pdf->SetX($x);
    $pdf->SetFont('Times', 'B', 11);
    $pdf->Cell(20, 5, ucwords(strtolower('POSICION ' . $row->POSICION)), 1, 0, 'C');
    $pdf->SetFont('Times', '', 11);
    $pdf->Cell(20, 5, $row->NUMERO, 1, 1, 'C');
    $jj += 1;
}

$pdf->Output();

function nombre_meses($nro_mes)
{
    switch ($nro_mes) {
        case 1:
            $nombre_mes = 'Enero';
            break;
        case 2:
            $nombre_mes = 'Febrero';
            break;
        case 3:
            $nombre_mes = 'Marzo';
            break;
        case 4:
            $nombre_mes = 'Abril';
            break;
        case 5:
            $nombre_mes = 'Mayo';
            break;
        case 6:
            $nombre_mes = 'Junio';
            break;
        case 7:
            $nombre_mes = 'Julio';
            break;
        case 8:
            $nombre_mes = 'Agosto';
            break;
        case 9:
            $nombre_mes = 'Septiembre';
            break;
        case 10:
            $nombre_mes = 'Octubre';
            break;
        case 11:
            $nombre_mes = 'Noviembre';
            break;
        case 12:
            $nombre_mes = 'Diciembre';
            break;
        default:
            $nombre_mes = 'S/P';
    }
    return $nombre_mes;
}
