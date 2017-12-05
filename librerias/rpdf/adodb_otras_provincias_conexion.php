<?php
//Aqui configure a gusto su conexion ADODB predeterminada
// error_reporting(E_ALL);
// ini_set('display_errors', 1);
@session_start();
global $dbRPDF;
$rnum=rand(0,99999999);
// include_once("../adodb/adodb.inc.php");				
// include_once('../adodb/adodb-exceptions.inc.php');	
require_once dirname(__FILE__).'/librerias/adodb/adodb.inc.php';
// require_once dirname(__FILE__).'/librerias/adodb/adodb-exceptions.inc.php';

//var_dump(file_exists("../adodb/adodb.inc.php"));

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
				 )", 'otras_provincias', 'esquema');
}
//$_SESSION['esquema'] = "slots_desarrollo";

catch  (exception $e) { die($dbRPDF->ErrorMsg()."<br><br><a href=\"index.php\">Regresar a pagina anterior.</a><br><br>");}