<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN" "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
	<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
	<title>Sorteador Loteria de Cordoba S.E.</title>
	<link href="escribano_estilo.css" rel="stylesheet" type="text/css" />
	<script type="text/javascript" src="../../librerias/jquery/jquery-1.10.1.js"></script>
	<script type="text/javascript" src="../../js/funciones.js"></script>
</head>
<body>

	<div id="escribano_resumen"></div>
	<script type="text/javascript">
		//var buscar_informacion_sorteo;

		$(document).ready(			
			function(){
				buscar_informacion();
				//buscar_informacion_sorteo = setInterval(buscar_informacion, 1300);
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
			
			for(var i=0;i<data.billetesZona1.length;i++){
				$('#escribano_resumen').append('<span id="bola'+data.billetesZona1[i].posicion+'">'+data.billetesZona1[i].numero+'</span>');
				
				if(data.billetesZona1[i].posicion == 1){
					var progresion = (data.billetesZona1[i].numero % 11) + 1;
					progresion = progresion < 10 ? '0' + progresion : progresion;
					//20 primeros premios1
					$('#escribano_resumen').append('<span id="progresion">'+progresion+'</span>');
				}
			}

			//Extraordinarios

			for(var i=0;i<data.billetesZona2.length;i++){
				$('#escribano_resumen').append('<span id="bola'+data.billetesZona2[i].posicion+'">'+data.billetesZona2[i].numero+'</span>');
				$('#escribano_resumen').append('<span id="fraccion'+data.billetesZona2[i].posicion+'">'+data.billetesZona2[i].fraccion+'</span>');
			
			}
						
		}
	</script>
</body>
</html>