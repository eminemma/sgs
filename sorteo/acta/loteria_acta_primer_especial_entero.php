<?php 
session_start();
include_once dirname(__FILE__).'/../../db.php';
$_SESSION['codigo_tipo']=2;
$_SESSION['descripcion_sorteo']='EXTRAORDINARIO';
//$db->debug=true;
//OBTENGO DATOS DEL SORTEO
try{
	$rs_sorteo=sql("SELECT TO_CHAR(SO.FECHA_SORTEO, 'DD/MM/YYYY') AS FECHA_SORTEO,
              TO_CHAR(SO.FECHA_HASTA_PAGO_PREMIO, 'DD/MM/YYYY') AS FECHA_CADUCIDAD,       
              U.DESCRIPCION JEFE_SORTEO,       
              SO.DESCRIPCION AS USUARIO,
              US.DESCRIPCION OPERADOR,
              ESC.DESCRIPCION     AS ESCRIBANO
              from sgs.T_SORTEO       SO,
              sgs.T_ESCRIBANO    ESC,
              SUPERUSUARIO.USUARIOS U,
              SUPERUSUARIO.USUARIOS US
              where SO.ID_ESCRIBANO = ESC.ID_ESCRIBANO
              AND U.ID_USUARIO = SO.ID_JEFE
              AND US.ID_USUARIO = SO.ID_OPERADOR
              AND SO.SORTEO = ?
              AND SO.ID_JUEGO = ?",array($_SESSION['sorteo'],$_SESSION['id_juego']));
}catch(exception $e){	die($db->ErrorMsg()); }
$row_sor=$rs_sorteo->FetchNextObject($toupper=true);
$fechasorteo=$row_sor->FECHA_SORTEO;
$fechacaduca=$row_sor->FECHA_CADUCIDAD;
$jefesorteo=utf8_decode($row_sor->JEFE_SORTEO);
$operador=utf8_decode($row_sor->OPERADOR);
$escribano=utf8_decode($row_sor->ESCRIBANO);



try   {
  $rs_extracciones = sql("   SELECT decode(tpp.tipo_premio,'ESPECIE',tde.descripcion_especia,'EFECTIVO',tpp.premio_efectivo)
                                    as descripcion_especia,tpd.descripcion,te.posicion,te.numero,te.fraccion,DECODE(
                                                              (SELECT COUNT(*) FROM sgs.t_billetes_participantes WHERE SORTEO = te.SORTEO
                                                              AND ID_JUEGO                                                    = te.ID_JUEGO
                                                              AND BILLETE                                                     = te.numero
                                                              and fraccion                                                    =te.fraccion
                                                              ), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO,to_char(FECHA_EXTRACCION,'dd/mm/yyyy hh24:mi:ss') as FECHA_EXTRACCION
                FROM SGS.T_EXTRACCION te,SGS.t_programa_premios tpp,SGS.t_premio_descripcion tpd,SGS.t_descripcion_especias tde,SGS.T_SORTEO S
                WHERE te.SORTEO=?
                AND te.ID_JUEGO=?
                AND te.ZONA_JUEGO=4
                and tpp.id_descripcion=tpd.id_premio_desc
                and te.posicion=tpp.id_descripcion
                 and tpp.id_descripcion=tpd.id_premio_desc
                 AND tde.id_descripcion_especia=tpp.premio_id_especias
                 AND S.ID_JUEGO=TE.ID_JUEGO
                 AND S.SORTEO=TE.SORTEO
                 AND S.ID_PROGRAMA=TPP.ID_PROGRAMA                 
        ORDER BY te.zona_juego desc ,te.ORDEN DESC",array($_SESSION['sorteo'], $_SESSION['id_juego']));
}catch  (exception $e) { die($db->ErrorMsg());}


 
 
$titulo=strtoupper('ACTA PREMIOS EXTRAORDINARIOS '.$_SESSION['juego'].' EMISION '.$_SESSION['sorteo']); 
$titulo2="POR COMPRA DE BILLETE ENTERO";
if ($_SESSION['codigo_tipo']==2)
	$desc=$_SESSION['descripcion_sorteo'];
else
  $desc="";


//$titulo2=strtoupper(utf8_decode('MODALIDAD SORTEA HASTA QUE SALE')); 

require("header_listado_c.php"); 
//require(dirname(__FILE__).'/../../librerias/pdf/fpdf.php');


$pdf=new PDF('P');
$pdf->AliasNbPages();
$pdf->AddPage();

$pdf->SetFont('Arial','I',11);
$pdf->SetXY(120,48);
$pdf->Cell(30,8,'Fecha:',0,0,'R');
$pdf->SetFont('Arial','BI',11);
$pdf->SetXY(150,48);
$pdf->Cell(30,8,$fechasorteo,1,0,'C');
$pdf->SetFont('Arial','I',11);
$pdf->SetXY(120,56);
$pdf->Cell(30,8,'Hora:',0,0,'R');
$pdf->SetFont('Arial','BI',11);
$pdf->SetXY(150,56);
$pdf->Cell(30,8,'..............',1,0,'C');
$pdf->SetFont('Arial','I',11);
$pdf->SetXY(120,64);
$pdf->Cell(30,8,'Caducidad:',0,0,'R');
$pdf->SetFont('Arial','BI',11);
$pdf->SetXY(150,64);
$pdf->Cell(30,8,$fechacaduca,1,1,'C');
$pdf->ln(5);

$pdf->SetFont('Times','B',20);
$pdf->Cell(5,5,'',0,0,'C');
$pdf->Cell(150,10,'PREMIO',1,0,'C');
// $pdf->Cell(70,5,'POSICION',1,0,'C');
$pdf->Cell(40,10,'BILLETE',1,1,'C');
// $pdf->Cell(20,5,'FRACCION',1,0,'C');
// $pdf->Cell(35,5,'FECHA',1,1,'C');
//$pdf->Cell(40,5,'COMERCIALIZADO',1,1,'C');
while($row_extraccion=$rs_extracciones->FetchNextObject($toupper=true)){
      $pdf->SetFont('Times','B',18);
      $pdf->Cell(5,5,'',0,0,'C');
      $pdf->Cell(150,10,$row_extraccion->DESCRIPCION_ESPECIA,'B',0,'C');
      // $pdf->Cell(70,5,$row_extraccion->DESCRIPCION,'B',0,'L');
      $pdf->Cell(40,10,str_pad($row_extraccion->NUMERO, 5,0,STR_PAD_LEFT),'B',1,'C');
      // $pdf->Cell(20,5,str_pad($row_extraccion->FRACCION, 2,0,STR_PAD_LEFT),'B',0,'C');
      // $pdf->Cell(35,5,$row_extraccion->FECHA_EXTRACCION,'B',1,'C');
      //$pdf->Cell(40,5,$row_extraccion->COMERCIALIZADO,'B',1,'C');
}

//hora
$pdf->SetFont('Arial','B',10);
$pdf->SetXY(87,250);
$pdf->Cell(20,5,'Hora de Finalizacion:............',0,0,'L');
 
$pdf->SetFont('Times','B',9);
$pdf->SetXY(25,264);
$pdf->Cell(150,5,'___________________                                               ___________________                                      _________________________',0,1,'J');
$pdf->SetXY(25,268);
$pdf->Cell(150,5,'          Operador                                                                  Jefe de Sorteos                                               Firma Escribano Actuante',0,0,'J');
//$pdf->Cell(150,5,'  Firma Responsable                                                     Firma Responsable                                          Firma Escribano Actuante',0,0,'J');
$pdf->SetXY(28,271);
$pdf->Cell(25,5,$operador,0,0,'C');
 
$pdf->SetXY(96,271);
$pdf->Cell(25,5,$jefesorteo,0,0,'C');

$pdf->SetXY(162,271);
$pdf->Cell(25,5,$escribano,0,0,'C');
  
$pdf->Output();
?>
