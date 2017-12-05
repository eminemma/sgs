<?php
	session_start();
	include_once '../../mensajes.php';

	if((int)$_SESSION['dni'] > 0){
		info('Seleccione la opción del menú que desee');
		error('Si desea cerrar sesión, haga click en el boton de la esquina superior derecha "Cerrar Sesión"');
		exit;
	}else{
		info('Para evitar problemas de compatibilidad, recomendamos utilizar los siguientes navegadores: Mozilla Firefox, o Google Chrome');
	}
?>

<br>

<form class="form-signin">
	<h2 class="form-signin-heading">Inicio de Sesión</h2>
	<input id="dni" type="text" class="input-block-level" placeholder="Numero de documento">
	<input id="password" type="password" class="input-block-level" placeholder="Contraseña">
	<button class="btn btn-large btn-primary" type="submit" onclick="g('sesion/iniciar_sesion/iniciar_sesion_ajax.php?'+a('iniciar_sesion')+v('dni')+v('password')); return false;">Ingresar</button>
</form>