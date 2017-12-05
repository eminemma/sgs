<?php 
session_start();

//	-----------------------------------------------------------------------------------------------------------
//	PARA QUE ESTE EJEMPLO FUNCIONE DEBEMOS CONTAR CON:
//		UNA BASE DE DATOS LLAMADA ejemplo_pdf
//		UNA TABLA LLAMADA autos
//		CON LOS SIGUIENTES CAMPOS:		pk_auto - marca - modelo -color - ano
//	-----------------------------------------------------------------------------------------------------------


//$color=$_POST['color']; 
$color = "Rojo";			//EL VALOR DE ESTA VARIABLE PODRIA VENIR DE UN FORMULARIO


include("conectar_base.php"); 


//	LISTAR AUTOS DE COLOR ROJO
//	-------------------------------------------------------------------------------------
$sql = 	"	SELECT 		* 
			FROM 		autos
			WHERE		color >= '$color' 	
		";		
$rs = mysql_query($sql) or die(mysql_error());
//	-------------------------------------------------------------------------------------




 
require('fpdf.php');
class PDF extends FPDF
{

//Cabecera de página
function Header()
	{
	//	DEFINO VARIABLE DE TIPO GLOBAL
	global $color;
	
    $this->SetFont('Arial','B',13);
	$this->SetFillColor(240,240,240);	
	
	$this->Cell(190,10,'Listado de autos color '.$color,0,0,'C');
	
	$this->setXY(10, 20);	
	
	$this->SetFont('Arial','B',10);
	$this->Cell(50,6,'Marca',1,0,'L',1);
	$this->Cell(50,6,'Modelo',1,0,'L',1);
	$this->Cell(50,6,'Color',1,0,'L',1);
	$this->Cell(40,6,'Año',1,1,'L',1);	
	
	$this->setXY(10, 21);	
    
	$this->SetFont('Arial','BUI',15);
    $this->Ln(5);
	}


//Pie de página
function Footer()
	{
    $this->SetY(-15);				    									//Posición: a 1,5 cm del final
	$this->Line(10, 285, 196, 285);
	$this->SetFont('Arial','',10);
    $this->Cell(0,20,'Página '.$this->PageNo().'/{nb}',0,0,'L');		    //Número de página
	}
}





$pdf=new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();



$pdf->SetFont('Arial','',10);

while ($row = mysql_fetch_assoc($rs)) 
	{
	$pdf->Cell(50,6,$row['marca'],1,0,'L');
	$pdf->Cell(50,6,$row['modelo'],1,0,'L');
	$pdf->Cell(50,6,$row['color'],1,0,'L');
	$pdf->Cell(40,6,$row['ano'],1,1,'L');
	} 


//$pdf->Output("Comprobantes/".$num_comprobante.".pdf", F);				//	guarda el archivo
$pdf->Output();														//	tira al navegador
?> 









