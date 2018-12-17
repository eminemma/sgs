<?php
session_start();
include_once '../../db.php';

try {
    $rs_sorteo = sql("  SELECT
                                                TO_CHAR(FECHA_SORTEO,'dd/mm/yyyy') AS FECHA_SORTEO
                                            FROM
                                                sgs.T_SORTEO
                                            WHERE
                                                sorteo = ?",
        array($_SESSION['sorteo']));
} catch (exception $e) {
    die($db->ErrorMsg());
}
$row_sorteo   = $rs_sorteo->FetchNextObject($toupper = true);
$fecha_sorteo = $row_sorteo->FECHA_SORTEO;
?>
<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sorteador Loteria de Cordoba S.E.</title>
	  <link href="escribano_estilo.php" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../librerias/jquery/jquery-1.10.1.js"></script>
	<script type="text/javascript" src="../../js/funciones.js"></script>
</head>
<body>

	<div id="escribano_resumen">
	<!-- 	<div id="sorteo_resumen"><?php echo $_SESSION['sorteo']; ?></div>
        <div id="fecha_sorteo_resumen"><?php echo $fecha_sorteo; ?></div> -->
        <div id="primer_premio_resumen"></div>
        <div id="contenido"></div>
	</div>
	<script type="text/javascript">
		//var buscar_informacion_sorteo;

		$(document).ready(
			function(){
				buscar_informacion();
				buscar_informacion_sorteo = setInterval(buscar_informacion, 1300);
			}
		);
		function buscar_informacion(){
			$.getJSON(
				'../escribano/escribano_ajax.php?rand='+parseInt(Math.random() * 1000000000),
				function(data){
					animarSorteo(data);
					//clearInterval(buscar_informacion_sorteo);
				}
			);
		}

		function animarSorteo(data){
			$("#contenido").empty();
			$("#primer_premio_resumen").empty();
			$('#primer_premio_resumen').css('background-image', 'url()');
			for(var i=0;i<data.billetesZona1.length;i++){

				$('#contenido').append('<span id="bola'+data.billetesZona1[i].posicion+'" class="billete">'+data.billetesZona1[i].numero+'</span>');

				if(data.billetesZona1[i].posicion == 1){

					//codigo de sale o sale

					if(data.billetesZona1[i].vendido == 'SI')
						 $('#primer_premio_resumen').css('background-image', 'url(escribano_img/primer_premio_<?php echo $_SESSION['sorteo']; ?>.png)');
					else if(data.billetesZona1[i].vendido == 'NO')
						$('#primer_premio_resumen').css('background-image', 'url(escribano_img/no_vendido_<?php echo $_SESSION['sorteo']; ?>.png)');



					var progresion = (data.billetesZona1[i].numero % 11) + 1;
					progresion = progresion < 10 ? '0' + progresion : progresion;
					//20 primeros premios1
					$('#progresion').remove();
					$('#escribano_resumen').append('<span id="progresion">'+progresion+'</span>');

				}
			}

			//Extraordinarios

			for(var i=0;i<data.billetesZona2.length;i++){
				$('#contenido').append('<span id="bola'+data.billetesZona2[i].posicion+'" class="billete">'+data.billetesZona2[i].numero+'</span>');
				$('#contenido').append('<span id="fraccion'+data.billetesZona2[i].posicion+'">'+data.billetesZona2[i].fraccion+'</span>');

			}

			$('#contenido').append('<span id="bola27">'+data.billetesZona4[0].numero+'</span>');


		}
	</script>
</body>
</html>