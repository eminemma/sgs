<?php
session_start();

include "db.php";

conectar_db();

error_reporting(E_ERROR);

ComenzarTransaccion($db);

$valor = $_POST['opciones'] == 'uno' ? 'N' : 'S';
//$db->debug=true;
try {

    $db->Execute("	UPDATE sgs.T_PARAMETRO_COMPARTIDO
					SET
					    VALOR = ?
					WHERE
					        PARAMETRO = 'CARGADOBLE'", array($valor));

} catch (exception $e) {
    die($db->ErrorMsg());
}

FinalizarTransaccion($db);
//header("location: adm_sorteo_paquete.php");
$json = array('mensaje' => 'Se modifico el parametro correctamente', 'tipo' => 'success');
header('Content-Type: application/json');
die(json_encode($json));
