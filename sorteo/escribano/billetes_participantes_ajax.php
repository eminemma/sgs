<?php
set_time_limit(0);
ini_set('memory_limit', '256M');
@session_start();
include_once dirname(__FILE__) . '/../../db.php';
include_once dirname(__FILE__) . '/../../librerias/alambre/funcion.inc.php';

conectar_db();
$billete_participantes = array();
$res                   = sql("   SELECT BILLETE,FRACCION FROM  (
    				SELECT BILLETE,FRACCION
    				FROM   T_BILLETES_PARTICIPANTES
    				WHERE SORTEO=?
    				AND ID_JUEGO=?
    				ORDER BY DBMS_RANDOM.VALUE
    			)
    			WHERE ROWNUM<=50000", array($_SESSION['sorteo'], $_SESSION['id_juego']));
while ($row = siguiente($res)) {

    $billete_participantes[] = array(
        'billete' => str_pad($row->BILLETE, 5, 0, STR_PAD_LEFT)
        , 'fraccion' => str_pad($row->FRACCION, 2, 0, STR_PAD_LEFT),
    );
}

echo json_encode($billete_participantes);
