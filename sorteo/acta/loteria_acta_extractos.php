<?php
session_start();
include_once dirname(__FILE__) . '/../../db.php';

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

try {
    $rs_extracciones = sql("SELECT DISTINCT 	lpad(pe.billete,'5','0')                      AS extraccion,
												  	INITCAP(des.descripcion)                                    AS DESCRIP,
												  	LPAD(pe.progresion,'2','0')                                 AS progresion,
												  	pe.id_descripcion                                           AS id,
												  	pro.premio_efectivo                                         AS efe,
												  	SUBSTR(TO_CHAR(pe.horaextraccion, 'dd/mm/yyyy hh:mi'),12,5) AS hora,
                                                    pro.sale_o_sale
								FROM 	sgs.t_premio_extracto pe,
								  		sgs.t_premio_descripcion des,
										sgs.t_programa_premios pro,
										sgs.t_sorteo so
								WHERE pe.id_descripcion=des.id_premio_desc
									AND pro.id_descripcion =des.id_premio_desc
									AND so.id_programa     =pro.id_programa
									AND so.sorteo=pe.sorteo
									and so.id_juego=pe.id_juego
									AND so.sorteo          =?
									AND so.id_juego        =?
									AND PE.SERIE           =?
									and pe.ZONA_JUEGO In (1,3)
								ORDER BY pe.id_descripcion ASC", array($_SESSION['sorteo'], $_SESSION['id_juego'], $_SESSION['serie']));
} catch (exception $e) {die($db->ErrorMsg());}

$ii = 0;
//obtengo vectores
while ($row_extracciones = $rs_extracciones->FetchNextObject($toupper = true)) {
    $ii               = $ii + 1;
    $ef               = $ef + 1;
    $jj               = $row_extracciones->ID;
    $quini[$jj]       = $row_extracciones->EXTRACCION;
    $progresion[$jj]  = $row_extracciones->PROGRESION;
    $descripcion[$jj] = $row_extracciones->DESCRIP;
    $hora[$jj]        = $row_extracciones->HORA;
    $efectivo[$jj]    = $row_extracciones->EFE;
    $efectivo[$jj]    = $row_extracciones->EFE;
    $sale_o_sale[$jj] = $row_extracciones->SALE_O_SALE;
}

//OBTENGO DATOS DEL SORTEO
try {
    $rs_sorteo = $db->Execute("	SELECT TO_CHAR(SO.FECHA_SORTEO,'DD/MM/YYYY')       AS FECHA_SORTEO,
										jefe.descripcion                                  AS JEFE,
										operador.descripcion                              AS USUARIO,
										ESC.DESCRIPCION                                  AS ESCRIBANO,
										TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY')                      AS FECHA_CADUCIDAD
								FROM 	SGS.T_SORTEO SO,
										SGS.T_ESCRIBANO ESC,
										SUPERUSUARIO.usuarios jefe,
										SUPERUSUARIO.usuarios operador
								WHERE 	SO.ID_ESCRIBANO=ESC.ID_ESCRIBANO(+)
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
$jefe        = utf8_decode($row_sor->JEFE);
$usuario     = utf8_decode($row_sor->USUARIO);
$escribano   = utf8_decode($row_sor->ESCRIBANO);

try {
    $rscomercializado = $db->Execute("	SELECT 	ID_JUEGO,
										  		SORTEO,
										  		BILLETE,
										  		ID_SUCURSAL,
										  		ID_AGENCIA,
										  		DESCRIPCION_AGENCIA,
										  		LOCALIDAD,
										  		PROVINCIA,
										  		FRACCION,
										  		PROGRESION
										FROM SGS.T_BILLETES_PARTICIPANTES
										WHERE sorteo=?
											AND id_juego=?
											AND billete=?", array($_SESSION['sorteo'], $_SESSION['id_juego'], $quini[1]));
} catch (exception $e) {die($db->ErrorMsg());}

$rscomercializado->FetchNextObject($toupper = true);

if ($jefe == '') {
    $jefe = '........................';
}

if ($usuario == '') {
    $usuario = '........................';
}

if ($escribano == '') {
    $escribano = '........................';
}

$texto1 = "En la Ciudad de Córdoba, República Argentina, a los " . substr($fechasorteo, 0, 2) . " días del Mes de " . nombre_meses(substr($fechasorteo, 3, 2)) . " del año " . substr($fechasorteo, 6, 4) . " presentes en el Salón de Actos de la 'LOTERIA DE LA PROVINCIA DE CORDOBA S.E.', sito en calle 27 de Abril 185, de esta Ciudad, los agentes de la Institución: el Sr. " . $jefe . " en su carácter de Jefe de Sorteos en representación de la Sub. Gcia de Operaciones, Liquidación y Fiscaclización y el Sr. " . $usuario . " en su calidad de operador, siendo las ...... horas, con el objeto de realizar el Sorteo de " . strtolower($desctipo) . " programado. Iniciado el sorteo, se verifica en forma alternativa y conforme a la Reglamentación vigente, los veinte premios por extracción, lo que como resultado se consignan a continuación:";
$texto2 = "Con lo que se da por terminado el acto, previa lectura y ratificación de los actuantes, firman la presente por ante mí " . $escribano . " doy fe Escribano Autorizante, de todo lo que certifico; siendo las ..... hs., se da por finalizado el Sorteo";
//echo utf8_encode($texto1);
//die();

//obtengo datos de fechas

//$titulo=strtoupper('ACTA SORTEO DE LOTERIA '.$_SESSION['juego_tipo']);
if ($_SESSION['sorteo'] == 4766) {
    $titulo = strtoupper('ACTA SORTEO DE LOTERIA ORDINARIA');
} else {
    $titulo = strtoupper('ACTA SORTEO DE LOTERIA ' . $_SESSION['juego_tipo']);
}

if ($_SESSION['codigo_tipo'] == 2) {
    $desc = $_SESSION['descripcion_sorteo'];
} else {
    $desc = "";
}

$titulo2 = strtoupper('EMISION ' . $_SESSION['sorteo']);

require "header_listado_c.php";
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');

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
$pdf->SetXY(30, 52);
$pdf->Cell(30, 0, 'PROGRESION', 0, 0, 'C');
$pdf->SetFont('Arial', 'B', 24);
$pdf->SetXY(30, 60);
$pdf->Cell(30, 0, $progresion[1], 0, 0, 'C');
//cuadroS de numeros
//1 al quinto premio
if ($rscomercializado->Rowcount() == 0 && ($_SESSION['juego_tipo'] == 'EXTRAORDINARIA') && $sale_o_sale[1] == 'SI') {
    $pdf->SetXY(35, 80);
    $pdf->SetFont('Arial', 'BI', 12);
}

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(25, 85);
$pdf->Cell(156, 45, '', 1, 0, 1);

$pdf->SetXY(40, 90);
$pdf->Cell(20, 0, '1er. Premio de', 0, 0, 1);
$pdf->SetXY(65, 90);
$pdf->SetFont('Arial', 'b', 13);

if ($rscomercializado->Rowcount() == 0 && ($_SESSION['juego_tipo'] == 'EXTRAORDINARIA') && $sale_o_sale[1] == 'SI') {
    $pdf->Cell(20, 0, 'No Vendido (*)', 0, 0, 1);
} else {
    $pdf->Cell(20, 0, '$ ' . number_format($efectivo[1], 0, ',', '.'), 0, 0, 1);
}

//$pdf->Cell(20, 0, '$ ' . number_format($efectivo[1], 0, ',', '.'), 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(80, 90);
$pdf->Cell(20, 0, '                               Certificado      Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(140, 90);
$pdf->Cell(20, 0, $quini[1], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(40, 98);
$pdf->Cell(20, 0, '2do. Premio de', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(65, 98);
$pdf->Cell(20, 0, '$ ' . number_format($efectivo[2], 0, ',', '.'), 0, 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(80, 98);
$pdf->Cell(20, 0, '                               Certificado      Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(140, 98);
$pdf->Cell(20, 0, $quini[2], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(40, 106);
$pdf->Cell(20, 0, '3er. Premio de', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(65, 106);
$pdf->Cell(20, 0, '$ ' . number_format($efectivo[3], 0, ',', '.'), 0, 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(80, 106);
$pdf->Cell(20, 0, '                               Certificado      Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(140, 106);
$pdf->Cell(20, 0, $quini[3], 0, 0, 1);
$pdf->SetFont('Arial', '', 9);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(40, 114);
$pdf->Cell(20, 0, '4to. Premio de', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(65, 114);
$pdf->Cell(20, 0, '$ ' . number_format($efectivo[4], 0, ',', '.'), 0, 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(80, 114);
$pdf->Cell(20, 0, '                               Certificado      Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(140, 114);
$pdf->Cell(20, 0, $quini[4], 0, 0, 1);
$pdf->SetFont('Arial', '', 9);

$pdf->SetXY(40, 122);
$pdf->Cell(20, 0, '5to. Premio de', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(65, 122);
$pdf->Cell(20, 0, '$ ' . number_format($efectivo[5], 0, ',', '.'), 0, 0, 1);
$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(80, 122);
$pdf->Cell(20, 0, '                               Certificado      Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(140, 122);
$pdf->Cell(20, 0, $quini[5], 0, 1, 1);
$pdf->SetXY(50, 127);
if ($_SESSION['juego_tipo'] == 'EXTRAORDINARIA' && $rscomercializado->Rowcount() == 0 && $sale_o_sale[1] == 'SI') {
    $pdf->SetFont('Arial', 'b', 10);
    $pdf->Cell(20, 0, '(*) Premio $' . number_format($efectivo[1], 0, ',', '.') . ' Sujeto a modalidad Sortea Hasta Que Sale', 0, 0, 1);
}
$pdf->SetFont('Arial', '', 9);

$pdf->SetFont('Arial', 'b', 13);
$pdf->SetXY(25, 132);
$pdf->Cell(156, 55, '', 1, 0, 1);

$pdf->SetXY(35, 136);
$pdf->Cell(20, 0, '5 Premios de            $ ' . number_format($efectivo[6], 0, ',', '.'), 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(68, 143);
$pdf->Cell(25, 0, '6to.      Certificado      Nro.:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(118, 143);
$pdf->Cell(20, 0, $quini[6], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(68, 153);
$pdf->Cell(25, 0, '7mo.    Certificado      Nro.:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(118, 153);
$pdf->Cell(20, 0, $quini[7], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(68, 163);
$pdf->Cell(25, 0, '8vo.     Certificado      Nro.:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(118, 163);
$pdf->Cell(20, 0, $quini[8], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(68, 173);
$pdf->Cell(25, 0, '9no.     Certificado      Nro.:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(118, 173);
$pdf->Cell(20, 0, $quini[9], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(68, 183);
$pdf->Cell(25, 0, '10mo.  Certificado      Nro.:', 0, 0, 'L');
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(118, 183);
$pdf->Cell(20, 0, $quini[10], 0, 0, 1);

//11 al 20 premio

$pdf->SetXY(25, 190);
$pdf->Cell(156, 55, '', 1, 0, 1);

$pdf->SetXY(35, 195);
$pdf->Cell(20, 0, '10 Premios de            $ ' . number_format($efectivo[11], 0, ',', '.'), 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(50, 201);
$pdf->Cell(20, 0, '11er. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(85, 201);
$pdf->Cell(20, 0, $quini[11], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(115, 201);
$pdf->Cell(20, 0, '16to. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(150, 201);
$pdf->Cell(20, 0, $quini[16], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(50, 211);
$pdf->Cell(20, 0, '12do. Certificado  Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(85, 211);
$pdf->Cell(20, 0, $quini[12], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(115, 211);
$pdf->Cell(20, 0, '17mo. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(150, 211);
$pdf->Cell(20, 0, $quini[17], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(50, 221);
$pdf->Cell(20, 0, '13er. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(85, 221);
$pdf->Cell(20, 0, $quini[13], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(115, 221);
$pdf->Cell(20, 0, '18vo. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(150, 221);
$pdf->Cell(20, 0, $quini[18], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(50, 231);
$pdf->Cell(20, 0, '14to. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(85, 231);
$pdf->Cell(20, 0, $quini[14], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(115, 231);
$pdf->Cell(20, 0, '19no. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(150, 231);
$pdf->Cell(20, 0, $quini[19], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(50, 241);
$pdf->Cell(20, 0, '15to. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(85, 241);
$pdf->Cell(20, 0, $quini[15], 0, 0, 1);

$pdf->SetFont('Arial', '', 9);
$pdf->SetXY(115, 241);
$pdf->Cell(20, 0, '20mo. Certificado Nro.:', 0, 0, 1);
$pdf->SetFont('Arial', 'B', 13);
$pdf->SetXY(150, 241);
$pdf->Cell(20, 0, $quini[20], 0, 0, 1);

//hora
$pdf->SetFont('Arial', 'B', 10);
$pdf->SetXY(87, 250);
$pdf->Cell(20, 5, 'Hora de Finalizacion:............', 0, 0, 'L');

//firmas

$pdf->SetFont('Arial', 'B', 9);
$pdf->SetXY(25, 265);
$pdf->Cell(150, 0, '___________________                                    ___________________                                  _________________________', 0, 1, 'J');
$pdf->SetXY(25, 271);
$pdf->Cell(150, 0, '          Operador                                                     Jefe de Sorteos                                              Firma Escribano Actuante', 0, 0, 'J');
//$pdf->Cell(150,5,'  Firma Responsable                                                     Firma Responsable                                          Firma Escribano Actuante',0,0,'J');
$pdf->SetXY(28, 275);
$pdf->Cell(25, 0, $usuario, 0, 0, 'C');

$pdf->SetXY(96, 275);
$pdf->Cell(25, 0, $jefe, 0, 0, 'C');

$pdf->SetXY(166, 275);
$pdf->Cell(25, 0, $escribano, 0, 0, 'C');

$pdf->Output();
