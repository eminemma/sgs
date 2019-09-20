<?php

@session_start();

include_once dirname(__FILE__) . '/../mensajes.php';

include_once dirname(__FILE__) . '/../db.php';

$reinciar = isset($_GET['reiniciar_entero']) ? (Boolean) $_GET['reiniciar_entero'] : false;

conectar_db();

if (!$reinciar) {

    $stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 1); END;");

    $db->InParameter($stmt, $_SESSION['id_juego'], 'a1');

    $db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

    $ok = $db->Execute($stmt);

    $stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 2); END;");

    $db->InParameter($stmt, $_SESSION['id_juego'], 'a1');

    $db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

    $ok2 = $db->Execute($stmt);

    $stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 3); END;");

    $db->InParameter($stmt, $_SESSION['id_juego'], 'a1');

    $db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

    $ok3 = $db->Execute($stmt);

    $stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 4); END;");

    $db->InParameter($stmt, $_SESSION['id_juego'], 'a1');

    $db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

    $ok3 = $db->Execute($stmt);

    $stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 32); END;");

    $db->InParameter($stmt, $_SESSION['id_juego'], 'a1');

    $db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

    $ok3 = $db->Execute($stmt);

} else {

    $stmt = $db->PrepareSP("BEGIN SGS.PR_REINICIAR_SORTEO(:a1, :a2, 4); END;");

    $db->InParameter($stmt, $_SESSION['id_juego'], 'a1');

    $db->InParameter($stmt, $_SESSION['sorteo'], 'a2');

    $ok3 = $db->Execute($stmt);

}

$rs_usuarios = sql(" SELECT
                                SUBSTR(ID_JEFE,3,LENGTH(ID_JEFE)) AS USUARIO1,
                                SUBSTR(ID_OPERADOR,3,LENGTH(ID_OPERADOR)) AS USUARIO2
                            FROM
                                SGS.T_SORTEO
                            WHERE SORTEO  =?
                             AND  ID_JUEGO=?", array($_SESSION['sorteo'], $_SESSION['id_juego']));
$row_usuarios = siguiente($rs_usuarios);

$ok4 = sql("UPDATE SGS.T_PARAMETRO_COMPARTIDO
						SET VALOR               = 'SI',
							VALOR_SEGUNDO       = 'SI',
							ID_USUARIO 			= ?,
                            ID_USUARIO2          = ?
						WHERE PARAMETRO       	= 'REINICIO'
							AND ID_JUEGO        = ? ", array($row_usuarios->USUARIO1, $row_usuarios->USUARIO2, $_SESSION['id_juego']));

if ($ok && $ok2 && $ok3) {
    ok('Se reinicio el sorteo ' . $_SESSION['sorteo'] . ' ' . $_SESSION['juego']);
} else {
    error('Error en el reinicio del sorteo ' . $_SESSION['sorteo'] . ' ' . $_SESSION['juego']);
}
