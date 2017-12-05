<?php
/*
	DUPLICAR ESTE ARCHIVO CUANTAS VECES SEA NECESARIO PARA CAMBIAR LAS CONFIGURACIONES Y ESTETICA GENERAL DEL REPORTE
*/

class RPDF extends FPDF{
	function RPDF($a, $b, $c){
		if($b != NULL || $c != NULL)
			die('El constructor con 3 parametros aún no ha sido desarrollado.');
		$this->FPDF($a);
		$this->CeldaMargenIzquierdo = 0;
		$this->SetFillColor(240);
		
		if($this->CurOrientation == 'P'){
			$this->AnchoContenido = 190;
			$this->AltoMaximo = 273;
		}else{
			$this->AnchoContenido = 275;
			$this->AltoMaximo = 188;
		}
	}

	//Cabecera de página
	function Header(){
		global $tituloRPDF, $subTituloRPDF;
		
		$this->SetFont('Times', 'B', 14);
		$this->SetY(0);
		$this->Cell(0, 10, 'LOTERIA DE CORDOBA S.E.', 0, 1, 'C'); //NO BORRAR!, SI MODIFICAR
		$this->SetFont('Times', 'I', 10);
		$this->Cell(0, 5, '27 de Abril 185 - Córdoba - República Argentina', 0, 1, 'C'); //NO BORRAR!, SI MODIFICAR

		if($this->CurOrientation == 'P'){
			$this->Line(10, 17, 200, 17);
			$this->Image($this->directorioBase.'img/logo.png', 10, 1, 30, 15);
			$this->Image($this->directorioBase.'img/logo.png', 175, 1, 30, 15);
		}else{
			$this->Line(10, 17, 285, 17);
			$this->Image($this->directorioBase.'img/logo.png', 10, 1, 30, 15);
			$this->Image($this->directorioBase.'img/logo.png', 255, 1, 30, 15);
		}
		
		$this->SetY(18);
		
		if($tituloRPDF != null){
			$this->SetFont('Times', 'B', 14);
			$this->Cell(0, 8, strtoupper($tituloRPDF), 0, 1, 'C');
		}else{
			RPDF_ReportarError($this, 'POR FAVOR, INGRESE UN TITULO');
			RPDF_Salir($this);
		}
		
		if($subTituloRPDF != null){
			$this->SetFont('Times', 'B', 12);
			$this->Cell(0, 5, strtoupper($subTituloRPDF), 0, 1, 'C');
			$this->AltoDeInicio = 33;
		}else
			$this->AltoDeInicio = 28;
			
		if($this->setup['marcaDeAgua']){
			$this->SetTextColor(190);
			$this->Rotate(56);
			if(strtoupper($this->setup['marcaDeAgua']['texto']) == 'DUPLICADO'){
				$this->SetFont('Arial', '', 95);
				$this->Text(-180, 190, 'D U P L I C A D O');
			}else if(strtoupper($this->setup['marcaDeAgua']['texto']) == 'TRIPLICADO'){
				$this->SetFont('Arial', '', 90);
				$this->Text(-180, 190, 'T R I P L I C A D O');
			}else{
				$this->Rotate(0);
				RPDF_ReportarError($this, 'El texto de marca de agua solo puede ser: { DUPLICADO, TRIPLICADO }');
				RPDF_ReportarError($this, 'Si desea añadir más textos, comunicarse con Broda Noel');
				RPDF_Salir($this);
			}
			$this->Rotate(0);
		}
		
		$this->SetFont('Arial', '', 10);
		$this->SetY($this->GetY() + 2);
	}
	
	//Pie de página
	function Footer(){
		$this->SetY(-7);
		$y = $this->GetY();
		if($this->CurOrientation == 'P')
			$this->Line(10, $y, 200, $y);
		else
			$this->Line(10, $y, 285, $y);
		$this->SetFont('Arial', 'I', 8);
		$this->Cell($this->AnchoContenido / 3, 7, $_SESSION['nombre_usuario'], 0, 0,'L');
		$this->Cell($this->AnchoContenido / 3, 7, date('d/m/Y h:i:s A'), 0, 0, 'C');
		$this->Cell($this->AnchoContenido / 3, 7, $this->PageNo()."/{nb}", 0, 0, 'R');
	}
	
	var $angle = 0;

	function Rotate($angle,$x=-1,$y=-1){
		if($x==-1)
			$x=$this->x;
		if($y==-1)
			$y=$this->y;
		if($this->angle!=0)
			$this->_out('Q');
		$this->angle=$angle;
		if($angle!=0){
			$angle*=M_PI/180;
			$c=cos($angle);
			$s=sin($angle);
			$cx=$x*$this->k;
			$cy=($this->h-$y)*$this->k;
			$this->_out(sprintf('q %.5F %.5F %.5F %.5F %.2F %.2F cm 1 0 0 1 %.2F %.2F cm',$c,$s,-$s,$c,$cx,$cy,-$cx,-$cy));
		}
	}

	function _endpage(){
		if($this->angle!=0){
			$this->angle=0;
			$this->_out('Q');
		}
		parent::_endpage();
	}
}
 ?>