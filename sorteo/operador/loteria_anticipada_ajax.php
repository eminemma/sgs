<?php
@session_start();
include_once dirname(__FILE__) . '/../../db.php';

include_once dirname(__FILE__) . '/../../librerias/alambre/funcion.inc.php';
/*error_reporting(E_ALL);
ini_set('display_errors',1);*/

$accion  = isset($_POST['accion']) ? $_POST['accion'] : '';
$validar = isset($_POST['validar']) ? $_POST['validar'] : '';
$juego   = isset($_POST['juego']) ? $_POST['juego'] : '';

//$db->debug= true;
//var_dump($_SESSION);

/**
Control de ganadores cargados
 */
if ($accion == 'configuracion' && $juego == 'anticipados') {
    conectar_db();
    //$db->debug=true;
    $sorteo   = $_SESSION['sorteo'];
    $id_juego = $_SESSION['id_juego'];

    $loteria = array();

    $rs_programa = sql(" SELECT ID_JUEGO,
                          SORTEO,
                          SEMANA,
                          PREMIO,
                          ID_JEFE,
                          ID_ESCRIBANO,
                          PRESCRIPCION,
                          PROX_SORTEO,
                          PREMIO_PROX_SORTEO,
                          FECHA_SORTEO,
                          IMPORTE,
                          ORDEN
                        FROM SGS.T_ANTICIPADA
                        WHERE SORTEO=?
                        AND ID_JUEGO=?
                        AND to_date(FECHA_SORTEO) = to_date(sysdate)
                        ORDER BY SEMANA,ORDEN ASC", array($sorteo, $id_juego));

    //AND tpd.descripcion <> 'VIGESIMO CUARTO PREMIO'

    $loteria['premios'] = array();
    while ($row_programa = siguiente($rs_programa)) {
        $loteria['semana']    = strtolower($row_programa->SEMANA);
        $loteria['premios'][] = array('orden' => $row_programa->ORDEN, 'premio' => strtolower($row_programa->PREMIO));
    }
    header('Content-Type: application/json');
    echo json_encode($loteria);
    exit;

}
if ($accion == 'control_ingreso') {

    conectar_db();

/*    $db->debug = true;*/
    //$db_kanban->debug=true;

    $incentivo = isset($_POST['incentivo']) ? $_POST['incentivo'] : '';
    $semana    = isset($_POST['semana']) ? $_POST['semana'] : '';
    $sorteo    = $_SESSION['sorteo'];
    $id_juego  = $_SESSION['id_juego'];
    $orden     = $_POST['orden'];
    $mensaje   = '';

    //Verifico si se ha sorteado el incentivo
    try {

        ComenzarTransaccion($db);
        $rs_sorteado = sql("SELECT COUNT(*) AS CANTIDAD FROM SGS.T_ANTICIPADA_GANADORES WHERE ID_JUEGO = ? AND SORTEO = ? AND SEMANA = ? AND ORDEN = ?", array($id_juego, $sorteo, $semana, $orden));
        FinalizarTransaccion($db);

    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error al buscar si existen ganadores ya sorteados: " . $db->ErrorMsg(), "tipo" => "error");
    }
    $row_sorteado = siguiente($rs_sorteado);

    if ($row_sorteado->CANTIDAD == 0) {

        ComenzarTransaccion($db);
        sql("UPDATE SGS.t_parametro_compartido SET VALOR=? WHERE ID_JUEGO=? AND PARAMETRO='MOSTRAR_ANTICIPADA'", array($semana, $id_juego));
        $pantalla = '';
        FinalizarTransaccion($db);
        $juego = '';

        try {
            ComenzarTransaccion($db);

            /*
            $rs = sql("SELECT COUNT(*) AS EXISTE FROM SGS.T_EXTRACCION WHERE SORTEO=? AND ID_JUEGO= ? AND POSICION = ?",array($sorteo,$id_juego,$posicion));
            $row_ganador= siguiente($rs);
            if($row_ganador->EXISTE > 0){
            die('<div id="error_juego" class="alert alert-error"> <button type="button" class="close" onclick="$(this).parent().remove();">x</button><span><i class="icon-remove"></i></span><span class="contenido_error">Ya se ingreso el billete entero</span></div>');
            }
             */

            $posicion = 1;

            $stmt = $db->PrepareSP("BEGIN SGS.PR_TT_NUEVO_SORTEO_ENTERO_F_A(:a1, :a2, :a3, :a4, :a5, :a6); END;");
            $db->InParameter($stmt, $id_juego, 'a1');
            $db->InParameter($stmt, $sorteo, 'a2');
            $db->InParameter($stmt, $posicion, 'a3');
            $db->InParameter($stmt, $juego, 'a4');
            $db->InParameter($stmt, $semana, 'a5');
            $db->InParameter($stmt, $orden, 'a6');
            $ok = $db->Execute($stmt);
            $db->CommitTrans();

            FinalizarTransaccion($db);

            /*
        $ok = sql(    "INSERT INTO SGS.T_ANTICIPADA_GANADORES (ID_JUEGO,    SORTEO,    SEMANA,    BILLETE,    FRACCION,    AGENCIA,    LOCALIDAD, NOMBRE)
        VALUES  (?,?,?,?,?,?,?,?)"
        ,array($_SESSION['id_juego'], $_SESSION['sorteo'],$semana,$entero,$fraccion,$lagencia,$localidad,$nombre)
        );
        FinalizarTransaccion($db);
         */

        } catch (exception $e) {
            $mensaje = array("mensaje" => "Error al buscar si existen ganadores ya sorteados: " . $db->ErrorMsg(), "tipo" => "error");
        }

        if (!$ok) {
            $mensaje = array("mensaje" => "Error al buscar el aleatorio: " . $db->ErrorMsg(), "tipo" => "error");
        }

    } else {
        $mensaje = array("mensaje" => "El sorteo anticipado ya se ha sorteado", "tipo" => "error");
    }

    header('Content-Type: application/json');
    echo json_encode($mensaje);
}

/**
Control ganadores
 */
if ($accion == 'control_ganador' && $juego == 'anticipada') {

    // solo para sorteo juego raspaguita
    //$id_juego = 3;
    $id_juego = $_SESSION['id_juego'];
    $sorteo   = $_SESSION['sorteo'];

    $i = 0;
    try {
        $rs_incentivo = sql("
		SELECT TA.ID_JUEGO,
			NVL(TAG.SEMANA,0) SORTEADO,
			TA.SEMANA
		FROM SGS.T_ANTICIPADA_GANADORES TAG, SGS.T_ANTICIPADA TA
		WHERE TA.ID_JUEGO = ? AND TA.SORTEO = ?
		AND TA.ID_JUEGO = TAG.ID_JUEGO(+) AND TA.SORTEO = TAG.SORTEO(+) AND TA.SEMANA =TAG.SEMANA(+)", array($id_juego, $sorteo));

        $resultado_2 = array();

        //var_dump($resultado);
        while ($row_incentivo = siguiente($rs_incentivo)) {
            $resultado[$i] = array("idIncentivo" => $row_incentivo->SEMANA, "sorteado" => $row_incentivo->SORTEADO);
            $i += 1;
            $resultado_2[] = array("idIncentivo" => $row_incentivo->SEMANA, "sorteado" => $row_incentivo->SORTEADO);

        }
        //var_dump($resultado);
        //var_dump($resultado_2);

        header('Content-Type: application/json');
        //echo json_encode($resultado);
        echo json_encode($resultado_2);

    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error: " . $db->ErrorMsg(), "tipo" => "error");
    }
}

/**
Habilitar pantallas segun el juego (FUNCIONANDO)
 */
if ($accion == 'mostrar_extracto') {

    $id_juego = $_SESSION['id_juego'];
    $sorteo   = $_SESSION['sorteo'];
    $tipo     = isset($_POST['tipo']) ? $_POST['tipo'] : '';
    $orden    = isset($_POST['orden']) ? $_POST['orden'] : '';
    try {
        conectar_db();

        if ($tipo == 'semana_1') {
            $semana = 1;
        } else if ($tipo == 'semana_2') {
            $semana = 2;
        } else if ($tipo == 'semana_3') {
            $semana = 3;
        } else if ($tipo == 'semana_4') {
            $semana = 4;
        } else if ($tipo == 'semana_5') {
            $semana = 5;
        } else if ($tipo == 'semana_6') {
            $semana = 6;
        } else if ($tipo == 'semana_7') {
            $semana = 7;
        } else if ($tipo == 'semana_8') {
            $semana = 8;
        } else if ($tipo == 'semana_9') {
            $semana = 9;
        } else if ($tipo == 'semana_10') {
            $semana = 10;
        } else if ($tipo == 'semana_11') {
            $semana = 11;
        } else if ($tipo == 'semana_12') {
            $semana = 12;
        }

        ComenzarTransaccion($db);
        sql("UPDATE SGS.t_parametro_compartido SET VALOR=?, ORDEN=? WHERE ID_JUEGO=? AND PARAMETRO='MOSTRAR_ANTICIPADA'",
            array($semana, $orden, $id_juego));

        sql("UPDATE SGS.t_parametro_compartido SET VALOR=? WHERE ID_JUEGO=? AND PARAMETRO='MOSTRAR_RESUMEN_ANTICIPADA'", array(null, $id_juego));
        FinalizarTransaccion($db);
        $juego = '';

        $rs = sql('SELECT
                  PREMIO
                FROM SGS.T_ANTICIPADA
                WHERE ID_JUEGO=?
                AND SORTEO=?
                AND SEMANA=?
                AND ORDEN=?', array($_SESSION['id_juego'], $_SESSION['sorteo'], $semana, $orden));
        $row   = siguiente($rs);
        $juego = $row->PREMIO;

        $mensaje = array("mensaje" => "Se va a mostrar el sorteo anticipado " . $juego, "tipo" => "info");

    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error: " . $db->ErrorMsg(), "tipo" => "error");
    }

    header('Content-Type: application/json');
    echo json_encode($mensaje);
}

/**
Eliminar extraccion sorteo
 */
if ($accion == 'eliminar') {

    $semana   = isset($_POST['semana']) ? $_POST['semana'] : '';
    $sorteo   = isset($_POST['sorteo']) ? $_POST['sorteo'] : '';
    $id_juego = isset($_POST['id_juego']) ? $_POST['id_juego'] : '';
    $orden    = isset($_POST['orden']) ? $_POST['orden'] : '';

    conectar_db();
    ComenzarTransaccion($db);
    try {
        sql('DELETE FROM SGS.T_ANTICIPADA_GANADORES WHERE  SEMANA = ? AND ID_JUEGO=? AND SORTEO=? AND ORDEN = ?', array($semana, $id_juego, $sorteo, $orden));
        $mensaje = array("mensaje" => "Se Elimino el sorteo anticipado de la semana " . $semana . ", premio:" . $orden, "tipo" => "error");
    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error al eliminar: " . $db->ErrorMsg(), "tipo" => "error");
    }
    FinalizarTransaccion($db);
    header('Content-Type: application/json');
    echo json_encode($mensaje);
}

if ($accion == 'mostrar_resumen') {

    // solo para sorteo juego raspaguita
    //$id_juego = 3;
    $id_juego = $_SESSION['id_juego'];
    $sorteo   = $_SESSION['sorteo'];
    $semana   = $_POST['semana'];

    $i = 0;
    try {
        sql("UPDATE SGS.t_parametro_compartido SET VALOR=? WHERE ID_JUEGO=? AND PARAMETRO='MOSTRAR_RESUMEN_ANTICIPADA'", array($semana, $id_juego));

        $mensaje = array("mensaje" => "Se va a mostrar el resumen anticipado " . $juego, "tipo" => "info");
    } catch (exception $e) {
        $mensaje = array("mensaje" => "Error: " . $db->ErrorMsg(), "tipo" => "error");
    }
    header('Content-Type: application/json');
    echo json_encode($mensaje);
}
