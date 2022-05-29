<?php
include_once dirname(__FILE__) . '/../../librerias/alambre/funcion.inc.php';
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

if ($accion == 'modificar') {

    include_once dirname(__FILE__) . '/../../db.php';

    $id_juego           = isset($_POST['id_juego']) ? $_POST['id_juego'] : '';
    $sorteo             = isset($_POST['sorteo']) ? $_POST['sorteo'] : '';
    $semana             = isset($_POST['semana']) ? $_POST['semana'] : '';
    $escribano          = isset($_POST['escribano']) ? $_POST['escribano'] : '';
    $jefe_sorteo        = isset($_POST['jefe']) ? $_POST['jefe'] : '';
    $id                 = isset($_POST['id_sorteo']) ? $_POST['id_sorteo'] : '';
    $fecha_sorteo       = isset($_POST['fecha_sorteo']) ? $_POST['fecha_sorteo'] : '';
    $fecha_prescripcion = isset($_POST['fecha_prescripcion']) ? $_POST['fecha_prescripcion'] : '';

    if ($jefe_sorteo == '-1') {
        $json = array('mensaje' => 'Es necesario ingresar el jefe de Sorteo', 'tipo' => 'error');
    } else if ($escribano == '-1') {
        $json = array('mensaje' => 'Es necesario ingresar el Escribano del Sorteo', 'tipo' => 'error');
    } else if (empty($fecha_sorteo)) {
        $json = array('mensaje' => 'Es necesario ingresar la fecha de Sorteo', 'tipo' => 'error');
    } else {
        try {
            $ok = sql("
			UPDATE sgs.T_ANTICIPADA
            SET id_escribano=?,id_jefe=?,fecha_sorteo=to_date(?,'dd/mm/yyyy'),PRESCRIPCION=to_date(?,'dd/mm/yyyy')
            WHERE ID_JUEGO = ? AND SORTEO = ? AND SEMANA = ?", array($escribano, $jefe_sorteo, $fecha_sorteo, $fecha_prescripcion, $id_juego, $sorteo, $semana));
            if ($ok) {
                $json = array('mensaje' => 'Se modifico correctamente el Incentivo', 'tipo' => 'success');
            }

        } catch (Exception $db) {
            $json = array('mensaje' => urlencode($db->ErrorMsg()), 'tipo' => 'error');
        }
    }
    header('Content-Type: application/json');
    echo json_encode($json);
} else if ($accion == 'nuevo') {
    include_once dirname(__FILE__) . '/../../db.php';
    conectar_db();
    //$db->debug=true;
    $sorteo = isset($_POST['sorteo']) ? $_POST['sorteo'] : '';
    try {

        if ($sorteo == '') {
            header('Content-Type: application/json');
            $json = array('mensaje' => 'Es necesario ingresar el numero de incentivo', 'tipo' => 'error');
            die(json_encode($json));
        }

        $ok = sql("INSERT INTO sgs.T_INCENTIVO_SORTEO(ID_INCENTIVO_SORTEO,ID_JUEGO,FECHA_SORTEO) VALUES(?,?,to_date(?,'dd/mm/yyyy hh24:mi'))", array($sorteo, 1, date('d/m/Y')));
        if ($ok) {
            $json = array('mensaje' => 'Se grabo correctamente el Incentivo', 'tipo' => 'success');
            header('Content-Type: application/json');
            die(json_encode($json));
        }

    } catch (Exception $db) {
        header('Content-Type: application/json');
        $json = array('mensaje' => urlencode($db->ErrorMsg()), 'tipo' => 'error');
        echo json_encode($json);
    }

}
