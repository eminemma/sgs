<?php
@session_start();
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/../../librerias/alambre/funcion.inc.php';

conectar_db();
$db->SetFetchMode(ADODB_FETCH_ASSOC);
$res        = sql("SELECT FRACCIONES FROM sgs.T_SORTEO WHERE ID_JUEGO = ? AND SORTEO= ?", array($_SESSION['id_juego'], $_SESSION['sorteo']));
$row        = siguiente($res);
$fracciones = $row->FRACCIONES;

$res = sql("SELECT VALOR FROM sgs.T_PARAMETRO_COMPARTIDO WHERE ID_JUEGO = ? AND PARAMETRO = 'MOSTRAR_RESUMEN_ANTICIPADA'", array($_SESSION['id_juego']));
$row = siguiente($res);
if ($row->VALOR != null) {

    $res_ganador = sql("SELECT TG.ID_JUEGO,
                              TG.SORTEO,
                              TG.SEMANA,
                              TG.BILLETE,
                              TG.FRACCION,
                              NVL(TG.AGENCIA,'') as AGENCIA,
                              NVL(TG.LOCALIDAD,'CORDOBA')      AS LOCALIDAD,
                              TG.NOMBRE AS NOMBRE,
                              TA.PREMIO,TG.SUCURSAL
                            FROM    SGS.T_ANTICIPADA_GANADORES TG,
                                    SGS.T_ANTICIPADA TA
                            WHERE TG.ID_JUEGO = ?
                            AND TG.SORTEO     = ?
                            AND TG.SEMANA     = ?
                            AND TG.SORTEO     =TA.SORTEO
                            AND TG.SEMANA     =TA.SEMANA
                            AND TG.ORDEN      =TA.ORDEN
                            ORDER BY TG.ORDEN", array($_SESSION['id_juego'], $_SESSION['sorteo'], $row->VALOR));
    $res_premios = sql("SELECT PREMIO
                        FROM  SGS.T_ANTICIPADA
                        WHERE
                                ID_JUEGO=?
                        AND SORTEO=?
                        AND SEMANA=?
                        GROUP BY PREMIO
                        ORDER BY PREMIO", array($_SESSION['id_juego'], $_SESSION['sorteo'], ($row->VALOR)));

    $res = sql("
            SELECT
                TS.ID_JUEGO,  TS.SORTEO,  TS.SEMANA,  TS.PREMIO,  TS.ID_JEFE,  TS.ID_ESCRIBANO,  TS.PRESCRIPCION,  TS.PROX_SORTEO,  TS.PREMIO_PROX_SORTEO
                ,DECODE(TS.ID_JEFE,NULL,'SIN JEFE',JEFE.DESCRIPCION) AS JEFE_SORTEO,DECODE(TS.ID_ESCRIBANO,NULL,'SIN ESCRIBANO',ES.DESCRIPCION) AS ESCRIBANO,TS.SORTEO,ROWNUM RNUM
                 ,(SELECT MAX(SEMANA) FROM SGS.T_ANTICIPADA WHERE ID_JUEGO=? AND SORTEO=?) CANTIDAD_SEMANAS,  to_char(TS.FECHA_SORTEO,'dd/mm/yyyy') as FECHA_SORTEO

            FROM
                SGS.T_ANTICIPADA TS,
                SUPERUSUARIO.USUARIOS JEFE,
                SGS.T_ESCRIBANO ES
            WHERE
                TS.ID_JUEGO     = ?
                AND TS.SORTEO   = ?
                AND TS.SEMANA   = ?
                AND TS.ORDEN    = ?
                AND TS.ID_JEFE      = JEFE.ID_USUARIO(+)
                AND TS.ID_ESCRIBANO = ES.ID_ESCRIBANO(+)", array($_SESSION['id_juego'], $_SESSION['sorteo'], $_SESSION['id_juego'], $_SESSION['sorteo'], $row->VALOR, 1));

    $row_sorteo         = siguiente($res);
    $descIncentivo      = $row_sorteo->PREMIO;
    $prescripcion       = $row_sorteo->PRESCRIPCION;
    $prox_sorteo        = $row_sorteo->PROX_SORTEO;
    $fecha_sorteo       = $row_sorteo->FECHA_SORTEO;
    $premio_prox_sorteo = $row_sorteo->PREMIO_PROX_SORTEO;
    $escribano          = $row_sorteo->ESCRIBANO;
    $jefe_sorteo        = $row_sorteo->JEFE_SORTEO;

    $retorno = array(
        'incentivoMostrando' => 'resumen',
        'semana'             => $row->VALOR,
        'sorteo'             => $row_sorteo->SORTEO,
        'escribano'          => $escribano,
        'jefe_sorteo'        => $jefe_sorteo,
        'prescripcion'       => date('d/m/Y', strtotime(str_replace("/", "-", $prescripcion))),
        'fecha_sorteo'       => $fecha_sorteo,
        'prox_sorteo'        => $prox_sorteo,
        'ganadores'          => $res_ganador->GetArray(),
        'premios'            => $res_premios->GetArray(),
    );
    echo json_encode($retorno);
    exit;
}
//$db->debug= true;

$res   = sql("SELECT VALOR,ORDEN FROM sgs.T_PARAMETRO_COMPARTIDO WHERE ID_JUEGO = ? AND PARAMETRO = 'MOSTRAR_ANTICIPADA'", array($_SESSION['id_juego']));
$row   = siguiente($res);
$valor = $row->VALOR;
$orden = $row->ORDEN;
/*$orden_pos = array(1 => 'PRIMER PREMIO', 2 => 'SEGUNDO PREMIO', 3 => 'TERCER PREMIO', 4 => 'CUARTO PREMIO', 5 => 'QUINTO PREMIO', 6 => 'SEXTO PREMIO', 7 => 'SEPTIMO PREMIO', 8 => 'OCTAVO PREMIO', 9 => 'NOVENO PREMIO');*/
//$db->debug= true;

$res = sql("
SELECT
	TS.ID_JUEGO,  TS.SORTEO,  TS.SEMANA,  TS.PREMIO,  TS.ID_JEFE,  TS.ID_ESCRIBANO,  TS.PRESCRIPCION,  TS.PROX_SORTEO,  TS.PREMIO_PROX_SORTEO
	,DECODE(TS.ID_JEFE,NULL,'SIN JEFE',JEFE.DESCRIPCION) AS JEFE_SORTEO,DECODE(TS.ID_ESCRIBANO,NULL,'SIN ESCRIBANO',ES.DESCRIPCION) AS ESCRIBANO,TS.SORTEO,ROWNUM RNUM
     ,(SELECT MAX(SEMANA) FROM SGS.T_ANTICIPADA WHERE ID_JUEGO=? AND SORTEO=?) CANTIDAD_SEMANAS,  to_char(TS.FECHA_SORTEO,'dd/mm/yyyy') as FECHA_SORTEO

FROM
	SGS.T_ANTICIPADA TS,
	SUPERUSUARIO.USUARIOS JEFE,
	SGS.T_ESCRIBANO ES
WHERE
	TS.ID_JUEGO     = ?
	AND TS.SORTEO   = ?
	AND TS.SEMANA	= ?
    AND TS.ORDEN    = ?
	AND TS.ID_JEFE      = JEFE.ID_USUARIO(+)
	AND TS.ID_ESCRIBANO = ES.ID_ESCRIBANO(+)", array($_SESSION['id_juego'], $_SESSION['sorteo'], $_SESSION['id_juego'], $_SESSION['sorteo'], $valor, $orden));

$row                = siguiente($res);
$descIncentivo      = $row->PREMIO;
$prescripcion       = $row->PRESCRIPCION;
$fecha_sorteo       = $row->FECHA_SORTEO;
$prox_sorteo        = $row->PROX_SORTEO;
$premio_prox_sorteo = $row->PREMIO_PROX_SORTEO;
$escribano          = $row->ESCRIBANO;
$jefe_sorteo        = $row->JEFE_SORTEO;
$retorno            = array(
    'incentivoMostrando' => $valor,
    'orden'              => $orden,
    'sorteo'             => $row->SORTEO,
    'descIncentivo'      => $descIncentivo,
    'maximoAleatorio'    => $maximoAleatorio,
    'prescripcion'       => date('d/m/Y', strtotime(str_replace("/", "-", $prescripcion))),
    'fecha_sorteo'       => $fecha_sorteo,
    'prox_sorteo'        => $prox_sorteo,
    'premio_prox_sorteo' => $premio_prox_sorteo,
    'escribano'          => $escribano,
    'jefe_sorteo'        => $jefe_sorteo,
    'datosIncentivo'     => array(),
    'cantFracciones'     => $fracciones,
);

if ((int) $row->SEMANA == $row->CANTIDAD_SEMANAS) {

    $retorno = array(
        'incentivoMostrando' => $valor,
        'orden'              => $orden,
        'sorteo'             => $row->SORTEO,
        'descIncentivo'      => $descIncentivo,
        'maximoAleatorio'    => $maximoAleatorio,
        'prescripcion'       => date('d/m/Y', strtotime(str_replace("/", "-", $prescripcion))),
        'fecha_sorteo'       => date('d/m/Y', strtotime(str_replace("/", "-", $fecha_sorteo))),
        'escribano'          => $escribano,
        'jefe_sorteo'        => $jefe_sorteo,
        'prox_sorteo'        => '',
        'premio_prox_sorteo' => '',
        'datosIncentivo'     => array(),
        'cantFracciones'     => $fracciones,
    );
}

/**
BUSCAMOS LOS DATOS DEL GANADOR DEL INCENTIVO
 */
$res = sql("SELECT ID_JUEGO, SORTEO,  SEMANA,BILLETE,  FRACCION,  AGENCIA,
nvl(LOCALIDAD,'CORDOBA') as LOCALIDAD,NOMBRE AS NOMBRE,SUCURSAL
FROM SGS.T_ANTICIPADA_GANADORES WHERE ID_JUEGO = ? AND SORTEO = ? AND SEMANA = ? AND ORDEN = ?", array($_SESSION['id_juego'], $_SESSION['sorteo'], $valor, $orden));

while ($row = siguiente($res)) {

    $retorno['datosIncentivo'][] = array('aleatorio' => ''
        , 'id_sucursal' => ''
        , 'desc_sucursal' => $row->SUCURSAL
        , 'id_agencia' => $row->AGENCIA
        , 'desc_agencia' => str_pad($row->AGENCIA, 4, 0, STR_PAD_LEFT)
        , 'importe' => ''

        , 'localidad' => $row->LOCALIDAD
        , 'nombre' => $row->NOMBRE

        , 'billete' => str_pad($row->BILLETE, 5, 0, STR_PAD_LEFT)
        , 'fraccion' => str_pad($row->FRACCION, 2, 0, STR_PAD_LEFT),
    );
}

echo json_encode($retorno);
exit;
