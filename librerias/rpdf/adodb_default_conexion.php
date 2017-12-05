<?php
//Aqui configure a gusto su conexion ADODB predeterminada
@session_start();
global $dbRPDF;
$rnum=rand(0,99999999);
require_once dirname(__FILE__).'/librerias/adodb/adodb.inc.php';
// include_once("../adodb/adodb.inc.php");				
// include_once('../adodb/adodb-exceptions.inc.php');	

$dbRPDF = NewADOConnection("oci8po"); 		//oracle 9.2 o superior
//$dbRPDF = NewADOConnection("mysql"); 		//MySQL
//$dbRPDF = NewADOConnection("oci8"); 		//oracle 8i 0 9i
//$dbRPDF = NewADOConnection("oracle"); 	//oracle 7

//$_SESSION['esquema'] = "curso";	
//Conexion de Produccion
//Conexion de Desarrollo

try {
 
$dbRPDF->Connect("(DESCRIPTION =
					(ADDRESS =
				(PROTOCOL = TCP)
					(HOST = nscentral-scan.loteriadecordoba.com.ar)
					(PORT = 1521)
					(HASH = '.$rnum.')
				 )
			(CONNECT_DATA =(SERVER = DEDICATED)
      (SERVICE_NAME = CENTRAL))
				 )", "DU".$_SESSION['usuario'], $_SESSION['clave']);
}
//$_SESSION['esquema'] = "slots_desarrollo";

catch  (exception $e) { die($dbRPDF->ErrorMsg()."<br><br><a href=\"index.php\">Regresar a pagina anterior.</a><br><br>");}