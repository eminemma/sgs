<?php 
@session_start();
include_once dirname(__FILE__).'/../../db.php';
$id_incentivo=isset($_POST['id_incentivo']) ? $_POST['id_incentivo'] : $_GET['id_incentivo'];

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


//echo $id_incentivo;
//$db->debug=true;


try   {
  $rs_incentivo_descripcion = sql("SELECT UPPER(I.DESCRIPCION) AS INCENTIVO, I.importe
                    FROM SGS.T_INCENTIVOS_GANADORES G,SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE G.ID_AGENCIA = A.ID_AGENCIA
                    AND G.ID_SUCURSAL = A.ID_SUCURSAL
                    AND G.ID_INCENTIVO = A.ID_INCENTIVO
                    AND I.ID_JUEGO = G.ID_JUEGO
                    AND I.ID_INCENTIVO = G.ID_INCENTIVO
                    AND I.SORTEO = G.SORTEO
                    AND G.ID_JUEGO=?
                    AND G.SORTEO=?
                    AND G.ID_INCENTIVO=?",array($_SESSION['id_juego'],$_SESSION['sorteo'],$id_incentivo));
}catch  (exception $e) { die($db->ErrorMsg());}

$row_incentivo_descripcion = siguiente($rs_incentivo_descripcion);


$sql_ids_incentivos = '';

// RALLY
if($id_incentivo == 34){
	$sql_ids_incentivos = 'in (34,35)';
}
if($id_incentivo == 36){
	$sql_ids_incentivos = 'in (36)';
}
if($id_incentivo == 37){
	$sql_ids_incentivos = 'in (37,38,39,40,41,42,43,44,45,46,47,48,49,50,51,52,53,54,55,56,57,58,59,60,61)';
}

//GORDO INVIERNO 2015
if($id_incentivo == 62){
	$sql_ids_incentivos = 'in (62,64)';
}
if($id_incentivo == 66){
	$sql_ids_incentivos = 'in (66,67,68,69,70)';
}
if($id_incentivo == 71){
	$sql_ids_incentivos = 'in (71,72,73,74,75)';
}
if($id_incentivo == 76){
	$sql_ids_incentivos = 'in (76,77,78,79,80,81,82,83,84,85,86,87,88,89,90)';
}


if($id_incentivo == 91){$sql_ids_incentivos = 'in (91,92,93,94,95)';}
if($id_incentivo == 96){$sql_ids_incentivos = 'in (96,97,98,99,100)';}
if($id_incentivo == 101){$sql_ids_incentivos = 'in (101,102,103,104,105)';}
if($id_incentivo == 106){$sql_ids_incentivos = 'in (106,107,108,109,110)';}

if($id_incentivo == 111){$sql_ids_incentivos = 'in (111,112,113,114,115)';}
if($id_incentivo == 116){$sql_ids_incentivos = 'in (116,117,118,119,120)';}
if($id_incentivo == 121){$sql_ids_incentivos = 'in (121,122,123,124,125)';}
if($id_incentivo == 126){$sql_ids_incentivos = 'in (126,127,128,129,130)';}


if($id_incentivo == 131 || $id_incentivo == 135){$sql_ids_incentivos = 'in (131,132,133,134,135)';}
if($id_incentivo == 136 || $id_incentivo == 140){$sql_ids_incentivos = 'in (136,137,138,139,140)';}
if($id_incentivo == 141 || $id_incentivo == 145){$sql_ids_incentivos = 'in (141,142,143,144,145)';}
if($id_incentivo == 146 || $id_incentivo == 150){$sql_ids_incentivos = 'in (146,147,148,149,150)';}


if($id_incentivo == 151 || $id_incentivo == 155){$sql_ids_incentivos = 'in (151,152,153,154,155)';}
if($id_incentivo == 156 || $id_incentivo == 160){$sql_ids_incentivos = 'in (156,157,158,159,160)';}
if($id_incentivo == 161 || $id_incentivo == 165){$sql_ids_incentivos = 'in (161,162,163,164,165)';}
if($id_incentivo == 166 || $id_incentivo == 170){$sql_ids_incentivos = 'in (166,167,168,169,170)';}



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


 
//$titulo=strtoupper('ACTA PROGRAMA DE INCENTIVOS  -  RASPA RALLY'); //.$_SESSION['juego'].' EMISION '.$_SESSION['sorteo']); 
//$titulo=strtoupper('ACTA PROGRAMA DE INCENTIVOS ESPECIALES -  RASPAGUITA MUNDIAL SERIE Nº 82'); 
$titulo=strtoupper('ACTA PROGRAMA DE INCENTIVOS ESPECIALES - GORDO DE INVIERNO 2017'); // - GORDO DE NAVIDAD 2015
//$titulo2=strtoupper('INCENTIVO: '.$row_ganador->INCENTIVO);
$titulo3=strtoupper('EMISION 4840');
$titulo2=strtoupper('INCENTIVO DE $'.$row_incentivo_descripcion->IMPORTE /*.': '.utf8_decode($row_incentivo_descripcion->INCENTIVO) */ );

require("header_listado.php"); 
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');


$pdf=new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','B',12);
$pdf->Cell(15,5,utf8_decode($row_incentivo_descripcion->INCENTIVO),0,1,'L');

$pdf->SetFont('Arial','B',8);
$pdf->Cell(15,5,'ALEAT.',1,0,'C');
$pdf->Cell(30,5,'SUCURSAL',1,0,'C');
$pdf->Cell(15,5,'Nro AGEN',1,0,'C');
$pdf->Cell(60,5,'TITULAR',1,0,'C');
$pdf->Cell(45,5,'LOCALIDAD',1,0,'C');
$pdf->Cell(15,5,'PREMIO',1,1,'C');
//$pdf->Cell(15,5,'FECHA',1,1,'C');

while($row_ganador = siguiente($rs_ganador)){
  

//$pdf->Cell(40,5,'COMERCIALIZADO',1,1,'C');
  $pdf->Cell(15,5,str_pad($row_ganador->ALEATORIO,6,0,STR_PAD_LEFT),'B',0,'R');
  $pdf->Cell(30,5,str_pad($row_ganador->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_ganador->DESCRIPCION_SUCURSAL,'B',0,'L');
  $pdf->Cell(15,5,str_pad($row_ganador->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'C');
  $pdf->Cell(60,5,utf8_decode($row_ganador->DESCRIPCION_AGENCIA),'B',0,'L');
  $pdf->Cell(45,5,utf8_decode($row_ganador->LOCALIDAD),'B',0,'L');
  $pdf->Cell(15,5,'$'.$row_ganador->IMPORTE,'B',1,'L');
  //$pdf->Cell(15,5,' KIT MUN.','B',0,'L');
  //$pdf->Cell(15,5,$fechasorteo,'B',1,'C');
}

//hora
$pdf->SetFont('Arial','B',10);
$pdf->SetXY(87,250);
//$pdf->Cell(20,5,'Hora de Finalizacion:............',0,0,'L');
 
$pdf->SetFont('Times','B',9);
$pdf->SetXY(25,264);
$pdf->Cell(150,5,'              ___________________                                                                                                 _________________________',0,1,'J');
$pdf->SetXY(25,268);
$pdf->Cell(150,5,'                    Jefe de Sorteos                                                                                                          Firma Escribano Actuante',0,0,'J');
//$pdf->Cell(150,5,'  Firma Responsable                                                     Firma Responsable                                          Firma Escribano Actuante',0,0,'J');
/*$pdf->SetXY(28,271);
$pdf->Cell(25,5,$operador,0,0,'C');*/
 
$pdf->SetXY(40,271);
$pdf->Cell(25,5,$jefesorteo,0,0,'C');

$pdf->SetXY(152,271);
$pdf->Cell(25,5,$escribano,0,0,'C');
  
$pdf->Output();
?>
