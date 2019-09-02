<?php
include_once dirname(__FILE__) . '/../../librerias/alambre/funcion.inc.php';
include_once dirname(__FILE__) . '/../../db.php';
$accion = isset($_POST['accion']) ? $_POST['accion'] : '';

if ($accion == 'modificar') {
    $operador      = isset($_POST['operador']) ? $_POST['operador'] : '';
    $escribano     = isset($_POST['escribano']) ? $_POST['escribano'] : '';
    $jefe_sorteo   = isset($_POST['jefe']) ? $_POST['jefe'] : '';
    $id            = isset($_POST['id_sorteo']) ? $_POST['id_sorteo'] : '';
    $fecha_sorteo  = isset($_POST['fecha_sorteo']) ? $_POST['fecha_sorteo'] : '';
    $id_tipo_juego = isset($_POST['id_tipo_juego']) ? $_POST['id_tipo_juego'] : '';
    $quiniela_asoc = isset($_POST['quiniela_asoc']) ? $_POST['quiniela_asoc'] : '';
    $programa      = isset($_POST['programa']) ? $_POST['programa'] : '';

    if ($jefe_sorteo == '-1') {
        $json = array('mensaje' => 'Es necesario ingresar el jefe de Sorteo', 'tipo' => 'error');
    } else if ($operador == '-1') {
        $json = array('mensaje' => 'Es necesario ingresar el Operador del Sorteo', 'tipo' => 'error');
    } else if ($escribano == '-1') {
        $json = array('mensaje' => 'Es necesario ingresar el Escribano del Sorteo', 'tipo' => 'error');
    } else if (empty($fecha_sorteo)) {
        $json = array('mensaje' => 'Es necesario ingresar la fecha de Sorteo', 'tipo' => 'error');
    } else {
        try {

            $ok = sql("UPDATE sgs.T_SORTEO
              SET id_escribano=?,id_operador=?,id_jefe=?,fecha_sorteo=to_date(?,'dd/mm/yyyy hh24:mi:ss'),id_tipo_juego=?,quiniela_asoc=?,id_programa=?
             WHERE ID_SORTEO             = ?", array($escribano, $operador, $jefe_sorteo, $fecha_sorteo, $id_tipo_juego, $quiniela_asoc, $programa, $id));

            if ($ok) {
                $json = array('mensaje' => 'Se modifico correctamente el Sorteo', 'tipo' => 'success');
            }

        } catch (Exception $db) {
            $json = array('mensaje' => urlencode($db->ErrorMsg()), 'tipo' => 'error');
        }

    }
    header('Content-Type: application/json');
    echo json_encode($json);
} else if ($accion == 'nuevo') {

    conectar_db();
    //$db->debug=true;
    $sorteo          = isset($_POST['sorteo']) ? $_POST['sorteo'] : '';
    $primer_elemento = 0;
    if ($_SESSION['id_juego'] == 1) {
        $ultimo_elemento = 99999;
    } else if ($_SESSION['id_juego'] == 2) {
        $ultimo_elemento = 9999;
    }

    try {
        $rs = sql(" SELECT SORTEO
                  FROM sgs.T_SORTEO
                  WHERE SORTEO = ?", array($sorteo));

        if ($rs->RecordCount() > 0) {
            header('Content-Type: application/json');
            $json = array('mensaje' => 'Este sorteo ya existe', 'tipo' => 'error');
            echo json_encode($json);
            exit();
        }
        if ($sorteo == '') {
            header('Content-Type: application/json');
            $json = array('mensaje' => 'Es necesario ingresar el numero de sorteo', 'tipo' => 'error');
            die(json_encode($json));
        }

        $ok = sql("INSERT INTO
                      sgs.T_SORTEO(SORTEO,ID_JUEGO,FECHA_SORTEO,ID_PROGRAMA,PRIMER_ELEMENTO,ULTIMO_ELEMENTO,MONTO_FRACCION,ID_TIPO_JUEGO,FRACCIONES)
              VALUES(?,?,to_date(?,'dd/mm/yyyy hh24:mi'),?,?,?,?,?,?)",
            array($sorteo, $_SESSION['id_juego'], date('d/m/Y'), 1, 0, $ultimo_elemento, 90, $_POST['id_tipo_juego'], 0));

        if ($ok) {
            $json = array('mensaje' => 'Se grabo correctamente el Sorteo', 'tipo' => 'success');
            header('Content-Type: application/json');
            die(json_encode($json));
        }
    } catch (exception $e) {
        $error = oci_error();
        header('Content-Type: application/json');
        $mensaje = $db->ErrorMsg();
        $json    = array('mensaje' => urlencode($mensaje), 'tipo' => 'error');
        echo json_encode($json);
    }

} else if ($accion == 'modificar_anticipada') {
    try {
        conectar_db();
        // var_dump($_POST);
        // $db->debug = true;
        $ok = sql(" UPDATE SGS.T_ANTICIPADA SET ID_JEFE =?,ID_ESCRIBANO=? ,FECHA_SORTEO = to_date(?,'dd/mm/yyyy')
              WHERE ID_JUEGO         = ?
                AND SORTEO           = ?
                AND SEMANA           = ?",
            array($_POST['jefe'], $_POST['escribano'], $_POST['fecha'], $_SESSION['id_juego'], $_SESSION['sorteo'], $_POST['semana']));

        if ($ok) {
            $json = array('mensaje' => 'Se grabo correctamente', 'tipo' => 'success');
            header('Content-Type: application/json');
            die(json_encode($json));
        }
    } catch (exception $e) {
        $error = oci_error();
        header('Content-Type: application/json');
        $mensaje = $db->ErrorMsg();
        $json    = array('mensaje' => urlencode($mensaje), 'tipo' => 'error');
        echo json_encode($json);
    }
}

function sql_sorteo()
{
    $sql = "SELECT  TS.DESCRIPCION,
                  TO_CHAR(TS.FECHA_SORTEO,'DD/MM/YYYY HH24:MI:SS') AS FECHA_SORTEO,
                  TO_CHAR(TS.FECHA_SORTEO,'DD') AS DIA,
                  TS.FECHA_HASTA_PAGO_PREMIO,
                  TS.ID_JEFE,
                  TS.ID_OPERADOR,
                  TS.ID_ESCRIBANO,
                  TS.SORTEO,
                  TS.ID_SORTEO,
                  TS.ID_TIPO_JUEGO,
                  TS.QUINIELA_ASOC,
                  TS.ID_PROGRAMA
          FROM SGS.T_SORTEO TS
          WHERE TS.ID_SORTEO = ?";

    return $sql;
}

function sql_quiniela_asociadas()
{
    $sql = "SELECT
                    ID_SORTEO,
                    SORTEO
            FROM
                    SGS.T_SORTEO
            WHERE  ID_JUEGO                = 2
                AND  SORTEO=?
            ORDER BY FECHA_SORTEO DESC";

    return $sql;
}

function sql_operador()
{
    $sql = "SELECT ID_USUARIO ,DESCRIPCION FROM SUPERUSUARIO.USUARIOS
            WHERE AREA_ID=135
            AND FECHA_BAJA IS NULL
            ORDER BY DESCRIPCION";

    return $sql;
}

function sql_escribano()
{
    $sql = "SELECT ID_ESCRIBANO, DESCRIPCION FROM SGS.T_ESCRIBANO
            WHERE FECHA_BAJA IS NULL
            ORDER BY DESCRIPCION";

    return $sql;
}

function sql_administracion_sorteo()
{
    $sql = " SELECT  TS.DESCRIPCION,
                    TO_CHAR(TS.FECHA_SORTEO,'DD/MM/YYYY') AS FECHA_SORTEO,
                    TO_CHAR(TS.FECHA_HASTA_PAGO_PREMIO,'DD/MM/YYYY') AS FECHA_HASTA_PAGO_PREMIO,
                    DECODE(TS.ID_JEFE,NULL,'SIN JEFE',JEFE.DESCRIPCION) AS JEFE_SORTEO,
                    DECODE(TS.ID_OPERADOR,NULL,'SIN OPERADOR',OPERADOR.DESCRIPCION) AS OPERADOR,
                    DECODE(TS.ID_ESCRIBANO,NULL,'SIN ESCRIBANO',ES.DESCRIPCION) AS ESCRIBANO,
                    TS.SORTEO,TS.ID_SORTEO,ROWNUM RNUM
            FROM    SGS.T_SORTEO TS,
                    SUPERUSUARIO.USUARIOS JEFE,
                    SUPERUSUARIO.USUARIOS OPERADOR,
                    SGS.T_ESCRIBANO ES
            WHERE   TS.SORTEO     = ?
              AND TS.ID_JUEGO     = ?
              AND TS.ID_JEFE      = JEFE.ID_USUARIO(+)
              AND TS.ID_OPERADOR  = OPERADOR.ID_USUARIO(+)
              AND TS.ID_ESCRIBANO = ES.ID_ESCRIBANO(+)";

    return $sql;
}

function sql_anticipados_sorteo()
{
    $sql = " SELECT  ID_JUEGO,
                    SORTEO,
                    SEMANA,
                    ORDEN,
                    PREMIO,
                    ID_JEFE,
                    ID_ESCRIBANO,
                    PRESCRIPCION,
                    PROX_SORTEO,
                    PREMIO_PROX_SORTEO,
                    to_char(FECHA_SORTEO,'dd/mm/yyyy') as FECHA_SORTEO,
                    IMPORTE,ROWNUM RNUM
            FROM SGS.T_ANTICIPADA
            WHERE SORTEO    = ?
              AND ID_JUEGO  = ?
            ORDER BY SEMANA, ORDEN ASC";

    return $sql;
}

function sql_programa()
{
    $sql = " SELECT ID_PROGRAMA,
                    FECHA,
                    ID_JUEGO,
                    CODIGO_TIPO_JUEGO,
                    ESTADO,
                    FECHA_BAJA,
                    USUARIO_BAJA,
                    DESCRIPCION,
                    PRIMER_ELEMENTO,
                    ULTIMO_ELEMENTO,
                    CANTIDAD_NUMEROS
              FROM SGS.T_PROGRAMA
              WHERE ID_JUEGO  = ?
                AND ESTADO    ='A'
              ORDER BY ID_PROGRAMA";

    return $sql;
}
