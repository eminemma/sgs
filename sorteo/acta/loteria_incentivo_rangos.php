<?php 
@session_start();
include_once dirname(__FILE__).'/../../db.php';
//$db->debug=true;

 
//$titulo=strtoupper('PROGRAMA DE INCENTIVOS ESPECIALES -  '.$_SESSION['juego'].' EMISION '.$_SESSION['sorteo']); 
$titulo=strtoupper('PROGRAMA DE INCENTIVOS -  GORDO DE INVIERNO 2017 - EMISION 4840'); 
$titulo2=strtoupper('RANGOS ASIGNADOS A AGENCIAS');

require("header_listado.php"); 
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');


$pdf=new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

$incentivo='';
$y=0;

//90,89,88,87,86,85,84,83,82,81,80,79,78,77,76,75,74,73,72,71,70,69,68,67,66,64,62



// 
try{
	$rs_incentivo=sql("SELECT I.ID_INCENTIVO, UPPER(I.DESCRIPCION) AS INCENTIVO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,A.LOCALIDAD,A.CHANCES,A.DESDE,A.HASTA
                    FROM SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE I.ID_INCENTIVO=A.ID_INCENTIVO
                    AND I.ID_JUEGO=?
                    AND I.SORTEO=?
					AND A.ID_INCENTIVO in (151)
                    ORDER BY I.ID_INCENTIVO asc, A.ID_SUCURSAL, A.ID_AGENCIA,A.DESDE",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch(exception $e){	die($db->ErrorMsg()); }

while($row_incentivo = siguiente($rs_incentivo)){
  if ($row_incentivo->ID_INCENTIVO != $incentivo){
    $pdf->SetFont('Arial','B',12); $pdf->Cell(15,10,'',0,1,'L');    
	
	$pdf->Cell(15,10,utf8_decode($row_incentivo->INCENTIVO) . "  ",0,1,'L');    
	
	$incentivo=$row_incentivo->ID_INCENTIVO;
    $pdf->SetFont('Arial','B',8); $pdf->Cell(40,5,'SUCURSAL',1,0,'C'); $pdf->Cell(17,5,'NRO AGEN',1,0,'C'); $pdf->Cell(50,5,'TITULAR',1,0,'C'); $pdf->Cell(38,5,'LOCALIDAD',1,0,'C'); $pdf->Cell(15,5,'CHANCES',1,0,'C');
    $pdf->Cell(15,5,'DESDE',1,0,'C'); $pdf->Cell(15,5,'HASTA',1,1,'C');

  } elseif((int)$pdf->GetY() == 276){
    $pdf->SetFont('Arial','B',8); $pdf->Cell(40,5,'SUCURSAL',1,0,'C'); $pdf->Cell(17,5,'NRO AGEN',1,0,'C'); $pdf->Cell(50,5,'TITULAR',1,0,'C'); $pdf->Cell(38,5,'LOCALIDAD',1,0,'C'); $pdf->Cell(15,5,'CHANCES',1,0,'C');
    $pdf->Cell(15,5,'DESDE',1,0,'C'); $pdf->Cell(15,5,'HASTA',1,1,'C');
  }

  $pdf->SetFont('Arial','',8);
  $pdf->Cell(40,5,str_pad($row_incentivo->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_incentivo->DESCRIPCION_SUCURSAL,'B',0,'L');
  $pdf->Cell(17,5,str_pad($row_incentivo->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'C');
  $pdf->Cell(50,5,  utf8_decode(substr($row_incentivo->DESCRIPCION_AGENCIA,0,35))  ,'B',0,'L');
  $pdf->Cell(38,5,substr($row_incentivo->LOCALIDAD,0,22),'B',0,'L');
  $pdf->Cell(15,5,$row_incentivo->CHANCES,'B',0,'C');
  $pdf->Cell(15,5,$row_incentivo->DESDE,'B',0,'C');
  $pdf->Cell(15,5,$row_incentivo->HASTA,'B',1,'C');
}


// 
try{
	$rs_incentivo=sql("SELECT I.ID_INCENTIVO, UPPER(I.DESCRIPCION) AS INCENTIVO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,A.LOCALIDAD,A.CHANCES,A.DESDE,A.HASTA
                    FROM SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE I.ID_INCENTIVO=A.ID_INCENTIVO
                    AND I.ID_JUEGO=?
                    AND I.SORTEO=?
					AND A.ID_INCENTIVO in (156)
                    ORDER BY I.ID_INCENTIVO asc, A.ID_SUCURSAL, A.ID_AGENCIA,A.DESDE",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch(exception $e){	die($db->ErrorMsg()); }

while($row_incentivo = siguiente($rs_incentivo)){
  if ($row_incentivo->ID_INCENTIVO != $incentivo){
    $pdf->SetFont('Arial','B',12); $pdf->Cell(15,10,'',0,1,'L');    
	
	$pdf->Cell(15,10,utf8_decode($row_incentivo->INCENTIVO) . "  ",0,1,'L');    
	
	$incentivo=$row_incentivo->ID_INCENTIVO;
    $pdf->SetFont('Arial','B',8); $pdf->Cell(40,5,'SUCURSAL',1,0,'C'); $pdf->Cell(17,5,'NRO AGEN',1,0,'C'); $pdf->Cell(50,5,'TITULAR',1,0,'C'); $pdf->Cell(38,5,'LOCALIDAD',1,0,'C'); $pdf->Cell(15,5,'CHANCES',1,0,'C');
    $pdf->Cell(15,5,'DESDE',1,0,'C'); $pdf->Cell(15,5,'HASTA',1,1,'C');

  } elseif((int)$pdf->GetY() == 276){
    $pdf->SetFont('Arial','B',8); $pdf->Cell(40,5,'SUCURSAL',1,0,'C'); $pdf->Cell(17,5,'NRO AGEN',1,0,'C'); $pdf->Cell(50,5,'TITULAR',1,0,'C'); $pdf->Cell(38,5,'LOCALIDAD',1,0,'C'); $pdf->Cell(15,5,'CHANCES',1,0,'C');
    $pdf->Cell(15,5,'DESDE',1,0,'C'); $pdf->Cell(15,5,'HASTA',1,1,'C');
  }

  $pdf->SetFont('Arial','',8);
  $pdf->Cell(40,5,str_pad($row_incentivo->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_incentivo->DESCRIPCION_SUCURSAL,'B',0,'L');
  $pdf->Cell(17,5,str_pad($row_incentivo->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'C');
  $pdf->Cell(50,5,  utf8_decode(substr($row_incentivo->DESCRIPCION_AGENCIA,0,35))  ,'B',0,'L');
  $pdf->Cell(38,5,substr($row_incentivo->LOCALIDAD,0,22),'B',0,'L');
  $pdf->Cell(15,5,$row_incentivo->CHANCES,'B',0,'C');
  $pdf->Cell(15,5,$row_incentivo->DESDE,'B',0,'C');
  $pdf->Cell(15,5,$row_incentivo->HASTA,'B',1,'C');
}


// 
try{
	$rs_incentivo=sql("SELECT I.ID_INCENTIVO, UPPER(I.DESCRIPCION) AS INCENTIVO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,A.LOCALIDAD,A.CHANCES,A.DESDE,A.HASTA
                    FROM SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE I.ID_INCENTIVO=A.ID_INCENTIVO
                    AND I.ID_JUEGO=?
                    AND I.SORTEO=?
					AND A.ID_INCENTIVO in (161)
                    ORDER BY I.ID_INCENTIVO asc, A.ID_SUCURSAL, A.ID_AGENCIA,A.DESDE",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch(exception $e){	die($db->ErrorMsg()); }

while($row_incentivo = siguiente($rs_incentivo)){
  if ($row_incentivo->ID_INCENTIVO != $incentivo){
    $pdf->SetFont('Arial','B',12); $pdf->Cell(15,10,'',0,1,'L');    
	
	$pdf->Cell(15,10,utf8_decode($row_incentivo->INCENTIVO) . "  ",0,1,'L');    
	
	$incentivo=$row_incentivo->ID_INCENTIVO;
    $pdf->SetFont('Arial','B',8); $pdf->Cell(40,5,'SUCURSAL',1,0,'C'); $pdf->Cell(17,5,'NRO AGEN',1,0,'C'); $pdf->Cell(50,5,'TITULAR',1,0,'C'); $pdf->Cell(38,5,'LOCALIDAD',1,0,'C'); $pdf->Cell(15,5,'CHANCES',1,0,'C');
    $pdf->Cell(15,5,'DESDE',1,0,'C'); $pdf->Cell(15,5,'HASTA',1,1,'C');

  } elseif((int)$pdf->GetY() == 276){
    $pdf->SetFont('Arial','B',8); $pdf->Cell(40,5,'SUCURSAL',1,0,'C'); $pdf->Cell(17,5,'NRO AGEN',1,0,'C'); $pdf->Cell(50,5,'TITULAR',1,0,'C'); $pdf->Cell(38,5,'LOCALIDAD',1,0,'C'); $pdf->Cell(15,5,'CHANCES',1,0,'C');
    $pdf->Cell(15,5,'DESDE',1,0,'C'); $pdf->Cell(15,5,'HASTA',1,1,'C');
  }

  $pdf->SetFont('Arial','',8);
  $pdf->Cell(40,5,str_pad($row_incentivo->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_incentivo->DESCRIPCION_SUCURSAL,'B',0,'L');
  $pdf->Cell(17,5,str_pad($row_incentivo->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'C');
  $pdf->Cell(50,5,  utf8_decode(substr($row_incentivo->DESCRIPCION_AGENCIA,0,35))  ,'B',0,'L');
  $pdf->Cell(38,5,substr($row_incentivo->LOCALIDAD,0,22),'B',0,'L');
  $pdf->Cell(15,5,$row_incentivo->CHANCES,'B',0,'C');
  $pdf->Cell(15,5,$row_incentivo->DESDE,'B',0,'C');
  $pdf->Cell(15,5,$row_incentivo->HASTA,'B',1,'C');
}

// 
try{
	$rs_incentivo=sql("SELECT I.ID_INCENTIVO, UPPER(I.DESCRIPCION) AS INCENTIVO,A.ID_SUCURSAL,A.DESCRIPCION_SUCURSAL,A.ID_AGENCIA,A.DESCRIPCION_AGENCIA,A.LOCALIDAD,A.CHANCES,A.DESDE,A.HASTA
                    FROM SGS.T_INCENTIVOS_AGENCIAS A,SGS.T_INCENTIVOS I
                    WHERE I.ID_INCENTIVO=A.ID_INCENTIVO
                    AND I.ID_JUEGO=?
                    AND I.SORTEO=?
					AND A.ID_INCENTIVO in (166)
                    ORDER BY I.ID_INCENTIVO asc, A.ID_SUCURSAL, A.ID_AGENCIA,A.DESDE",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch(exception $e){	die($db->ErrorMsg()); }

while($row_incentivo = siguiente($rs_incentivo)){
  if ($row_incentivo->ID_INCENTIVO != $incentivo){
    $pdf->SetFont('Arial','B',12); $pdf->Cell(15,10,'',0,1,'L');    
	
	$pdf->Cell(15,10,utf8_decode($row_incentivo->INCENTIVO) . "  ",0,1,'L');    
	
	$incentivo=$row_incentivo->ID_INCENTIVO;
    $pdf->SetFont('Arial','B',8); $pdf->Cell(40,5,'SUCURSAL',1,0,'C'); $pdf->Cell(17,5,'NRO AGEN',1,0,'C'); $pdf->Cell(50,5,'TITULAR',1,0,'C'); $pdf->Cell(38,5,'LOCALIDAD',1,0,'C'); $pdf->Cell(15,5,'CHANCES',1,0,'C');
    $pdf->Cell(15,5,'DESDE',1,0,'C'); $pdf->Cell(15,5,'HASTA',1,1,'C');

  } elseif((int)$pdf->GetY() == 276){
    $pdf->SetFont('Arial','B',8); $pdf->Cell(40,5,'SUCURSAL',1,0,'C'); $pdf->Cell(17,5,'NRO AGEN',1,0,'C'); $pdf->Cell(50,5,'TITULAR',1,0,'C'); $pdf->Cell(38,5,'LOCALIDAD',1,0,'C'); $pdf->Cell(15,5,'CHANCES',1,0,'C');
    $pdf->Cell(15,5,'DESDE',1,0,'C'); $pdf->Cell(15,5,'HASTA',1,1,'C');
  }

  $pdf->SetFont('Arial','',8);
  $pdf->Cell(40,5,str_pad($row_incentivo->ID_SUCURSAL,2,0,STR_PAD_LEFT).' - '.$row_incentivo->DESCRIPCION_SUCURSAL,'B',0,'L');
  $pdf->Cell(17,5,str_pad($row_incentivo->ID_AGENCIA, 5,0,STR_PAD_LEFT),'B',0,'C');
  $pdf->Cell(50,5,  utf8_decode(substr($row_incentivo->DESCRIPCION_AGENCIA,0,35))  ,'B',0,'L');
  $pdf->Cell(38,5,substr($row_incentivo->LOCALIDAD,0,22),'B',0,'L');
  $pdf->Cell(15,5,$row_incentivo->CHANCES,'B',0,'C');
  $pdf->Cell(15,5,$row_incentivo->DESDE,'B',0,'C');
  $pdf->Cell(15,5,$row_incentivo->HASTA,'B',1,'C');
}

$pdf->Output();
?>
