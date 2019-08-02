<?php
@session_start();
include_once dirname(__FILE__) . '/../../../db.php';
include_once dirname(__FILE__) . '/../../../librerias/alambre/funcion.inc.php';

$res = sql("SELECT 	TE.DESCRIPCION AS ESCRIBANO,
				  	TOP.DESCRIPCION     AS OPERADOR,
				  	TJ.DESCRIPCION      AS JEFE,
				  	TJT.DESCRIPCION AS TIPO_JUEGO,
				  	TO_CHAR(TS.FECHA_SORTEO,'dd/mm/YYYY') as FECHA_SORTEO,
				  	TO_CHAR(TS.FECHA_SORTEO,'HH24:MI') AS HORA_SORTEO,
				  	TS.SORTEO
			FROM 	SGS.T_SORTEO TS,
				  	SGS.T_ESCRIBANO TE,
				  	SUPERUSUARIO.USUARIOS TOP,
				  	SUPERUSUARIO.USUARIOS TJ,
				  	SGS.T_JUEGO_TIPO TJT
			WHERE TS.ID_ESCRIBANO 	  = TE.ID_ESCRIBANO
				AND TS.ID_OPERADOR    = TOP.ID_USUARIO
				AND TS.ID_JEFE        = TJ.ID_USUARIO
				AND TS.ID_TIPO_JUEGO  = TJT.ID_JUEGO_TIPO
				AND TS.SORTEO         = ?",
    array($_SESSION['sorteo']));

$row_sorteo = siguiente($res);

$res = sql("SELECT
				VALOR
			FROM
				sgs.T_PARAMETRO_COMPARTIDO
			WHERE
				ID_JUEGO = ?
				AND PARAMETRO = 'ZONA_MOSTRANDO'",
    array($_SESSION['id_juego']));

$row = siguiente($res);

$retorno = array(
    'zonaMostrando' => 'zona' . (int) $row->VALOR,
    'escribano'     => ($row_sorteo->ESCRIBANO == null) ? '' : $row_sorteo->ESCRIBANO,
    'jefe'          => ($row_sorteo->JEFE == null) ? '' : $row_sorteo->JEFE,
    'operador'      => ($row_sorteo->OPERADOR == null) ? '' : $row_sorteo->OPERADOR,
    'juego'         => ($row_sorteo->TIPO_JUEGO == null) ? '' : $row_sorteo->TIPO_JUEGO,
    'sorteo'        => ($row_sorteo->SORTEO == null) ? '' : $row_sorteo->SORTEO,
    'fecha_sorteo'  => ($row_sorteo->FECHA_SORTEO == null) ? '' : $row_sorteo->FECHA_SORTEO,
    'hora_sorteo'   => ($row_sorteo->HORA_SORTEO == null) ? '' : $row_sorteo->HORA_SORTEO,
    'billetesZona1' => array(),
    'billetesZona2' => array(),
    'billetesZona3' => array(),
);

/**
BUSCAMOS LAS EXTRACCIONES DE LA ZONA 1
 */

$res = sql("SELECT
				LPAD(NUMERO, 2, 0) AS NUMERO,
				LPAD(POSICION, 2, 0) AS POSICION,
        		(	SELECT decode(COUNT(*),0,'NO','SI')
        			FROM sgs.T_BILLETES_PARTICIPANTES WHERE ID_JUEGO=te.id_juego and SORTEO=te.sorteo and billete=te.numero) AS VENDIDO

			FROM
				sgs.T_EXTRACCION te
			WHERE
					ID_JUEGO = ?
				AND SORTEO = ?
				AND ZONA_JUEGO = 1
				AND TE.SORTEO_ASOC NOT LIKE '%COINCIDE%' 
			ORDER BY
				ORDEN",
    array($_SESSION['id_juego'], $_SESSION['sorteo']));

while ($row = siguiente($res)) {
    $retorno['billetesZona1'][] = array('numero' => $row->NUMERO, 'posicion' => $row->POSICION, 'vendido' => $row->VENDIDO);
}




//var_dump($retorno['billetesZona3']);
//header('Content-Type: text/html; charset=iso-8859-1');
header('Content-Type: text/html; charset=utf-8');
echo json_encode($retorno);
exit;