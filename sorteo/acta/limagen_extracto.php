<?php 
session_start();
include ("../db_conecta_adodb.inc.php");
include ("../funcion.inc.php");
require('../pdf/rotation.php');
include_once ("../../ftp.inc.php");

error_reporting(E_ERROR);
//ini_set('display_errors','On');





header("Pragma: public");
header("Expires: 0");
header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
header("Cache-Control: public"); 
header("../numero_letra.php");

require('../pdf/fpdf.php');


class PDF extends PDF_ROTATE{			
	//CABECERA DE PAGINA
	function Header(){} 
	function Footer(){}
}

$xguardar=$_GET['guardar'];


if ($xguardar=='N'){ 

	$pdf=new PDF('L','mm','A4');
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$xnombre=$_GET['nombre'];
	$xurl=$_GET['url'];
	$ximagen=$_GET['imagen'];
	$pdf->Image('../image/'.$ximagen,5,20,288,170);
	$pdf->Output("");
	
	
} else {
	
	
	$pdf=new PDF('P','mm','A4');
	$pdf->AliasNbPages();
	$pdf->AddPage();
	$xnombre=$_GET['nombre'];
	$xurl='/'.$_GET['url'];
	$ximagen=$_GET['imagen'];
	$pdf->Image('../image/'.$ximagen,15,10,180,100);
	
	//$pdf->Output("");
	
	$pdf->Output("../extractospdf/".$xnombre.".pdf", 'F');

	$archivo_local="../extractospdf/".$xnombre.".pdf";
	$archivo_remoto=$xnombre.".pdf";
	//$archivo_remoto=$_SERVER["DOCUMENT_ROOT"].$xnombre.".pdf";

	//Sube archivo de la maquina Cliente al Servidor (Comando PUT)
	//Obtiene un manejador y se conecta al Servidor FTP 
	$id_ftp=ConectarFTP();

	if (!$id_ftp){
	 die( "No se pudo conectar al servidor FTP!!!!");
	} 

	if (!ftp_put($id_ftp,$archivo_remoto,$archivo_local,FTP_BINARY)) {
	 die("No se pudo pegar el archivo ".$archivo_remoto."\n");
	}

	if (!ftp_chmod($id_ftp, 0777, $archivo_remoto)) {
	 echo "no se pudo realizar chmod sobre $archivo_remoto\n";
	} 

	$termino='S';
	//Sube un archivo al Servidor FTP en modo Binario
	ftp_quit($id_ftp); //Cierra la conexion FTP

	//PAGINA WEB NUEVA
	$archivo_local="../extractospdf/".$xnombre.".pdf";
	$archivo_remoto=$xnombre.".pdf";
	//$archivo_remoto=$xnombre.".pdf";

	//Sube archivo de la maquina Cliente al Servidor (Comando PUT)
	//Obtiene un manejador y se conecta al Servidor FTP 

	switch (substr(trim($xnombre),0,3)) {
		case "ext":
			$id_ftp2 = ConectarFtpWebQuiniela();
		 if (!$id_ftp2){
			die( "No se pudo conectar al servidor web FTP!!!!");
		 }

		if (!ftp_put($id_ftp2,$archivo_remoto,$archivo_local,FTP_BINARY)) {
			 die("No se pudo pegar el archivo ".$archivo_remoto."\n");
		}

		if (!ftp_chmod($id_ftp2, 0777, $archivo_remoto)) {
		 echo "no se pudo realizar chmod sobre $archivo_remoto\n";
		} 

		$termino='S';
		//Sube un archivo al Servidor FTP en modo Binario
		ftp_quit($id_ftp2); //Cierra la conexion FTP

		   break;

		case "LOT":
			$id_ftp2 = ConectarFtpWebLoteria();
		 if (!$id_ftp2){
			die( "No se pudo conectar al servidor web FTP!!!!");
		 }
		if (!ftp_put($id_ftp2,$archivo_remoto,$archivo_local,FTP_BINARY)) {
			die("No se pudo pegar el archivo ".$archivo_remoto."\n");
		}
		if (!ftp_chmod($id_ftp2, 0777, $archivo_remoto)) {
			echo "no se pudo realizar chmod sobre $archivo_remoto\n";
		} 

		$termino='S';
		//Sube un archivo al Servidor FTP en modo Binario
		ftp_quit($id_ftp2); //Cierra la conexion FTP
		break;
	}
}
?>
<link href="../estilo/estilo.css" rel="stylesheet" type="text/css" />

<table width="761" border="0">
  <tr>
    <td ><div align="center"><img src="../image/kanban_loguito.png" width="241" height="52" /></div></td>
    </tr>
    <tr>    
    <td class="texto3Copy"><div align="center">Archivo Guardado con &Eacute;xito</div></td>
       </tr>
        <tr>
    <td><div align="center"><img src="../image/ok.jpg" width="48" height="48" /></div></td>
  </tr>
</table>