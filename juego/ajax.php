<?php 
session_start();
require_once '../db.php';
include_once dirname(__FILE__).'/../librerias/alambre/funcion.inc.php';
$accion = $_GET['accion'];

switch ($accion) {
	case 'obtener_juego':
		$json=array('id_juego'=>$_SESSION['id_juego'],'tipo_juego'=>$_SESSION['juego_tipo']);
		header('Content-Type: application/json');
      	echo json_encode($json);
		break;	
	case 'listar_tipos_juegos':
		$rs_juegos = sql("		SELECT 	ID_JUEGO_TIPO,
								  		ID_JUEGO,
								  		CODIGO_TIPO_JUEGO,
								  		DESCRIPCION
								FROM SGS.T_JUEGO_TIPO 
								WHERE ID_JUEGO=?",
						array((int)$_GET['id_juego']));
		//$tipo_juegos = $rs_juegos->GetArray();
		//$juegos  = array();
		while($row_juego = siguiente($rs_juegos)){
			$juegos[] = array('DESCRIPCION'=>$row_juego->DESCRIPCION,'ID_JUEGO_TIPO'=>$row_juego->ID_JUEGO_TIPO);
		}
		/*$tipo_juegos_encode  = array();
		foreach ($tipo_juegos as $indice => $juego) {
			$juego['DESCRIPCION'] = utf8_decode($juego['DESCRIPCION']);
			$tipo_juegos_encode[] = $juego;
		}*/
		header('Content-Type: application/json');
		echo json_encode($juegos);
		break;
	default:
		die('Juego: accion invalida');
		break;
}