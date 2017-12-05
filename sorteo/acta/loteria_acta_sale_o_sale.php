<?php 
session_start();
include_once dirname(__FILE__).'/../../db.php';
//$db->debug=true;
//OBTENGO DATOS DEL SORTEO

$rs_extraccion_segundo=sql("SELECT tg.id_premio_descripcion as PREMIO,COUNT(*) as GANADOR
                    FROM SGS.T_SORTEO TS,
                        SGS.T_PROGRAMA TP,
                        SGS.t_programa_premios tpr,
                        SGS.t_ganadores tg,
                                sgs.t_extraccion te
                    WHERE ts.SORTEO        =?
                    AND TS.ID_JUEGO        =?
                    AND ts.id_programa     = tp.id_programa
                    AND tp.id_programa     = tpr.id_programa
                    AND tpr.id_descripcion =tg.id_premio_descripcion
                    AND ts.sorteo          =tg.sorteo
                    AND ts.id_juego        =tg.id_juego
                    AND upper(tpr.sale_o_sale) ='SI'
                    and te.sorteo=ts.sorteo
                            and te.id_juego=ts.id_juego
                            and te.numero=tg.billete
                            and te.posicion=tg.id_premio_descripcion
                    GROUP BY tg.id_premio_descripcion",array($_SESSION['sorteo'],$_SESSION['id_juego']));

if($rs_extraccion_segundo->RecordCount() == 0){
  die("En este sorteo no hay juego Sortea Hasta Que Sale ");
}

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
              where SO.ID_ESCRIBANO = ESC.ID_ESCRIBANO(+)
              AND U.ID_USUARIO(+) = SO.ID_JEFE
              AND US.ID_USUARIO(+) = SO.ID_OPERADOR
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
  $rs_numeros = sql("   SELECT ID_EXTRACCION,
                      ID_JUEGO,
                      SORTEO,
                      ORDEN,
                      POSICION,
                      NUMERO,
                      FRACCION,
                      ZONA_JUEGO,
                      DECODE(
                      (SELECT COUNT(*) FROM sgs.t_billetes_participantes WHERE SORTEO = te.SORTEO
                      AND ID_JUEGO                                                    = te.ID_JUEGO
                      AND BILLETE                                                     = te.numero
                      ), 0, 'NO VENDIDO', 'VENDIDO') AS COMERCIALIZADO
                FROM sgs.T_EXTRACCION te
                WHERE zona_juego=2
                  and id_juego=?
                  and sorteo=?
                ORDER BY orden",array($_SESSION['id_juego'],$_SESSION['sorteo']));
}catch  (exception $e) { die($db->ErrorMsg());}


 
 
$titulo=strtoupper('ACTA DE  SORTEO DE '.$_SESSION['juego'].' '.$_SESSION['juego_tipo'].' EMISION '.$_SESSION['sorteo']); 

if ($_SESSION['codigo_tipo']==2)
	$desc=$_SESSION['descripcion_sorteo'];
else
  $desc="";


$titulo2=strtoupper(utf8_decode('EXTRACCIONES MODALIDAD SORTEA HASTA QUE SALE ')); 

require("header_listado.php"); 
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


$pdf->SetXY(50,80);
$pdf->SetFont('Times','B',10);
$pdf->Cell(20,8,'BILLETE',1,0,'C');
$pdf->Cell(70,8,'COMERCIALIZADO',1,1,'C');
while($row_numeros=$rs_numeros->FetchNextObject($toupper=true)){
      $pdf->SetX(50);
      $pdf->Cell(20,5,str_pad($row_numeros->NUMERO, 5,0,STR_PAD_LEFT),1,0,'C');
      $pdf->Cell(70,5,$row_numeros->COMERCIALIZADO,1,1,'L');
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
