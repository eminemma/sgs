<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';

require "header_listado.php";
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');

//OBTENGO DATOS DEL SORTEO
try {
    $rs_sorteo = sql("  SELECT TO_CHAR(SO.FECHA_SORTEO,'DD/MM/YYYY')       AS FECHA_SORTEO,
                    jefe.descripcion                                  AS JEFE,
                    operador.descripcion                              AS USUARIO,
                    ESC.DESCRIPCION                                  AS ESCRIBANO,
                    TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY')                      AS FECHA_CADUCIDAD
                FROM  SGS.T_SORTEO SO,
                    SGS.T_ESCRIBANO ESC,
                    SUPERUSUARIO.usuarios jefe,
                    SUPERUSUARIO.usuarios operador
                WHERE   SO.ID_ESCRIBANO=ESC.ID_ESCRIBANO(+)
                  AND jefe.ID_USUARIO(+)=so.id_jefe
                  AND operador.ID_USUARIO(+)=SO.id_operador
                  AND SORTEO           = ?
                  AND ID_JUEGO         = ?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {
    die($db->ErrorMsg());
}

$row_sor     = $rs_sorteo->FetchNextObject($toupper = true);
$fechasorteo = $row_sor->FECHA_SORTEO;
$fechacaduca = $row_sor->FECHA_CADUCIDAD;
$jefe        = $row_sor->JEFE;
$operador    = utf8_decode($row_sor->USUARIO);
$escribano   = utf8_decode($row_sor->ESCRIBANO);
$jefesorteo  = utf8_decode($row_sor->JEFE);

//$db->debug=true;
try {
    $rs_extracciones = sql("   SELECT te.orden,tpp.premio_efectivo
                    as premio,tpd.descripcion,te.posicion,te.numero,te.fraccion,DECODE(
                                                              (SELECT COUNT(*) FROM sgs.t_billetes_participantes WHERE SORTEO = te.SORTEO
                                                              AND ID_JUEGO                                                    = te.ID_JUEGO
                                                              AND BILLETE                                                     = te.numero
                                                              and fraccion                                                    =te.fraccion
                                                              ), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO,
                    (SELECT COUNT(*) FROM SGS.t_billetes_participantes WHERE billete=te.numero and sorteo=te.sorteo)
                                    AS ganadores,to_char(FECHA_EXTRACCION,'dd/mm/yy hh:mi:ss') as FECHA_EXTRACCION
                FROM SGS.T_EXTRACCION te,SGS.t_programa_premios tpp,SGS.t_premio_descripcion tpd,SGS.T_SORTEO S
                WHERE te.SORTEO=?
                AND te.ID_JUEGO=?
                AND te.ZONA_JUEGO=1
                and tpp.id_descripcion=tpd.id_premio_desc
                and te.posicion=tpp.id_descripcion
                 AND S.ID_JUEGO=TE.ID_JUEGO
                 AND S.SORTEO=TE.SORTEO
                 AND S.ID_PROGRAMA=TPP.ID_PROGRAMA
        ORDER BY te.zona_juego desc ,te.ORDEN DESC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

if ($_SESSION['sorteo'] == 4766) {
    $titulo = strtoupper('LOTERIA ORDINARIA');
} else {
    $titulo = strtoupper('LOTERIA ' . $_SESSION['juego_tipo']);
}

$titulo2 = strtoupper('EMISION ' . $_SESSION['sorteo'] . ' ' . $desc);

$pdf = new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

$x_suc           = 0;
$x_fp_nombre     = '';
$corte           = 0;
$total           = 0;
$estado_anterior = 0;
$jj              = 0;
$zy              = 123;
$zy1             = 123;
$x               = 42;
$xx              = 100;

$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 48);
$pdf->Cell(30, 8, 'Fecha:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 48);
$pdf->Cell(30, 8, $fechasorteo, 1, 0, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 56);
$pdf->Cell(30, 8, 'Hora:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 56);
$pdf->Cell(30, 8, '..............', 1, 0, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 64);
$pdf->Cell(30, 8, 'Caducidad:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 64);
$pdf->Cell(30, 8, $fechacaduca, 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetXY(10, 100);

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(10, 5, '#OR', 1, 0, 'C');
$pdf->Cell(20, 5, 'POSICION', 1, 0, 'C');
$pdf->Cell(20, 5, 'ENTERO', 1, 0, 'C');
$pdf->Cell(65, 5, 'PREMIO', 1, 0, 'C');
$pdf->Cell(30, 5, 'FRACCIONES', 1, 0, 'C');
// $pdf->Cell(30,5,'FECHA',1,0,'C');
$pdf->Cell(45, 5, 'IMPO./ESPECIAS', 1, 1, 'C');
while ($row_extraccion = $rs_extracciones->FetchNextObject($toupper = true)) {
    $pdf->Cell(10, 5, $row_extraccion->ORDEN, 'B', 0, 'C');
    $pdf->Cell(20, 5, $row_extraccion->POSICION, 'B', 0, 'C');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 5, "0", STR_PAD_LEFT), 'B', 0, 'C');
    $pdf->Cell(65, 5, $row_extraccion->DESCRIPCION, 'B', 0, 'L');
    $pdf->Cell(30, 5, $row_extraccion->GANADORES, 'B', 0, 'C');
    // $pdf->Cell(30,5,$row_extraccion->FECHA_EXTRACCION,'B',0,'C');
    $pdf->Cell(45, 5, '$' . number_format($row_extraccion->PREMIO, 0, ',', '.'), 'B', 1, 'R');
}

//hora
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(87, 250);
$pdf->Cell(20, 5, 'Hora de Finalizacion:............', 0, 0, 'L');

$pdf->SetFont('Times', 'B', 9);
$pdf->SetXY(25, 264);
$pdf->Cell(150, 5, '___________________                                               ___________________                                      _________________________', 0, 1, 'J');
$pdf->SetXY(25, 268);
$pdf->Cell(150, 5, '          Operador                                                                  Jefe de Sorteos                                               Firma Escribano Actuante', 0, 0, 'J');

$pdf->SetXY(28, 271);
$pdf->Cell(25, 5, $operador, 0, 0, 'C');

$pdf->SetXY(96, 271);
$pdf->Cell(25, 5, $jefesorteo, 0, 0, 'C');

$pdf->SetXY(162, 271);
$pdf->Cell(25, 5, $escribano, 0, 0, 'C');

try {
    $rs_extracciones_zona_3 = sql(" SELECT TE.ORDEN,
                                  DECODE(TPP.TIPO_PREMIO,'ESPECIE',TDE.DESCRIPCION_ESPECIA,'EFECTIVO','$'
                                  ||TPP.PREMIO_EFECTIVO) AS PREMIO,
                                  TPD.DESCRIPCION,
                                  TE.POSICION,
                                  TE.NUMERO,
                                  Te.Fraccion,
                                  DECODE(TE.POSICION,26,(DECODE(
                                  (SELECT COUNT(*) FROM SGS.T_BILLETES_PARTICIPANTES WHERE SORTEO = TE.SORTEO
                                  AND ID_JUEGO                                                    = TE.ID_JUEGO
                                  AND BILLETE                                                     = TE.NUMERO
                                  ), 0, 'NO VENDIDO', 'VENDIDO')), DECODE(
                                  (SELECT COUNT(*) FROM SGS.T_BILLETES_PARTICIPANTES WHERE SORTEO = TE.SORTEO
                                  AND ID_JUEGO                                                    = TE.ID_JUEGO
                                  AND BILLETE                                                     = TE.NUMERO
                                  AND FRACCION                                                    =TE.FRACCION
                                  ), 0, 'NO VENDIDO', 'VENDIDO')) AS COMERCIALIZADO,
                                  DECODE(TE.POSICION,26,
                                  (SELECT COUNT(*)
                                  FROM SGS.T_BILLETES_PARTICIPANTES
                                  WHERE BILLETE=TE.NUMERO
                                  AND Sorteo   =Te.Sorteo
                                  AND rownum   =1
                                  ) ,
                                  (SELECT COUNT(*)
                                  FROM SGS.T_BILLETES_PARTICIPANTES
                                  WHERE BILLETE=TE.NUMERO
                                  AND FRACCION =TE.FRACCION
                                  AND SORTEO   =TE.SORTEO
                                  ))                                            AS GANADORES,
                                  TO_CHAR(FECHA_EXTRACCION,'DD/MM/YY HH:MI:SS') AS FECHA_EXTRACCION,
                                  TO_CHAR(FECHA_EXTRACCION,'DD/MM/YY HH:MI:SS') AS FECHA_EXTRACCION
                          FROM SGS.T_EXTRACCION TE,
                            SGS.T_PROGRAMA_PREMIOS TPP,
                            SGS.T_PREMIO_DESCRIPCION TPD,
                            SGS.T_DESCRIPCION_ESPECIAS TDE,
                            Sgs.T_Sorteo S
                          WHERE TE.SORTEO                  =?
                          AND TE.ID_JUEGO                  =?
                          AND (TE.ZONA_JUEGO               =3)
                          AND TPP.ID_DESCRIPCION           =TPD.ID_PREMIO_DESC
                          AND TE.POSICION                  =TPP.ID_DESCRIPCION
                          AND TPP.ID_DESCRIPCION           =TPD.ID_PREMIO_DESC
                          AND TDE.ID_DESCRIPCION_ESPECIA(+)=TPP.PREMIO_ID_ESPECIAS
                          AND TE.ID_JUEGO                  =S.ID_JUEGO
                          AND TE.SORTEO                    =S.SORTEO
                          AND TPP.ID_PROGRAMA              =S.ID_PROGRAMA
                          ORDER BY TE.ZONA_JUEGO DESC ,
                            TE.ORDEN ASC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

try {
    $rs_extracciones_zona_4 = sql(" SELECT TE.ORDEN,
                                  DECODE(TPP.TIPO_PREMIO,'ESPECIE',TDE.DESCRIPCION_ESPECIA,'EFECTIVO','$'
                                  ||TPP.PREMIO_EFECTIVO) AS PREMIO,
                                  TPD.DESCRIPCION,
                                  TE.POSICION,
                                  TE.NUMERO,
                                  Te.Fraccion,
                                  DECODE(TE.POSICION,24,(DECODE(
                                  (SELECT COUNT(*) FROM SGS.T_BILLETES_PARTICIPANTES WHERE SORTEO = TE.SORTEO
                                  AND ID_JUEGO                                                    = TE.ID_JUEGO
                                  AND BILLETE                                                     = TE.NUMERO
                                  ), 0, 'NO VENDIDO', 'VENDIDO')), DECODE(
                                  (SELECT COUNT(*) FROM SGS.T_BILLETES_PARTICIPANTES WHERE SORTEO = TE.SORTEO
                                  AND ID_JUEGO                                                    = TE.ID_JUEGO
                                  AND BILLETE                                                     = TE.NUMERO
                                  AND FRACCION                                                    =TE.FRACCION
                                  ), 0, 'NO VENDIDO', 'VENDIDO')) AS COMERCIALIZADO,
                                  DECODE(TE.POSICION,24,
                                  (SELECT COUNT(*)
                                  FROM SGS.T_BILLETES_PARTICIPANTES
                                  WHERE BILLETE=TE.NUMERO
                                  AND Sorteo   =Te.Sorteo
                                  ) ,
                                  (SELECT COUNT(*)
                                  FROM SGS.T_BILLETES_PARTICIPANTES
                                  WHERE BILLETE=TE.NUMERO
                                  AND SORTEO   =TE.SORTEO
                                  ))                                            AS GANADORES,
                                  TO_CHAR(FECHA_EXTRACCION,'DD/MM/YY HH:MI:SS') AS FECHA_EXTRACCION,
                                  TO_CHAR(FECHA_EXTRACCION,'DD/MM/YY HH:MI:SS') AS FECHA_EXTRACCION
                          FROM SGS.T_EXTRACCION TE,
                            SGS.T_PROGRAMA_PREMIOS TPP,
                            SGS.T_PREMIO_DESCRIPCION TPD,
                            SGS.T_DESCRIPCION_ESPECIAS TDE,
                            Sgs.T_Sorteo S
                          WHERE TE.SORTEO                  =?
                          AND TE.ID_JUEGO                  =?
                          AND (TE.ZONA_JUEGO                 =4)
                          AND TPP.ID_DESCRIPCION           =TPD.ID_PREMIO_DESC
                          AND TE.POSICION                  =TPP.ID_DESCRIPCION
                          AND TPP.ID_DESCRIPCION           =TPD.ID_PREMIO_DESC
                          AND TDE.ID_DESCRIPCION_ESPECIA(+)=TPP.PREMIO_ID_ESPECIAS
                          AND TE.ID_JUEGO                  =S.ID_JUEGO
                          AND TE.SORTEO                    =S.SORTEO
                          AND TPP.ID_PROGRAMA              =S.ID_PROGRAMA
                          ORDER BY TE.ZONA_JUEGO DESC ,
                            TE.ORDEN ASC", array($_SESSION['sorteo'], $_SESSION['id_juego']));
} catch (exception $e) {die($db->ErrorMsg());}

$titulo2 = strtoupper('PREMIOS EXTRAORDINARIOS, EMISION ' . $_SESSION['sorteo'] . ' ' . $desc);

$pdf->AliasNbPages();
$pdf->AddPage();

$x_suc           = 0;
$x_fp_nombre     = '';
$corte           = 0;
$total           = 0;
$estado_anterior = 0;
$jj              = 0;
$zy              = 123;
$zy1             = 123;
$x               = 42;
$xx              = 100;

$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 48);
$pdf->Cell(30, 8, 'Fecha:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 48);
$pdf->Cell(30, 8, $fechasorteo, 1, 0, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 56);
$pdf->Cell(30, 8, 'Hora:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 56);
$pdf->Cell(30, 8, '..............', 1, 0, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 64);
$pdf->Cell(30, 8, 'Caducidad:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 64);
$pdf->Cell(30, 8, $fechacaduca, 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetXY(10, 100);

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(10, 5, '#OR', 1, 0, 'C');
$pdf->Cell(20, 5, 'POSICION', 1, 0, 'C');
$pdf->Cell(20, 5, 'ENTERO', 1, 0, 'C');
$pdf->Cell(20, 5, 'FRACCION', 1, 0, 'C');
$pdf->Cell(55, 5, 'PREMIO', 1, 0, 'C');
$pdf->Cell(10, 5, 'GAN.', 1, 0, 'C');
$pdf->Cell(60, 5, 'IMPO./ESPECIAS', 1, 1, 'C');
while ($row_extraccion = $rs_extracciones_zona_3->FetchNextObject($toupper = true)) {
    $fraccion = (((int) $row_extraccion->FRACCION == 0) ? '--' : str_pad($row_extraccion->FRACCION, 2, "0", STR_PAD_LEFT));
    $pdf->Cell(10, 5, $row_extraccion->ORDEN, 'B', 0, 'C');
    $pdf->Cell(20, 5, $row_extraccion->POSICION, 'B', 0, 'C');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 5, "0", STR_PAD_LEFT), 'B', 0, 'C');
    $pdf->Cell(20, 5, $fraccion, 'B', 0, 'C');

    $pdf->Cell(55, 5, $row_extraccion->DESCRIPCION, 'B', 0, 'L');

    $ganadores = (((int) $row_extraccion->FRACCION == 0 && $row_extraccion->GANADORES > 1) ? 1 : $row_extraccion->GANADORES);
    //$ganadores = (((int)$row_extraccion->FRACCION == 0 && $row_extraccion->GANADORES > 1) ? 1 : 1);

    ## hack para sorteo 4765 (5 extraordinarios)
    if ($ganadores > 1) {
        $ganadores = 1;
    }

    $pdf->Cell(10, 5, $ganadores, 'B', 0, 'C');
    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 5, $row_extraccion->PREMIO, 'B', 1, 'R');
    $pdf->SetFont('Times', 'B', 10);
}

//hora
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(87, 250);
$pdf->Cell(20, 5, 'Hora de Finalizacion:............', 0, 0, 'L');

$pdf->SetFont('Times', 'B', 9);
$pdf->SetXY(25, 264);
$pdf->Cell(150, 5, '___________________                                               ___________________                                      _________________________', 0, 1, 'J');
$pdf->SetXY(25, 268);
$pdf->Cell(150, 5, '          Operador                                                                  Jefe de Sorteos                                               Firma Escribano Actuante', 0, 0, 'J');

$pdf->SetXY(28, 271);
$pdf->Cell(25, 5, $operador, 0, 0, 'C');

$pdf->SetXY(96, 271);
$pdf->Cell(25, 5, $jefesorteo, 0, 0, 'C');

$pdf->SetXY(162, 271);
$pdf->Cell(25, 5, $escribano, 0, 0, 'C');


/*
$titulo2 = strtoupper('PREMIOS EXTRAORDINARIOS, EMISION ' . $_SESSION['sorteo'] . ' ' . $desc);

$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 48);
$pdf->Cell(30, 8, 'Fecha:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 48);
$pdf->Cell(30, 8, $fechasorteo, 1, 0, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 56);
$pdf->Cell(30, 8, 'Hora:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 56);
$pdf->Cell(30, 8, '..............', 1, 0, 'C');
$pdf->SetFont('Arial', 'I', 11);
$pdf->SetXY(120, 64);
$pdf->Cell(30, 8, 'Caducidad:', 0, 0, 'R');
$pdf->SetFont('Arial', 'BI', 11);
$pdf->SetXY(150, 64);
$pdf->Cell(30, 8, $fechacaduca, 1, 0, 'C');
$pdf->SetFont('Arial', 'B', 11);
$pdf->SetXY(10, 100);

$pdf->SetFont('Times', 'B', 10);
$pdf->Cell(10, 5, '#OR', 1, 0, 'C');
$pdf->Cell(20, 5, 'POSICION', 1, 0, 'C');
$pdf->Cell(20, 5, 'ENTERO', 1, 0, 'C');
$pdf->Cell(80, 5, 'PREMIO', 1, 0, 'C');
$pdf->Cell(60, 5, 'IMPO./ESPECIAS', 1, 1, 'C');
while ($row_extraccion = $rs_extracciones_zona_4->FetchNextObject($toupper = true)) {
    $fraccion = (((int) $row_extraccion->FRACCION == 0) ? '--' : str_pad($row_extraccion->FRACCION, 2, "0", STR_PAD_LEFT));
    $pdf->Cell(10, 5, $row_extraccion->ORDEN, 'B', 0, 'C');
    $pdf->Cell(20, 5, $row_extraccion->POSICION, 'B', 0, 'C');
    $pdf->Cell(20, 5, str_pad($row_extraccion->NUMERO, 5, "0", STR_PAD_LEFT), 'B', 0, 'C');

    $pdf->Cell(80, 5, $row_extraccion->DESCRIPCION, 'B', 0, 'L');

    $pdf->SetFont('Arial', 'B', 7);
    $pdf->Cell(60, 5, $row_extraccion->PREMIO, 'B', 1, 'R');
    $pdf->SetFont('Times', 'B', 10);
}
*/

/*
$rs_extraccion_segundo=sql("SELECT tg.id_premio_descripcion as PREMIO,COUNT(*) as GANADOR
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
GROUP BY tg.id_premio_descripcion",array($_SESSION['sorteo'],$_SESSION['id_juego']));

if($rs_extraccion_segundo->RecordCount() > 0){

try   {
$rs_extracciones = sql("   SELECT te.orden,tpp.premio_efectivo
as premio,'SORTEA HASTA QUE SALE',te.posicion,te.numero,te.fraccion,DECODE(
(SELECT COUNT(*) FROM sgs.t_billetes_participantes WHERE SORTEO = te.SORTEO
AND ID_JUEGO                                                    = te.ID_JUEGO
AND BILLETE                                                     = te.numero
and fraccion                                                    =te.fraccion
), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO,
(SELECT COUNT(*) FROM SGS.t_billetes_participantes WHERE billete=te.numero and fraccion=te.fraccion and sorteo=te.sorteo)
AS ganadores,to_char(FECHA_EXTRACCION,'dd/mm/yy hh:mi:ss') as FECHA_EXTRACCION
FROM SGS.T_EXTRACCION te,SGS.t_programa_premios tpp,SGS.t_premio_descripcion tpd,SGS.T_SORTEO S
WHERE te.SORTEO=?
AND te.ID_JUEGO=?
AND te.ZONA_JUEGO=2
and tpp.id_descripcion=tpd.id_premio_desc
and te.posicion=tpp.id_descripcion
AND S.ID_JUEGO=TE.ID_JUEGO
AND S.SORTEO=TE.SORTEO
AND S.ID_PROGRAMA=TPP.ID_PROGRAMA
ORDER BY te.zona_juego desc ,te.ORDEN DESC",array($_SESSION['sorteo'], $_SESSION['id_juego']));
}catch  (exception $e) { die($db->ErrorMsg());}

$titulo2=strtoupper('LOTERIA SORTEA HASTA QUE SALE, EMISION '.$_SESSION['sorteo'].' '.$desc);

$pdf->AliasNbPages();
$pdf->AddPage();

$x_suc=0;
$x_fp_nombre='';
$corte=0;
$total=0;
$estado_anterior=0;
$jj=0;
$zy=123;
$zy1=123;
$x=42;
$xx=100;

$pdf->SetFont('Arial','I',11);
$pdf->SetXY(120,48);
$pdf->Cell(30,8,'Fecha:',0,0,'R');
$pdf->SetFont('Arial','BI',11);
$pdf->SetXY(150,48);
$pdf->Cell(30,8,$fechasorteo,1,0,'C');
$pdf->SetFont('Arial','I',11);
$pdf->SetXY(120,56);
$pdf->Cell(30,8,'Hora:',0,0,'R');
$pdf->SetFont('Arial','BI',11);
$pdf->SetXY(150,56);
$pdf->Cell(30,8,'..............',1,0,'C');
$pdf->SetFont('Arial','I',11);
$pdf->SetXY(120,64);
$pdf->Cell(30,8,'Caducidad:',0,0,'R');
$pdf->SetFont('Arial','BI',11);
$pdf->SetXY(150,64);
$pdf->Cell(30,8,$fechacaduca,1,0,'C');
$pdf->SetFont('Arial','B',11);
$pdf->SetXY(10,100);

$pdf->SetFont('Times','B',10);
$pdf->Cell(10,5,'#OR',1,0,'C');
$pdf->Cell(20,5,'POSICION',1,0,'C');
$pdf->Cell(20,5,'ENTERO',1,0,'C');
$pdf->Cell(20,5,'FRACCION',1,0,'C');
$pdf->Cell(50,5,'PREMIO',1,0,'C');
$pdf->Cell(10,5,'GAN.',1,0,'C');
$pdf->Cell(30,5,'FECHA',1,0,'C');
$pdf->Cell(30,5,'IMPO./ESPECIAS',1,1,'C');
while($row_extraccion=$rs_extracciones->FetchNextObject($toupper=true)){
$pdf->Cell(10,5,$row_extraccion->ORDEN,'B',0,'C');
$pdf->Cell(20,5,$row_extraccion->POSICION,'B',0,'C');
$pdf->Cell(20,5,str_pad($row_extraccion->NUMERO, 5, "0", STR_PAD_LEFT),'B',0,'C');
$pdf->Cell(20,5,'','B',0,'C');
$pdf->Cell(50,5,$row_extraccion->DESCRIPCION,'B',0,'L');
$pdf->Cell(10,5,$row_extraccion->GANADORES,'B',0,'C');
$pdf->Cell(30,5,$row_extraccion->FECHA_EXTRACCION,'B',0,'C');
$pdf->Cell(30,5,$row_extraccion->PREMIO,'B',1,'R');
}
//hora
$pdf->SetFont('Arial','B',10);
$pdf->SetXY(87,250);
$pdf->Cell(20,5,'Hora de Finalizacion:............',0,0,'L');

$pdf->SetFont('Times','B',9);
$pdf->SetXY(25,264);
$pdf->Cell(150,5,'___________________                                               ___________________                                      _________________________',0,1,'J');
$pdf->SetXY(25,268);
$pdf->Cell(150,5,'          Operador                                                                  Jefe de Sorteos                                               Firma Escribano Actuante',0,0,'J');

$pdf->SetXY(28,271);
$pdf->Cell(25,5,$operador,0,0,'C');

$pdf->SetXY(96,271);
$pdf->Cell(25,5,$jefesorteo,0,0,'C');

$pdf->SetXY(162,271);
$pdf->Cell(25,5,$escribano,0,0,'C');
}*/
$pdf->Output();
