<?php 
@session_start();
include_once dirname(__FILE__).'/../../db.php';

//$db->debug=true;


//OBTENGO DATOS DEL SORTEO
try{
	$rs_incentivo=sql("SELECT TO_CHAR(FECHA_SORTEO, 'DD/MM/YYYY') AS FECHA_SORTEO,
              TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIOS, 'DD/MM/YYYY') AS FECHA_CADUCIDAD,       
              U.DESCRIPCION JEFE_SORTEO,       
              US.DESCRIPCION OPERADOR,
              ESC.DESCRIPCION     AS ESCRIBANO
              from sgs.T_INCENTIVO_SORTEO       SO,
              sgs.T_ESCRIBANO    ESC,
              SUPERUSUARIO.USUARIOS U,
              SUPERUSUARIO.USUARIOS US
              where SO.ID_ESCRIBANO = ESC.ID_ESCRIBANO
              AND U.ID_USUARIO = SO.ID_JEFE
              AND US.ID_USUARIO = SO.ID_OPERADOR
              AND SO.SORTEO = ?
              AND SO.ID_JUEGO = ?",array($_SESSION['sorteo'],$_SESSION['id_juego']));
}catch(exception $e){	die($db->ErrorMsg()); }
$row_incentivo = siguiente($rs_incentivo);
$fechasorteo=$row_incentivo->FECHA_SORTEO;
$fechacaduca=$row_incentivo->FECHA_CADUCIDAD;

$jefesorteo=utf8_decode($row_incentivo->JEFE_SORTEO);
//$jefesorteo=utf8_decode('Roca Joaquin');

$operador=utf8_decode($row_incentivo->OPERADOR);
$escribano=utf8_decode($row_incentivo->ESCRIBANO);

//$db->debug=true;

//echo $id_incentivo;
//$db->debug=true;
/*
try   {
  $rs_ganador = sql("SELECT UPPER(I.DESCRIPCION) AS INCENTIVO,G.ALEATORIO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,I.IMPORTE,A.LOCALIDAD
                    FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE G.ID_AGENCIA = A.ID_AGENCIA
                    AND G.ID_SUCURSAL = A.ID_SUCURSAL
                    AND G.ID_INCENTIVO = A.ID_INCENTIVO
                    AND I.ID_JUEGO = G.ID_JUEGO
                    AND I.ID_INCENTIVO = G.ID_INCENTIVO
                    AND I.SORTEO = G.SORTEO
                    AND G.ID_JUEGO=?
                    AND G.SORTEO=?
                    ORDER BY I.ID_INCENTIVO",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch  (exception $e) { die($db->ErrorMsg());}
*/

$titulo=strtoupper('ACTA FINAL PROGRAMA DE INCENTIVOS ESPECIALES'); //.$_SESSION['juego'].' EMISION '.$_SESSION['sorteo']
//$titulo=strtoupper('ACTA FINAL PROGRAMA DE INCENTIVOS ESPECIALES -  RASPAGUITA MUNDIAL '); 
//$titulo2=strtoupper('INCENTIVO: '.$row_ganador->INCENTIVO);

//require("header_listado.php"); 
require("header_listado_acta_incentivo.php"); 
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');

$pdf=new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();
$pdf->SetFont('Arial','B',9);

$pdf->SetXY(15,40);//".date('d')."
$pdf->Cell(0,5,utf8_decode("En la Ciudad de Córdoba Capital de la Provincia de igual nombre, a ".date('d')." días del mes de octubre del año ".date('Y')." reunidos en el"),0,1,'L');
$pdf->Cell(0,5,utf8_decode("Salón de Sorteos de Lotería de Córdoba, sito en calle 27 de Abril 185 de esta Ciudad el señor Jefe de Sorteos y Escribano"),0,1,'L');
$pdf->Cell(0,5,utf8_decode("actuante indicados al pie de la presente, a efectos de la fiscalización del sorteo Programa de Incentivos GORDITO DE INVIERNO "),0,1,'L');
$pdf->Cell(0,5,utf8_decode("2017 emision 4840 de LOTERIA DE CORDOBA el que se realizará con el total de agencias que alcanzaron y/o superaron  "),0,1,'L');
$pdf->Cell(0,5,utf8_decode("los objetivos. Conforme listado y cantidad de chances provista y asignada por el Departamento Juegos de Lotería"),0,1,'L');
$pdf->Cell(0,5,utf8_decode(" de Córdoba, utilizando el sistema informático igualmente provisto por esta Institución."),0,1,'L');
$pdf->Cell(0,15,utf8_decode("Realizado el sorteo resultan ganadoras las siguientes agencias:"),0,1,'L');


//$pdf->SetX(25);


$sql_ids_incentivos = 'in (151,152,153,154,155)';
try{
  $rs_ganador = sql("SELECT UPPER(I.DESCRIPCION) AS INCENTIVO,G.ALEATORIO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,I.IMPORTE,A.LOCALIDAD
                    FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE G.ID_AGENCIA = A.ID_AGENCIA
                    AND G.ID_SUCURSAL = A.ID_SUCURSAL
                    AND G.ID_INCENTIVO_A = A.ID_INCENTIVO
                    AND I.ID_JUEGO = G.ID_JUEGO
                    AND I.ID_INCENTIVO = G.ID_INCENTIVO
                    AND I.SORTEO = G.SORTEO
                    AND G.ID_JUEGO=?
                    AND G.SORTEO=?
                    AND G.ID_INCENTIVO_A $sql_ids_incentivos",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch  (exception $e) { die($db->ErrorMsg());}

$ix = true;
while($row_ganador = siguiente($rs_ganador)){
	if($ix){
		$pdf->Cell(15,5,utf8_decode($row_ganador->INCENTIVO) . ' ',0,1,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(15,5,'ALEAT.',1,0,'C');
		$pdf->Cell(30,5,'SUCURSAL',1,0,'C');
		$pdf->Cell(15,5,'Nro AGEN',1,0,'C');
		$pdf->Cell(50,5,'TITULAR',1,0,'C');
		$pdf->Cell(55,5,'LOCALIDAD',1,0,'C');
		$pdf->Cell(15,5,'PREMIO',1,1,'C');
		//$pdf->Cell(15,5,'FECHA',1,1,'C');
		$ix = false;
	}
	$pdf->Cell(15,5,str_pad($row_ganador->ALEATORIO,6,0,STR_PAD_LEFT),'B',0,'L');
	$pdf->Cell(30,5,str_pad($row_ganador->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_ganador->DESCRIPCION_SUCURSAL,'B',0,'L');
	$pdf->Cell(15,5,str_pad($row_ganador->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'R');
	$pdf->Cell(50,5,utf8_decode($row_ganador->DESCRIPCION_AGENCIA),'B',0,'L');
	$pdf->Cell(55,5,utf8_decode($row_ganador->LOCALIDAD),'B',0,'L');
	$pdf->Cell(15,5,'$'.$row_ganador->IMPORTE,'B',1,'L');
	//$pdf->Cell(15,5,$fechasorteo,'B',1,'C');
}
$pdf->Cell(25,7,'',0,1,'C');
$pdf->Cell(25,7,'',0,1,'C');







$sql_ids_incentivos = 'in (156,157,158,159,160)';
try   {
  $rs_ganador = sql("SELECT UPPER(I.DESCRIPCION) AS INCENTIVO,G.ALEATORIO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,I.IMPORTE,A.LOCALIDAD
                    FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE G.ID_AGENCIA = A.ID_AGENCIA
                    AND G.ID_SUCURSAL = A.ID_SUCURSAL
                    AND G.ID_INCENTIVO_A = A.ID_INCENTIVO
                    AND I.ID_JUEGO = G.ID_JUEGO
                    AND I.ID_INCENTIVO = G.ID_INCENTIVO
                    AND I.SORTEO = G.SORTEO
                    AND G.ID_JUEGO=?
                    AND G.SORTEO=?
                    AND G.ID_INCENTIVO_A $sql_ids_incentivos",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch  (exception $e) { die($db->ErrorMsg());}
$ix = true;
while($row_ganador = siguiente($rs_ganador)){
	if($ix){
		$pdf->Cell(15,5,utf8_decode($row_ganador->INCENTIVO) . ' ',0,1,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(15,5,'ALEAT.',1,0,'C');
		$pdf->Cell(30,5,'SUCURSAL',1,0,'C');
		$pdf->Cell(15,5,'Nro AGEN',1,0,'C');
		$pdf->Cell(50,5,'TITULAR',1,0,'C');
		$pdf->Cell(55,5,'LOCALIDAD',1,0,'C');
		$pdf->Cell(15,5,'PREMIO',1,1,'C');
		//$pdf->Cell(15,5,'FECHA',1,1,'C');
		$ix = false;
	}
	$pdf->Cell(15,5,str_pad($row_ganador->ALEATORIO,6,0,STR_PAD_LEFT),'B',0,'L');
	$pdf->Cell(30,5,str_pad($row_ganador->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_ganador->DESCRIPCION_SUCURSAL,'B',0,'L');
	$pdf->Cell(15,5,str_pad($row_ganador->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'R');
	$pdf->Cell(50,5,utf8_decode($row_ganador->DESCRIPCION_AGENCIA),'B',0,'L');
	$pdf->Cell(55,5,utf8_decode($row_ganador->LOCALIDAD),'B',0,'L');
	$pdf->Cell(15,5,'$'.$row_ganador->IMPORTE,'B',1,'L');
	//$pdf->Cell(15,5,$fechasorteo,'B',1,'C');
}
$pdf->Cell(25,7,'',0,1,'C');
$pdf->Cell(25,7,'',0,1,'C');






$sql_ids_incentivos = 'in (161,162,163,164,165)';
try   {
  $rs_ganador = sql("SELECT UPPER(I.DESCRIPCION) AS INCENTIVO,G.ALEATORIO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,I.IMPORTE,A.LOCALIDAD
                    FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE G.ID_AGENCIA = A.ID_AGENCIA
                    AND G.ID_SUCURSAL = A.ID_SUCURSAL
                    AND G.ID_INCENTIVO_A = A.ID_INCENTIVO
                    AND I.ID_JUEGO = G.ID_JUEGO
                    AND I.ID_INCENTIVO = G.ID_INCENTIVO
                    AND I.SORTEO = G.SORTEO
                    AND G.ID_JUEGO=?
                    AND G.SORTEO=?
                    AND G.ID_INCENTIVO_A $sql_ids_incentivos",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch  (exception $e) { die($db->ErrorMsg());}
$ix = true;
while($row_ganador = siguiente($rs_ganador)){
	if($ix){
		$pdf->Cell(15,5,utf8_decode($row_ganador->INCENTIVO) . ' ',0,1,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(15,5,'ALEAT.',1,0,'C');
		$pdf->Cell(30,5,'SUCURSAL',1,0,'C');
		$pdf->Cell(15,5,'Nro AGEN',1,0,'C');
		$pdf->Cell(50,5,'TITULAR',1,0,'C');
		$pdf->Cell(55,5,'LOCALIDAD',1,0,'C');
		$pdf->Cell(15,5,'PREMIO',1,1,'C');
		$ix = false;
	}
	$pdf->Cell(15,5,str_pad($row_ganador->ALEATORIO,6,0,STR_PAD_LEFT),'B',0,'L');
	$pdf->Cell(30,5,str_pad($row_ganador->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_ganador->DESCRIPCION_SUCURSAL,'B',0,'L');
	$pdf->Cell(15,5,str_pad($row_ganador->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'R');
	$pdf->Cell(50,5,utf8_decode($row_ganador->DESCRIPCION_AGENCIA),'B',0,'L');
	$pdf->Cell(55,5,utf8_decode($row_ganador->LOCALIDAD),'B',0,'L');
	$pdf->Cell(15,5,'$'.$row_ganador->IMPORTE,'B',1,'L');
}
$pdf->Cell(25,7,'',0,1,'C');
$pdf->Cell(25,7,'',0,1,'C');




$sql_ids_incentivos = 'in (166,167,168,169,170)';
try   {
  $rs_ganador = sql("SELECT UPPER(I.DESCRIPCION) AS INCENTIVO,G.ALEATORIO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,I.IMPORTE,A.LOCALIDAD
                    FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE G.ID_AGENCIA = A.ID_AGENCIA
                    AND G.ID_SUCURSAL = A.ID_SUCURSAL
                    AND G.ID_INCENTIVO_A = A.ID_INCENTIVO
                    AND I.ID_JUEGO = G.ID_JUEGO
                    AND I.ID_INCENTIVO = G.ID_INCENTIVO
                    AND I.SORTEO = G.SORTEO
                    AND G.ID_JUEGO=?
                    AND G.SORTEO=?
                    AND G.ID_INCENTIVO_A $sql_ids_incentivos",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch  (exception $e) { die($db->ErrorMsg());}
$ix = true;
while($row_ganador = siguiente($rs_ganador)){
	if($ix){
		$pdf->Cell(15,5,utf8_decode($row_ganador->INCENTIVO) . ' ',0,1,'L');
		$pdf->SetFont('Arial','B',8);
		$pdf->Cell(15,5,'ALEAT.',1,0,'C');
		$pdf->Cell(30,5,'SUCURSAL',1,0,'C');
		$pdf->Cell(15,5,'Nro AGEN',1,0,'C');
		$pdf->Cell(50,5,'TITULAR',1,0,'C');
		$pdf->Cell(55,5,'LOCALIDAD',1,0,'C');
		$pdf->Cell(15,5,'PREMIO',1,1,'C');
		$ix = false;
	}
	$pdf->Cell(15,5,str_pad($row_ganador->ALEATORIO,6,0,STR_PAD_LEFT),'B',0,'L');
	$pdf->Cell(30,5,str_pad($row_ganador->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_ganador->DESCRIPCION_SUCURSAL,'B',0,'L');
	$pdf->Cell(15,5,str_pad($row_ganador->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'R');
	$pdf->Cell(50,5,utf8_decode($row_ganador->DESCRIPCION_AGENCIA),'B',0,'L');
	$pdf->Cell(55,5,utf8_decode($row_ganador->LOCALIDAD),'B',0,'L');
	$pdf->Cell(15,5,'$'.$row_ganador->IMPORTE,'B',1,'L');
}
$pdf->Cell(25,7,'',0,1,'C');





$pdf->Cell(25,5,'',0,1,'C');
$pdf->Cell(25,5,'',0,1,'C');
$pdf->Cell(25,5,'',0,1,'C');

$pdf->SetX(15);

//$pdf->Cell(0,5,utf8_decode("Con lo que siendo la hora ".date('H:i')." se da por finalizado el acto de lectura y ratificación susbribe el funcionario actuante,"),0,1,'L');
//$pdf->Cell(0,5,utf8_decode("todo por ante mi doy fe.-"),0,1,'L');

$pdf->Cell(0,5,utf8_decode("Con lo que se da por terminado el acto, previa lectura y ratificación de los actuantes, firman la presente por ante mí ..................................."),0,1,'L');
$pdf->Cell(0,5,utf8_decode("doy fe Escribano Autorizante, de todo lo que certifico; siendo las .............. hs., se da por finalizado el Sorteo"),0,1,'L');
 
//registro
$pdf->SetFont('Times','B',11);
$tmpy = $pdf->GetY()+4;
$pdf->SetY($tmpy);

$pdf->SetY($tmpy+5);
$pdf->Cell(20,0,utf8_decode('Consta en escritura Nº_________Sección_________ - Doy fe'),0,0,1);
$pdf->SetY($tmpy+5);
 
$pdf->SetFont('Times','B',9);

//$pdf->SetXY(25,250);
$pdf->SetXY(25,$pdf->GetY()+14);
$pdf->Cell(150,5,'___________________                                                                                                        _________________________',0,1,'J');
//$pdf->SetXY(25,258);
$pdf->SetXY(25,$pdf->GetY()+2);

$pdf->Cell(150,5,'       Jefe de Sorteos                                                                                                                Firma Escribano Actuante',0,0,'J');
//$pdf->Cell(150,5,'  Firma Responsable                                                     Firma Responsable                                          Firma Escribano Actuante',0,0,'J');
//$pdf->SetXY(28,271);
//$pdf->Cell(25,5,$operador,0,0,'C');
 
$pdf->SetXY(20,$pdf->GetY()+4);
$pdf->Cell(40,5,$jefesorteo,0,0,'C');

$pdf->SetXY(150,$pdf->GetY());
$pdf->Cell(20,5,$escribano,0,0,'C');
  
$pdf->Output();
?>
