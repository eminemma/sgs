<?php
session_start();
require_once '../db.php';
include_once dirname(__FILE__) . '/../librerias/alambre/funcion.inc.php';
$accion = $_GET['accion'];

switch ($accion) {
    case 'obtener_juego':
        $json = array('id_juego' => $_SESSION['id_juego'], 'tipo_juego' => $_SESSION['juego_tipo']);
        header('Content-Type: application/json');
        echo json_encode($json);
        break;
    case 'listar_tipos_juegos':
        $rs_juegos = sql("		SELECT
        							TP.ID_PROGRAMA,
        							TP.DESCRIPCION,
								    TJT.ID_JUEGO_TIPO,
								    TJT.ID_JUEGO,
								    TJT.CODIGO_TIPO_JUEGO
								FROM
								    SGS.T_PROGRAMA     TP,
								    SGS.T_JUEGO_TIPO   TJT
								WHERE
								    TP.ID_JUEGO = ?
								    AND TP.ESTADO = 'A'
								    AND TP.CODIGO_TIPO_JUEGO = TJT.CODIGO_TIPO_JUEGO
                                     AND ID_JUEGO_TIPO NOT IN(83)
                                ORDER BY FECHA DESC",
            array((int) $_GET['id_juego']));
        //$tipo_juegos = $rs_juegos->GetArray();
        //$juegos  = array();
        while ($row_juego = siguiente($rs_juegos)) {
            $juegos[] = array('DESCRIPCION' => $row_juego->DESCRIPCION, 'ID_PROGRAMA' => $row_juego->ID_PROGRAMA);
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
