<?php
@session_start();
include_once '../../mensajes.php';

if($_GET['accion'] == 'cerrar_sesion'){
	@session_destroy();
	echo '<script type="text/javascript">$("#cerrar_sesion, #cambiar_juego_sorteo").hide(); $("#iniciar_sesion").show()</script>';
	ok('Sesión cerrada correctamente');
	include '../iniciar_sesion/iniciar_sesion.php';
}