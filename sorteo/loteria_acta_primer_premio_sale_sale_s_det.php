<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';

//$db->debug=true;
//OBTENGO DATOS DEL SORTEO

$rs_extraccion_segundo = sql("SELECT tg.id_premio_descripcion as PREMIO,COUNT(*) as GANADOR
                    FROM SGS.T_SORTEO TS,
                        SGS.T_PROGRAMA TP,
                        SGS.t_programa_premios tpr,
                        SGS.t_ganadores tg,
                                sgs.t_extraccion te
                    WHERE ts.SORTEO        =?
                    AND TS.ID_JUEGO        =?
                    AND ts.id_programa     = tp.id_programa
                    AND tp.id_programa     = tpr.id_programa
                    AND tpr.id_descripcion =tg.id_premio_descripcion
                    AND ts.sorteo          =tg.sorteo
                    AND ts.id_juego        =tg.id_juego
                    AND upper(tpr.sale_o_sale) ='SI'
                    and te.sorteo=ts.sorteo
                            and te.id_juego=ts.id_juego
                            and te.numero=tg.billete
                            and te.posicion=tg.id_premio_descripcion
                    GROUP BY tg.id_premio_descripcion", array($_SESSION['sorteo'], $_SESSION['id_juego']));

if ($rs_extraccion_segundo->RecordCount() == 0) {
    die("En este sorteo no hay juego Sortea Hasta Que Sale ");
}

try {
    $rs_sorteo = sql("SELECT TO_CHAR(SO.FECHA_SORTEO, 'DD/MM/YYYY') AS FECHA_SORTEO,
              TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO, 'DD/MM/YYYY') AS FECHA_CADUCIDAD,
              U.DESCRIPCION JEFE_SORTEO,
              SO.DESCRIPCION AS USUARIO,
              US.DESCRIPCION OPERADOR,
              ESC.DESCRIPCION     AS ESCRIBANO
              from sgs.T_SORTEO       SO,
              sgs.T_ESCRIBANO    ESC,
              SUPERUSUARIO.USUARIOS U,
              SUPERUSUARIO.USUARIOS US
              where SO.ID_ESCRIBANO = ESC.ID_ESCRIBANO (+)
              AND U.ID_USUARIO (+) = SO.ID_JEFE
              AND US.ID_USUARIO (+) = SO.ID_OPERADOR
              AND SO.SORTEO = ?
              AND SO.ID_JUEGO = ?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}
$row_sor     = $rs_sorteo->FetchNextObject($toupper = true);
$fechasorteo = $row_sor->FECHA_SORTEO;
$fechacaduca = $row_sor->FECHA_CADUCIDAD;
$jefesorteo  = utf8_decode($row_sor->JEFE_SORTEO);
$operador    = utf8_decode($row_sor->OPERADOR);
$escribano   = utf8_decode($row_sor->ESCRIBANO);

try {
    $rs = sql("   SELECT * FROM (
                              SELECT DISTINCT LPAD(numero, '5', '0') AS EXTRACCION,
                                posicion                             AS ID_DESCRIPCION,
                                DECODE(
                                (SELECT COUNT(*) FROM sgs.t_billetes_participantes WHERE SORTEO = te.SORTEO
                                AND ID_JUEGO                                                    = te.ID_JUEGO
                                AND BILLETE                                                     = te.numero
                                ), 0, 'NO VENDIDO', 'VENDIDO') AS VENDIDO,
                                tPP.PREMIO_EFECTIVO,
                                (SELECT DESCRIPCION_AGENCIA
                                FROM sgs.t_billetes_participantes
                                WHERE SORTEO = te.SORTEO
                                AND ID_JUEGO = te.ID_JUEGO
                                AND BILLETE  = te.numero
                                AND rownum=1
                                ) AS DISTRIBUIDOEN,TE.ORDEN
                                FROM sgs.T_EXTRACCION te,
                                SGS.t_programa_premios tpp,SGS.t_sorteo ts
                              WHERE zona_juego=2
                                AND posicion    =1
                                AND te.sorteo      =?
                                AND te.id_juego    =?
                                 and ts.sorteo=te.sorteo
                                and ts.id_juego=te.id_juego
                                and ts.id_programa=tpp.id_programa
                                AND te.posicion =tpp.id_descripcion

                                ORDER BY TE.ORDEN DESC
                              )
                              WHERE rownum=1", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

$row = $rs->FetchNextObject($toupper = true);

$titulo = strtoupper('ACTA SORTEO DE ' . $_SESSION['juego'] . ' ' . $_SESSION['juego_tipo'] . ' EMISION ' . $_SESSION['sorteo']);

if ($_SESSION['codigo_tipo'] == 2) {
    $desc = $_SESSION['descripcion_sorteo'];
} else {
    $desc = "";
}

$titulo2 = strtoupper(utf8_decode('MODALIDAD SORTEA HASTA QUE SALE'));

require "header_listado.php";
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Times', 'B', 12);
$pdf->SetXY(10, 50);
$pdf->Cell(40, 5, utf8_decode('Posición'), 'B', 0, 'C');
$pdf->Cell(50, 5, 'Fecha ', 'B', 0, 'C');
$pdf->Cell(40, 5, utf8_decode('Número'), 'B', 0, 'C');
$pdf->Cell(40, 5, '', 'B', 1, 'L');

$y_line = $pdf->GetY();
$pdf->SetX(10);
$pdf->SetFont('Times', '', 18);

$pdf->Cell(40, 8, $row->ID_DESCRIPCION, 'B', 0, 'C');
$pdf->SetFont('Times', '', 14);
$pdf->Cell(50, 8, $fechasorteo, 'B', 0, 'C');
$pdf->SetFont('Times', '', 24);
$pdf->Cell(40, 8, $row->EXTRACCION, 'B', 0, 'C');
$pdf->SetFont('Times', '', 10);
$pdf->Cell(40, 8, $row->VENDIDO, 'B', 1, 'L');
$pdf->Ln(20);
$pdf->SetX(20);
if ($row->VENDIDO == 'VENDIDO') {
    $pdf->SetFont('Times', 'B', 12);
    $pdf->Cell(30, 10, ' Monto del Premio $ ' . number_format($row->PREMIO_EFECTIVO, 0, ',', '.'), '0', 0, 'L');
}
$pdf->SetXY(20, 100);

$pdf->SetFont('Times', 'B', 10);
if (!is_null($row->DISTRIBUIDOEN)) {
    try {
        $rs_fracciones = sql(" SELECT LPAD(FRACCION,2,0) AS FRACCION,DESCRIPCION_AGENCIA,LOCALIDAD,DESCRIPCION_SUCURSAL,PROVINCIA,ID_SUCURSAL,LPAD(ID_AGENCIA,5,'0') AS ID_AGENCIA
                FROM sgs.t_billetes_participantes
                WHERE SORTEO = ?
                AND ID_JUEGO = ?
                AND BILLETE  = ?", array($_SESSION['sorteo'], $_SESSION['id_juego'], $row->EXTRACCION));
    } catch (exception $e) {die($db->ErrorMsg());}
    $distribuido = '';
    $i           = 0;
    $agencia     = '';
    while ($row_fraccion = $rs_fracciones->FetchNextObject($toupper = true)) {

        if ($i == 0) {
            $agencia     = $row_fraccion->DESCRIPCION_AGENCIA;
            $distribuido = str_pad($row_fraccion->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . '- ' . $distribuido . ' ' . str_pad($row_fraccion->ID_SUCURSAL, 2, "0", STR_PAD_LEFT) . '-' . $row_fraccion->DESCRIPCION_SUCURSAL . ' ' . $row_fraccion->PROVINCIA;
            $localidad   = $row_fraccion->LOCALIDAD;
            if ($row_fraccion->DESCRIPCION_AGENCIA == 'VENTA CONTADO CASA CENTRAL') {
                $localidad   = $row_fraccion->PROVINCIA;
                $distribuido = '09001 - ' . $row_fraccion->PROVINCIA . ', ' . $localidad;

            } else if ($row_fraccion->DESCRIPCION_AGENCIA == 'VENTA CONTADO') {
                if ($row_fraccion->ID_SUCURSAL == 1) {
                    $localidad = $row_fraccion->PROVINCIA;
                } else {
                    $localidad = $row_fraccion->DESCRIPCION_SUCURSAL;
                }
                $distribuido = '09001 - ' . $row_fraccion->DESCRIPCION_SUCURSAL . ', ' . $localidad;
            }
            $i = 1;
        }
        if ($row_fraccion->DESCRIPCION_AGENCIA != $agencia) {
            $distribuido = str_pad($row_fraccion->ID_AGENCIA, 5, "0", STR_PAD_LEFT) . '- ' . $distribuido . ' ' . str_pad($row_fraccion->ID_SUCURSAL, 2, "0", STR_PAD_LEFT) . '-' . $row_fraccion->DESCRIPCION_SUCURSAL . ' ' . $row_fraccion->PROVINCIA;
            $localidad   = $row_fraccion->LOCALIDAD;
            if ($row_fraccion->DESCRIPCION_AGENCIA == 'VENTA CONTADO CASA CENTRAL') {
                $localidad   = $row_fraccion->PROVINCIA;
                $distribuido = '09001 - ' . $row_fraccion->PROVINCIA . ', ' . $localidad;

            } else if ($row_fraccion->DESCRIPCION_AGENCIA == 'VENTA CONTADO') {
                if ($row_fraccion->ID_SUCURSAL == 1) {
                    $localidad = $row_fraccion->PROVINCIA;
                } else {
                    $localidad = $row_fraccion->DESCRIPCION_SUCURSAL;
                }
                $distribuido = '09001 - ' . $row_fraccion->DESCRIPCION_SUCURSAL . ', ' . $localidad;
            }
            $agencia = $row_fraccion->DESCRIPCION_AGENCIA;
        }

    }

    $pdf->MultiCell(180, 5, 'DISTRIBUIDO A: ' . $distribuido, 0, 'L');
} else {
    $pdf->Cell(30, 0, $row->DISTRIBUIDOEN, 0, 0, 'l');
}

/*if (!is_null($row->DISTRIBUIDOEN)){
$pdf->SetFont('Times','B',10);
$pdf->SetX(24);
$pdf->Cell(80,8,'DETALLE DE FRACCIONES CON PREMIO',0,1,'L');
$pdf->SetX(24);
$pdf->SetFont('Times','B',8);
$pdf->Cell(18,5,'FRACCION',1,0,'R');
$pdf->Cell(70,5,'AGENCIA',1,0,'C');
$pdf->Cell(70,5,'LOCALIDAD',1,1,'C');
try   {
$rs_fracciones = sql(" SELECT LPAD(FRACCION,2,0) AS FRACCION,DESCRIPCION_AGENCIA,LOCALIDAD,DESCRIPCION_SUCURSAL,PROVINCIA,ID_SUCURSAL,ID_AGENCIA
FROM sgs.t_billetes_participantes
WHERE SORTEO = ?
AND ID_JUEGO = ?
AND BILLETE  = ?",array($_SESSION['sorteo'], $_SESSION['id_juego'],$row->EXTRACCION));
}catch  (exception $e) { die($db->ErrorMsg());}
while($row_fraccion=$rs_fracciones->FetchNextObject($toupper=true)){
$descripcion=str_pad($row_fraccion->ID_AGENCIA, 4, "0", STR_PAD_LEFT).'-'.$row_fraccion->DESCRIPCION_AGENCIA;
$localidad=$row_fraccion->LOCALIDAD;
if($row_fraccion->DESCRIPCION_AGENCIA=='VENTA CONTADO'){
$descripcion='VENTA MOSTRADOR';
$localidad=str_pad($row_fraccion->ID_SUCURSAL, 2, "0", STR_PAD_LEFT).'-'.$row_fraccion->DESCRIPCION_SUCURSAL.', '.$row_fraccion->PROVINCIA;
}
if($row_fraccion->DESCRIPCION_AGENCIA=='VENTA MOSTRADOR'){
$localidad=$row_fraccion->PROVINCIA;
}
$pdf->SetX(24);
$pdf->Cell(18,5,$row_fraccion->FRACCION,1,0,'C');
$pdf->Cell(70,5,$descripcion,1,0,'L');
$pdf->Cell(70,5,$localidad,1,1,'L');
}
}
 */
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
