<?php
@session_start();
include_once '../../mensajes.php';
require_once '../../db.php';

if ($_GET['accion'] == 'iniciar_sesion') {

    if (conectar_db()) {
        $rs = sql("	SELECT
						C.ID_CASA,
						SUC_BAN,
						B.DESCRIPCION AS AREA,
						C.DESCRIPCION AS SUCURSAL,
						A.DESCRIPCION AS USUARIO,
						TRIM(CLAVE_SECRETA) AS CLAVE_SECRETA
					FROM
						SUPERUSUARIO.USUARIOS A,
						SUPERUSUARIO.AREAS B,
						SUPERUSUARIO.CASAS C
					WHERE
							A.AREA_ID = B.ID_AREA
						AND B.CASA_ID = C.ID_CASA
						AND A.ID_USUARIO = 'DU'||?",
            array($_GET['dni']));
        $row = null;
        if (empty($_GET['dni'])) {
            die(error('Es necesario ingresar un documento'));
        }

        if (strlen($_GET['dni']) != 8) {
            die(error('Es necesario ingresar un documento'));
        }

        if (!$row = siguiente($rs)) {
            die(error('Usuario Inexistente'));
        }

        if ($row->CLAVE_SECRETA != $_GET['password']) {
            die(error('Clave Incorrecta'));
        }

        $_SESSION['dni']            = $_GET['dni'];
        $_SESSION['clave']          = $_GET['password'];
        $_SESSION['suc_ban']        = $row->SUC_BAN;
        $_SESSION['id_area']        = $row->ID_AREA;
        $_SESSION['area']           = $row->AREA;
        $_SESSION['sucursal']       = $row->SUCURSAL;
        $_SESSION['nombre_usuario'] = $row->USUARIO;

        ok('Usuario logueado correctamente');
        echo '<script type="text/javascript">$("#cerrar_sesion, #cambiar_juego_sorteo").show(); $("#iniciar_sesion").hide()</script>';
    } else {
        include 'iniciar_sesion.php';
    }
}
