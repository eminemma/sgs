<?php
@session_start();

include_once dirname(__FILE__).'/../../db.php';
//include_once dirname(__FILE__).'../../db_kanban.php';
include_once dirname(__FILE__).'../../librerias/alambre/funcion.inc.php';

//$db->debug = true;
//$db->debug= true;

//echo "SELECT VALOR FROM sgs.T_PARAMETRO_COMPARTIDO WHERE ID_JUEGO = ".$_SESSION['id_juego']." AND PARAMETRO = 'MOSTRAR_ANTICIPADA'<br>";

$res 	= 	sql("SELECT VALOR FROM sgs.T_PARAMETRO_COMPARTIDO WHERE ID_JUEGO = ? AND PARAMETRO = 'MOSTRAR_ANTICIPADA'",
			array($_SESSION['id_juego']));
$row 	= 	siguiente($res);
$valor	=	$row->VALOR;

//$db->debug= true;

$res = sql("
SELECT  
	TS.ID_JUEGO,  TS.SORTEO,  TS.SEMANA,  TS.PREMIO,  TS.ID_JEFE,  TS.ID_ESCRIBANO,  TS.PRESCRIPCION,  TS.PROX_SORTEO,  TS.PREMIO_PROX_SORTEO
	,DECODE(TS.ID_JEFE,NULL,'SIN JEFE',JEFE.DESCRIPCION) AS JEFE_SORTEO,DECODE(TS.ID_ESCRIBANO,NULL,'SIN ESCRIBANO',ES.DESCRIPCION) AS ESCRIBANO,TS.SORTEO,ROWNUM RNUM 
FROM    
	SGS.T_ANTICIPADA TS,
	SUPERUSUARIO.USUARIOS JEFE,
	SGS.T_ESCRIBANO ES
WHERE   
	TS.ID_JUEGO     = ?
	AND TS.SORTEO   = ?
	AND TS.SEMANA	= ?
	AND TS.ID_JEFE      = JEFE.ID_USUARIO(+)
	AND TS.ID_ESCRIBANO = ES.ID_ESCRIBANO(+)",
array($_SESSION['id_juego'],$_SESSION['sorteo'],$valor));
$row = siguiente($res);

$descIncentivo	=	$row->PREMIO  ;
$prescripcion	=	$row->PRESCRIPCION;
$prox_sorteo	=	$row->PROX_SORTEO;
$premio_prox_sorteo	=	$row->PREMIO_PROX_SORTEO;
$escribano	=	$row->ESCRIBANO;
$jefe_sorteo	=	$row->JEFE_SORTEO;

$retorno = array(
	'incentivoMostrando' 	=> (int)$valor,
	'descIncentivo' 		=> $descIncentivo,
	'maximoAleatorio' 		=> $maximoAleatorio,
	'prescripcion' 			=> date('d/m/Y',strtotime($prescripcion)),
	'prox_sorteo' 			=> $prox_sorteo,
	'premio_prox_sorteo' 	=> $premio_prox_sorteo,
	'escribano' 	=> $escribano,
	'jefe_sorteo' 	=> utf8_decode($jefe_sorteo),
	'datosIncentivo' => array()
);



/**
	BUSCAMOS LOS DATOS DEL GANADOR DEL INCENTIVO
*/
/*
$res = sql("SELECT ID_JUEGO, SORTEO,  SEMANA,BILLETE,  FRACCION,  AGENCIA,  
LOCALIDAD, 
NOMBRE 
FROM SGS.T_ANTICIPADA_GANADORES WHERE ID_JUEGO = ? AND SORTEO = ? AND SEMANA = ?",array($_SESSION['id_juego'],$_SESSION['sorteo'],$valor));
*/

$res = sql("SELECT ID_JUEGO, SORTEO,  SEMANA,BILLETE,  FRACCION,  AGENCIA,  
nvl(LOCALIDAD,'CORDOBA') as LOCALIDAD, 
nvl(NOMBRE,'VENTA MOSTRADOR') AS NOMBRE 
FROM SGS.T_ANTICIPADA_GANADORES WHERE ID_JUEGO = ? AND SORTEO = ? AND SEMANA = ?",array($_SESSION['id_juego'],$_SESSION['sorteo'],$valor));


while($row = siguiente($res)){

	$retorno['datosIncentivo'][] = array('aleatorio' 		=> ''
										,'id_sucursal' 		=> '' 
										,'desc_sucursal' 	=> ''	//$row->DESCRIPCION_SUCURSAL, 
										,'id_agencia' 		=> $row->AGENCIA
										,'desc_agencia' 	=> $row->AGENCIA
										,'importe' 			=> ''
										
										,'localidad' 		=> $row->LOCALIDAD
										,'nombre' 			=> $row->NOMBRE
										
										,'billete' 			=> str_pad($row->BILLETE,5,0,STR_PAD_LEFT)
										,'fraccion' 		=> str_pad($row->FRACCION,2,0,STR_PAD_LEFT)
										);
}

//echo $retorno;
echo json_encode($retorno);
exit;