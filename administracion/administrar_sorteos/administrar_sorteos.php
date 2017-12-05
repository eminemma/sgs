<?php 
session_start();
switch ($_SESSION['id_juego']) {
	case '1':
		include 'loteria_administrar_sorteos.php';
		break;
	case '2':
		include 'quiniela_administrar_sorteos.php';
		break;
	default:
		die('Es necesario parametrizar el juego actual'.$_SESSION['id_juego']);
		break;
}

?>