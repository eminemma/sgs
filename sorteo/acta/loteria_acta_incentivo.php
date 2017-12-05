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
$operador=utf8_decode($row_incentivo->OPERADOR);
$escribano=utf8_decode($row_incentivo->ESCRIBANO);


//echo $id_incentivo;
//$db->debug=true;
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
                    AND G.ID_INCENTIVO=?",array($_SESSION['id_juego'],$_SESSION['sorteo'],$id_incentivo));
}catch  (exception $e) { die($db->ErrorMsg());}


 
$titulo=strtoupper('ACTA PROGRAMA DE INCENTIVOS ESPECIALES -  '.$_SESSION['juego'].' EMISION '.$_SESSION['sorteo']); 
//$titulo=strtoupper('ACTA PROGRAMA DE INCENTIVOS ESPECIALES -  RASPAGUITA MUNDIAL SERIE Nº 82'); 
//$titulo2=strtoupper('INCENTIVO: '.$row_ganador->INCENTIVO);

require("header_listado.php"); 
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');


$pdf=new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

while($row_ganador = siguiente($rs_ganador)){
  $pdf->SetFont('Arial','B',12);
  $pdf->Cell(15,5,utf8_decode($row_ganador->INCENTIVO),0,1,'L');

  $pdf->SetFont('Arial','B',8);
  $pdf->Cell(15,5,'ALEAT.',1,0,'C');
  $pdf->Cell(30,5,'SUCURSAL',1,0,'C');
  $pdf->Cell(15,5,'Nro AGEN',1,0,'C');
  $pdf->Cell(50,5,'TITULAR',1,0,'C');
  $pdf->Cell(40,5,'LOCALIDAD',1,0,'C');
  $pdf->Cell(15,5,'PREMIO',1,0,'C');
  $pdf->Cell(15,5,'FECHA',1,1,'C');

//$pdf->Cell(40,5,'COMERCIALIZADO',1,1,'C');
  $pdf->Cell(15,5,str_pad($row_ganador->ALEATORIO,6,0,STR_PAD_LEFT),'B',0,'R');
  $pdf->Cell(30,5,str_pad($row_ganador->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_ganador->DESCRIPCION_SUCURSAL,'B',0,'C');
  $pdf->Cell(15,5,str_pad($row_ganador->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'C');
  $pdf->Cell(50,5,$row_ganador->DESCRIPCION_AGENCIA,'B',0,'C');
  $pdf->Cell(40,5,utf8_decode($row_ganador->LOCALIDAD),'B',0,'C');
  $pdf->Cell(15,5,'$'.$row_ganador->IMPORTE,'B',0,'L');
  //$pdf->Cell(15,5,' KIT MUN.','B',0,'L');
  $pdf->Cell(15,5,$fechasorteo,'B',1,'C');
}

//hora
$pdf->SetFont('Arial','B',10);
$pdf->SetXY(87,250);
$pdf->Cell(20,5,'Hora de Finalizacion:............',0,0,'L');
 
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
