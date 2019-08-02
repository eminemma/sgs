<?php

@session_start();

include_once dirname(__FILE__).'/../mensajes.php';

include_once dirname(__FILE__).'/../db.php';

$reinciar = isset($_GET['reiniciar_entero']) ? (Boolean)$_GET['reiniciar_entero'] : false;

conectar_db();





if(!$reinciar){

	$stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 1); END;");

	$db->InParameter($stmt,$_SESSION['id_juego'], 'a1');

	$db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

	$ok = $db->Execute($stmt);



	$stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 2); END;");

	$db->InParameter($stmt,$_SESSION['id_juego'], 'a1');

	$db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

	$ok2 = $db->Execute($stmt);





	$stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 3); END;");

	$db->InParameter($stmt,$_SESSION['id_juego'], 'a1');

	$db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

	$ok3 = $db->Execute($stmt);





	$stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 4); END;");

	$db->InParameter($stmt,$_SESSION['id_juego'], 'a1');

	$db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

	$ok3 = $db->Execute($stmt);



	$stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 32); END;");

	$db->InParameter($stmt,$_SESSION['id_juego'], 'a1');

	$db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

	$ok3 = $db->Execute($stmt);
	

} else {

	$stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 4); END;");

	$db->InParameter($stmt,$_SESSION['id_juego'], 'a1');

	$db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

	$ok3 = $db->Execute($stmt);

}

$ok4 = $db->Execute("UPDATE SGS.T_PARAMETRO_COMPARTIDO

						SET VALOR               = 'SI',ID_USUARIO = ?

						WHERE PARAMETRO       	= 'REINICIO'

							AND ID_JUEGO        = ? ",array($_SESSION['dni'],$_SESSION['id_juego']));



if($ok && $ok2 && $ok3)

	ok('Se reinicio el sorteo '.$_SESSION['sorteo'].' '.$_SESSION['juego']);

else

	error('Error en el reinicio del sorteo '.$_SESSION['sorteo'].' '.$_SESSION['juego']);



?>