<?php
@session_start();
include_once dirname(__FILE__).'/../mensajes.php';
include_once dirname(__FILE__).'/../db.php';
conectar_db();
$stmt = $db->PrepareSP("BEGIN SGS.PR_ACTUALIZAR_DESDE_HASTA_XY(:a1, :a2); END;");
$db->InParameter($stmt,$_SESSION['id_juego'], 'a1');
$db->InParameter($stmt, $_SESSION['sorteo'], 'a2');
$ok = $db->Execute($stmt);

if($ok)
	ok('Se mezclaron con éxito los rangos del Incentivo del sorteo '.$_SESSION['sorteo'].' de Loteria');
else
	error('Error al mezclar Incentivo del sorteo '.$_SESSION['sorteo'].' de Loteria');

?>