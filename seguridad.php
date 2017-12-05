<?php
@session_start();
include_once dirname(__FILE__).'/mensajes.php';

$permitidos = array('/app/otras_provincias/index.php',
					'/app/otras_provincias/sesion/sesion.php',
					'/app/otras_provincias/sesion/ajax.php');

if(!in_array($_SERVER['SCRIPT_NAME'], $permitidos)){
	if((int)$_SESSION['dni'] == 0){
		error('Acceso denegado. Debe iniciar sesión.');
		echo '<script type="text/javascript">$("#cerrar_sesion, #cambiar_juego_sorteo").hide()</script>';
		exit;
	}
}