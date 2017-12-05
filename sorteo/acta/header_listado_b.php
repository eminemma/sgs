<?php 
@session_start();
header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public"); 
require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');

class PDF extends FPDF{
	//Cabecera de página
	function Header(){}
	//Pie de página
}