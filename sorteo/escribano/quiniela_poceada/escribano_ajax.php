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

$res_rec = sql("SELECT
				    TOTAL_PREMIOS_8_ACIERTOS,
				    TOTAL_PREMIOS_7_ACIERTOS,
				    TOTAL_PREMIOS_6_ACIERTOS,
				    TOTAL_PREMIOS_5_ACIERTOS,
				    (SELECT COUNT(*) FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA WHERE SORTEO=REC.SORTEO AND ID_JUEGO=REC.ID_JUEGO AND ID_DESCRIPCION = 82) AS CANTIDAD_GANADORES_8,
				    (SELECT COUNT(*) FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA WHERE SORTEO=REC.SORTEO AND ID_JUEGO=REC.ID_JUEGO AND ID_DESCRIPCION = 83) AS CANTIDAD_GANADORES_7,
				    (SELECT COUNT(*) FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA WHERE SORTEO=REC.SORTEO AND ID_JUEGO=REC.ID_JUEGO AND ID_DESCRIPCION = 84) AS CANTIDAD_GANADORES_6,
				    (SELECT COUNT(*) FROM KANBAN.T_PREMIOS@KANBAN_ANTICIPADA WHERE SORTEO=REC.SORTEO AND ID_JUEGO=REC.ID_JUEGO AND ID_DESCRIPCION = 85) AS CANTIDAD_GANADORES_5,
				    TS.MONTO_FRACCION
				FROM
				    KANBAN.T_TT_RECAUDACION@KANBAN_ANTICIPADA REC,
                    KANBAN.T_SORTEO@KANBAN_ANTICIPADA TS
				WHERE
				    REC.SORTEO       = ?
				    AND REC.ID_JUEGO = ?
				    AND TS.ID_JUEGO=REC.ID_JUEGO
                    AND TS.SORTEO= REC.SORTEO",
    array($_SESSION['sorteo'], $_SESSION['id_juego']));
$row_rec = siguiente($res_rec);

$pozos['pozos'][] = array('pozo_8_aciertos' => '$' . number_format($row_rec->TOTAL_PREMIOS_8_ACIERTOS, 0, ',', '.'), 'cantidad_ganadores_8_aciertos' => (($row_rec->CANTIDAD_GANADORES_8 == 0) ? 'Pozo Vacante' : ($row_rec->CANTIDAD_GANADORES_8 > 1 ? $row_rec->CANTIDAD_GANADORES_8 . ' Ganadores con $' . number_format(($row_rec->TOTAL_PREMIOS_8_ACIERTOS / $row_rec->CANTIDAD_GANADORES_8), 0, ',', '.') . ' c/u ' : $row_rec->CANTIDAD_GANADORES_8 . ' Ganador con $' . number_format(($row_rec->TOTAL_PREMIOS_8_ACIERTOS / $row_rec->CANTIDAD_GANADORES_8), 0, ',', '.'))));
$pozos['pozos'][] = array('pozo_7_aciertos' => '$' . number_format($row_rec->TOTAL_PREMIOS_7_ACIERTOS, 0, ',', '.'), 'cantidad_ganadores_7_aciertos' => (($row_rec->CANTIDAD_GANADORES_7 == 0) ? 'Pozo Vacante' : ($row_rec->CANTIDAD_GANADORES_7 > 1 ? $row_rec->CANTIDAD_GANADORES_7 . ' Ganadores con $' . number_format(($row_rec->TOTAL_PREMIOS_7_ACIERTOS / $row_rec->CANTIDAD_GANADORES_7), 0, ',', '.') . ' c/u' : $row_rec->CANTIDAD_GANADORES_7 . ' Ganador con $' . number_format(($row_rec->TOTAL_PREMIOS_7_ACIERTOS / $row_rec->CANTIDAD_GANADORES_7), 0, ',', '.'))));
$pozos['pozos'][] = array('pozo_6_aciertos' => '$' . number_format($row_rec->TOTAL_PREMIOS_6_ACIERTOS, 0, ',', '.'), 'cantidad_ganadores_6_aciertos' => (($row_rec->CANTIDAD_GANADORES_6 == 0) ? 'Pozo Vacante' : ($row_rec->CANTIDAD_GANADORES_6 > 1 ? $row_rec->CANTIDAD_GANADORES_6 . ' Ganadores con $' . number_format(($row_rec->TOTAL_PREMIOS_6_ACIERTOS / $row_rec->CANTIDAD_GANADORES_6), 0, ',', '.') . ' c/u ' : $row_rec->CANTIDAD_GANADORES_6 . ' Ganador con $' . number_format(($row_rec->TOTAL_PREMIOS_6_ACIERTOS / $row_rec->CANTIDAD_GANADORES_6), 0, ',', '.'))));
$pozos['pozos'][] = array('pozo_5_aciertos' => '$' . number_format($row_rec->TOTAL_PREMIOS_5_ACIERTOS, 0, ',', '.'), 'cantidad_ganadores_5_aciertos' => (($row_rec->CANTIDAD_GANADORES_5 == 0) ? 'Pozo Vacante' : ($row_rec->CANTIDAD_GANADORES_5 > 1 ? $row_rec->CANTIDAD_GANADORES_5 . ' Ganadores con $' . number_format(($row_rec->TOTAL_PREMIOS_5_ACIERTOS / $row_rec->CANTIDAD_GANADORES_5), 0, ',', '.') . ' c/u ' : $row_rec->CANTIDAD_GANADORES_5 . ' Ganador con $' . number_format(($row_rec->MONTO_FRACCION), 0, ',', '.'))));
$retorno          = array(
    'zonaMostrando' => 'zona' . (int) $row->VALOR,
    'escribano'     => ($row_sorteo->ESCRIBANO == null) ? '' : $row_sorteo->ESCRIBANO,
    'jefe'          => ($row_sorteo->JEFE == null) ? '' : $row_sorteo->JEFE,
    'operador'      => ($row_sorteo->OPERADOR == null) ? '' : $row_sorteo->OPERADOR,
    'juego'         => ($row_sorteo->TIPO_JUEGO == null) ? '' : $row_sorteo->TIPO_JUEGO,
    'sorteo'        => ($row_sorteo->SORTEO == null) ? '' : $row_sorteo->SORTEO,
    'fecha_sorteo'  => ($row_sorteo->FECHA_SORTEO == null) ? '' : $row_sorteo->FECHA_SORTEO,
    'hora_sorteo'   => ($row_sorteo->HORA_SORTEO == null) ? '' : $row_sorteo->HORA_SORTEO,
    'billetesZona1' => array(),
    'pozos'         => $pozos['pozos'],
);
/**
BUSCAMOS LAS EXTRACCIONES DE LA ZONA 1
 */

$res = sql("SELECT
				LPAD(NUMERO, 2, 0) AS NUMERO,
				LPAD(POSICION, 2, 0) AS POSICION,
        		(	SELECT DECODE(COUNT(*),0,'NO','SI')
        			FROM SGS.T_BILLETES_PARTICIPANTES WHERE ID_JUEGO=TE.ID_JUEGO AND SORTEO=TE.SORTEO AND BILLETE=TE.NUMERO) AS VENDIDO,
                (
                CASE
                		WHEN SORTEO_ASOC LIKE 'QUINIELA DUPLICADO%' THEN 1
                		WHEN SORTEO_ASOC LIKE 'VALIDA%' THEN 2
            	ELSE 0
            	END) AS ESTADO

			FROM
				SGS.T_EXTRACCION TE
			WHERE
					ID_JUEGO = ?
				AND SORTEO = ?
				AND ZONA_JUEGO = 1
				AND TE.SORTEO_ASOC NOT LIKE '%COINCIDE%'
			ORDER BY
				ORDEN",
    array($_SESSION['id_juego'], $_SESSION['sorteo']));

$estado = array();
while ($row = siguiente($res)) {
    if ($row->ESTADO == '1') {
        $estado[$row->POSICION] = array('duplicado' => 'SI');
    }
    if ($row->ESTADO == '2') {
        $estados           = &$estado[$row->POSICION];
        $estados['valida'] = 'SI';
    }

}
$res->MoveFirst();
while ($row = siguiente($res)) {
    if ($estado[$row->POSICION]['duplicado'] == 'SI' && $estado[$row->POSICION]['valida'] == 'SI') {
        if ($row->ESTADO == '2') {
            $retorno['billetesZona1'][] = array('numero' => $row->NUMERO, 'posicion' => $row->POSICION, 'vendido' => $row->VENDIDO, 'estado' => $row->ESTADO);
        }
    } else if ($estado[$row->POSICION]['duplicado'] == 'SI') {
        if ($row->ESTADO == '1') {
            $retorno['billetesZona1'][] = array('numero' => $row->NUMERO, 'posicion' => $row->POSICION, 'vendido' => $row->VENDIDO, 'estado' => $row->ESTADO);
        }
    } else {
        $retorno['billetesZona1'][] = array('numero' => $row->NUMERO, 'posicion' => $row->POSICION, 'vendido' => $row->VENDIDO, 'estado' => $row->ESTADO);
    }

}

//var_dump($retorno['billetesZona3']);
//header('Content-Type: text/html; charset=iso-8859-1');
header('Content-Type: text/html; charset=utf-8');
echo json_encode($retorno);
exit;
